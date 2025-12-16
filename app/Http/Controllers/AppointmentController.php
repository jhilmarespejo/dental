<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Professional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

// controlador de citas médicas
class AppointmentController extends Controller
{
    // Agregar método para calendario de citas
    public function calendarEvents(Request $request)
    {
        $start = Carbon::parse($request->input('start'))->startOfDay();
        $end = Carbon::parse($request->input('end'))->endOfDay();
        
        $appointments = Appointment::with(['patient', 'professional'])
            ->whereBetween('fecha_hora', [$start, $end])
            ->get()
            ->map(function ($appointment) {
                $statusColors = [
                    'programada' => '#4e73df', // azul
                    'confirmada' => '#1cc88a', // verde
                    'completada' => '#36b9cc', // cyan
                    'cancelada' => '#e74a3b'   // rojo
                ];
                
                return [
                    'id' => $appointment->id,
                    'title' => $appointment->patient->nombres . ' - ' . $appointment->motivo,
                    'start' => $appointment->fecha_hora->toIso8601String(),
                    'end' => $appointment->fecha_hora->copy()->addMinutes($appointment->duracion)->toIso8601String(),
                    'color' => $statusColors[$appointment->estado] ?? '#858796',
                    'extendedProps' => [
                        'patient_id' => $appointment->patient->id,
                        'patient_name' => $appointment->patient->nombres . ' ' . $appointment->patient->apellidos,
                        'professional_name' => $appointment->professional->nombres . ' ' . $appointment->professional->apellidos,
                        'reason' => $appointment->motivo,
                        'status' => $this->translateStatus($appointment->estado),
                        'notes' => $appointment->notas,
                        'duration' => $appointment->duracion
                    ]
                ];
            });
        
        return response()->json($appointments);
    }

    // Agregar método para estadísticas
    public function statistics()
    {
        $total = Appointment::count();
        $completed = Appointment::where('estado', 'completada')->count();
        $pending = Appointment::where('estado', 'programada')->orWhere('estado', 'confirmada')->count();
        $cancelled = Appointment::where('estado', 'cancelada')->count();
        
        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $pending,
            'cancelled' => $cancelled
        ];
    }

    // Modificar el método index para incluir estadísticas
    public function index()
    {
        // Obtener citas de hoy
        $today = Appointment::with(['patient', 'professional'])
            ->whereDate('fecha_hora', now()->toDateString())
            ->orderBy('fecha_hora')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'time' => $appointment->fecha_hora->format('H:i'),
                    'patient' => $appointment->patient->nombres . ' ' . $appointment->patient->apellidos,
                    'patient_id' => $appointment->patient->id,
                    'reason' => $appointment->motivo,
                    'status' => $this->translateStatus($appointment->estado)
                ];
            });
            
        // Obtener próximas citas (excluyendo hoy)
        $upcoming = Appointment::with(['patient', 'professional'])
            ->whereDate('fecha_hora', '>', now()->toDateString())
            ->whereDate('fecha_hora', '<=', now()->addDays(7)->toDateString())
            ->orderBy('fecha_hora')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'date' => $appointment->fecha_hora->format('Y-m-d'),
                    'time' => $appointment->fecha_hora->format('H:i'),
                    'patient' => $appointment->patient->nombres . ' ' . $appointment->patient->apellidos,
                    'patient_id' => $appointment->patient->id,
                    'reason' => $appointment->motivo,
                    'status' => $this->translateStatus($appointment->estado)
                ];
            });
        
        // Obtener estadísticas
        $stats = $this->statistics();
            
        return view('appointments.index', compact('today', 'upcoming', 'stats'));
    }

    /**
     * Muestra el formulario para crear una nueva cita
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        // Obtener todos los pacientes para el selector
        $patients = Patient::select('id', 'nombres', 'apellidos')
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'name' => $patient->nombres . ' ' . $patient->apellidos
                ];
            });
            
        // Obtener todos los profesionales activos
        $professionals = Professional::where('estado', 'activo')
            ->select('id', 'nombres', 'apellidos', 'especialidad')
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get()
            ->map(function ($professional) {
                return [
                    'id' => $professional->id,
                    'name' => $professional->nombres . ' ' . $professional->apellidos,
                    'specialty' => $professional->especialidad
                ];
            });
            
        // Si se proporciona un ID de paciente en la URL, seleccionarlo
        $selectedPatientId = $request->input('patient_id');
        
        return view('appointments.create', compact('patients', 'professionals', 'selectedPatientId'));
    }

    /**
     * Almacena una nueva cita en la base de datos
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
public function store(Request $request)
{
    // Validación de datos
    $validator = Validator::make($request->all(), [
        'paciente_id' => 'required|exists:pacientes,id',
        'profesional_id' => 'required|exists:profesionales,id',
        'fecha' => 'required|date|after_or_equal:today',
        'hora' => 'required|date_format:H:i',
        'duracion' => 'required|integer|min:5|max:180',
        'motivo' => 'required|string|max:250',
        'notas' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    // Combinar fecha y hora
    $fechaHora = Carbon::parse($request->input('fecha') . ' ' . $request->input('hora'));
    
    // Asegurar que duracion sea entero
    $duracion = (int) $request->input('duracion');
    
    // Verificar disponibilidad del profesional
    $profesionalOcupado = Appointment::where('profesional_id', $request->input('profesional_id'))
        ->where(function ($query) use ($fechaHora, $duracion) {
            $inicio = $fechaHora->copy();
            $fin = $fechaHora->copy()->addMinutes($duracion);
            
            // Verificar solapamiento de horarios
            $query->where(function ($q) use ($inicio) {
                // Cita que comienza durante otra
                $q->where('fecha_hora', '<=', $inicio)
                  ->whereRaw("DATE_ADD(fecha_hora, INTERVAL duracion MINUTE) > ?", [$inicio]);
            })->orWhere(function ($q) use ($inicio, $fin) {
                // Cita que termina durante otra
                $q->where('fecha_hora', '<', $fin)
                  ->where('fecha_hora', '>=', $inicio);
            });
        })
        ->exists();
        
    if ($profesionalOcupado) {
        return redirect()->back()
            ->with('error', 'El profesional no está disponible en el horario seleccionado')
            ->withInput();
    }

    // Crear cita
    $appointment = Appointment::create([
        'paciente_id' => $request->input('paciente_id'),
        'profesional_id' => $request->input('profesional_id'),
        'fecha_hora' => $fechaHora,
        'duracion' => $duracion,
        'estado' => 'programada',
        'motivo' => $request->input('motivo'),
        'notas' => $request->input('notas'),
    ]);

    // Actualizar fecha de última visita del paciente (si la cita ya pasó)
    if ($fechaHora->isPast()) {
        $patient = Patient::find($request->input('paciente_id'));
        $patient->fecha_ultima_visita = $fechaHora->toDateString();
        $patient->save();
    }

    return redirect()->route('appointments.show', $appointment->id)
        ->with('success', 'Cita creada exitosamente');
}

    /**
     * Muestra los detalles de una cita específica
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $appointment = Appointment::with(['patient', 'professional'])->findOrFail($id);
        
        // Formatear para la vista
        $formattedAppointment = [
            'id' => $appointment->id,
            'date' => $appointment->fecha_hora->format('Y-m-d'),
            'time' => $appointment->fecha_hora->format('H:i'),
            'patient_id' => $appointment->patient->id,
            'patient_name' => $appointment->patient->nombres . ' ' . $appointment->patient->apellidos,
            'patient_phone' => $appointment->patient->celular,
            'patient_email' => $appointment->patient->email,
            'professional_id' => $appointment->professional->id,
            'professional_name' => $appointment->professional->nombres . ' ' . $appointment->professional->apellidos,
            'reason' => $appointment->motivo,
            'status' => $this->translateStatus($appointment->estado),
            'notes' => $appointment->notas,
            'created_at' => $appointment->created_at->format('Y-m-d H:i:s')
        ];
        
        // Obtener historial de citas anteriores del paciente
        $previousAppointments = Appointment::where('paciente_id', $appointment->paciente_id)
            ->where('id', '!=', $id)
            ->orderBy('fecha_hora', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($prevAppointment) {
                return [
                    'id' => $prevAppointment->id,
                    'date' => $prevAppointment->fecha_hora->format('Y-m-d'),
                    'time' => $prevAppointment->fecha_hora->format('H:i'),
                    'reason' => $prevAppointment->motivo,
                    'notes' => $prevAppointment->notas,
                    'status' => $this->translateStatus($prevAppointment->estado)
                ];
            });
            
        return view('appointments.show', compact('formattedAppointment', 'previousAppointments'));
    }

    /**
     * Muestra el formulario para editar una cita
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $appointment = Appointment::findOrFail($id);
        
        // Obtener todos los pacientes para el selector
        $patients = Patient::select('id', 'nombres', 'apellidos')
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'name' => $patient->nombres . ' ' . $patient->apellidos
                ];
            });
            
        // Obtener todos los profesionales activos
        $professionals = Professional::where('estado', 'activo')
            ->select('id', 'nombres', 'apellidos', 'especialidad')
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get()
            ->map(function ($professional) {
                return [
                    'id' => $professional->id,
                    'name' => $professional->nombres . ' ' . $professional->apellidos,
                    'specialty' => $professional->especialidad
                ];
            });
            
        return view('appointments.edit', compact('appointment', 'patients', 'professionals'));
    }

    /**
     * Actualiza los datos de una cita específica
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
public function update(Request $request, $id)
{
    // Validación de datos
    $validator = Validator::make($request->all(), [
        'paciente_id' => 'required|exists:pacientes,id',
        'profesional_id' => 'required|exists:profesionales,id',
        'fecha' => 'required|date',
        'hora' => 'required|date_format:H:i',
        'duracion' => 'required|integer|min:5|max:180',
        'motivo' => 'required|string|max:250',
        'estado' => 'required|in:programada,confirmada,completada,cancelada',
        'notas' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    // Preparar datos
    $fechaHora = Carbon::parse($request->fecha . ' ' . $request->hora);
    $duracion = (int) $request->duracion;
    
    // Obtener la cita
    $appointment = Appointment::findOrFail($id);
    
    // Verificar disponibilidad del profesional (excluyendo esta cita)
    $profesionalOcupado = Appointment::where('profesional_id', $request->profesional_id)
        ->where('id', '!=', $id)
        ->where('fecha_hora', '<', $fechaHora->copy()->addMinutes($duracion))
        ->whereRaw("DATE_ADD(fecha_hora, INTERVAL duracion MINUTE) > ?", [$fechaHora])
        ->exists();
        
    if ($profesionalOcupado) {
        return redirect()->back()
            ->with('error', 'El profesional no está disponible en el horario seleccionado')
            ->withInput();
    }

    // Actualizar la cita
    $appointment->update([
        'paciente_id' => $request->paciente_id,
        'profesional_id' => $request->profesional_id,
        'fecha_hora' => $fechaHora,
        'duracion' => $duracion,
        'estado' => $request->estado,
        'motivo' => $request->motivo,
        'notas' => $request->notas,
        // updated_at se actualizará automáticamente
    ]);

    // Actualizar fecha de última visita del paciente si la cita está completada
    if ($request->estado === 'completada') {
        Patient::where('id', $request->paciente_id)
            ->update(['fecha_ultima_visita' => $fechaHora->toDateString()]);
    }

    return redirect()->route('appointments.show', $appointment->id)
        ->with('success', 'Cita actualizada exitosamente');
}
    
    /**
     * Elimina una cita
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        
        // Verificar si tiene tratamientos asociados
        if ($appointment->treatments()->exists()) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar la cita porque tiene tratamientos asociados');
        }
        
        // Eliminar la cita
        $appointment->delete();
        
        return redirect()->route('appointments.index')
            ->with('success', 'Cita eliminada exitosamente');
    }
    
    /**
     * Cambiar el estado de una cita (AJAX)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'estado' => 'required|in:programada,confirmada,completada,cancelada',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Estado inválido'], 400);
        }
        
        $appointment = Appointment::findOrFail($id);
        $appointment->estado = $request->input('estado');
        $appointment->save();
        
        // Si la cita está completada, actualizar la fecha de última visita
        if ($request->input('estado') === 'completada') {
            $patient = Patient::find($appointment->paciente_id);
            
            // Solo actualizar si es más reciente que la fecha actual
            if (!$patient->fecha_ultima_visita || $appointment->fecha_hora->greaterThan($patient->fecha_ultima_visita)) {
                $patient->fecha_ultima_visita = $appointment->fecha_hora->toDateString();
                $patient->save();
            }
        }
        
        return response()->json([
            'success' => true, 
            'message' => 'Estado actualizado', 
            'status' => $this->translateStatus($request->input('estado'))
        ]);
    }
    
    /**
     * Traduce los estados de la base de datos a formato legible
     *
     * @param  string  $status
     * @return string
     */
    private function translateStatus($status)
    {
        $translations = [
            'programada' => 'Programada',
            'confirmada' => 'Confirmada',
            'completada' => 'Completada',
            'cancelada' => 'Cancelada'
        ];
        
        return $translations[$status] ?? $status;
    }


    /**
     * Obtener citas de un paciente para AJAX
     *
     * @param int $patientId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPatientAppointments($patientId)
    {
        $appointments = Appointment::where('paciente_id', $patientId)
            ->where('fecha_hora', '>=', now()->subMonths(3)) // Últimos 3 meses
            ->orderBy('fecha_hora', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'fecha_hora' => $appointment->fecha_hora->toIso8601String(),
                    'motivo' => $appointment->motivo,
                    'estado' => $appointment->estado,
                    'profesional_nombre' => $appointment->professional->nombres . ' ' . $appointment->professional->apellidos
                ];
            });
        
        return response()->json($appointments);
    }
}