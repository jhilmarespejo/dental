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
    /**
     * Muestra el listado de citas
     *
     * @return \Illuminate\View\View
     */
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
            
        return view('appointments.index', compact('today', 'upcoming'));
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
        
        // Verificar disponibilidad del profesional
        $profesionalOcupado = Appointment::where('profesional_id', $request->input('profesional_id'))
            ->where(function ($query) use ($fechaHora, $request) {
                $inicio = $fechaHora->copy();
                $fin = $fechaHora->copy()->addMinutes($request->input('duracion'));
                
                $query->where(function ($q) use ($inicio, $fin) {
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
            'duracion' => $request->input('duracion'),
            'estado' => 'programada',
            'motivo' => $request->input('motivo'),
            'notas' => $request->input('notas'),
        ]);

        // Actualizar fecha de última visita del paciente (si es en el futuro, no actualizar)
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

        // Combinar fecha y hora
        $fechaHora = Carbon::parse($request->input('fecha') . ' ' . $request->input('hora'));
        
        // Obtener la cita
        $appointment = Appointment::findOrFail($id);
        
        // Verificar disponibilidad del profesional (excluyendo la cita actual)
        $profesionalOcupado = Appointment::where('profesional_id', $request->input('profesional_id'))
            ->where('id', '!=', $id)
            ->where(function ($query) use ($fechaHora, $request) {
                $inicio = $fechaHora->copy();
                $fin = $fechaHora->copy()->addMinutes($request->input('duracion'));
                
                $query->where(function ($q) use ($inicio, $fin) {
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

        // Actualizar la cita
        $appointment->update([
            'paciente_id' => $request->input('paciente_id'),
            'profesional_id' => $request->input('profesional_id'),
            'fecha_hora' => $fechaHora,
            'duracion' => $request->input('duracion'),
            'estado' => $request->input('estado'),
            'motivo' => $request->input('motivo'),
            'notas' => $request->input('notas'),
        ]);

        // Actualizar fecha de última visita del paciente si la cita está completada
        if ($request->input('estado') === 'completada') {
            $patient = Patient::find($request->input('paciente_id'));
            
            // Solo actualizar si es más reciente que la fecha actual
            if (!$patient->fecha_ultima_visita || $fechaHora->greaterThan($patient->fecha_ultima_visita)) {
                $patient->fecha_ultima_visita = $fechaHora->toDateString();
                $patient->save();
            }
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
}