<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\TreatmentPerformed;
use App\Models\Appointment;
use App\Models\Professional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PatientController extends Controller
{
    /**
     * Muestra el listado de pacientes
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Filtros para búsqueda
        $search = $request->input('search');
        
        $query = Patient::query();
        
        // Aplicar filtros si existen
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombres', 'like', "%{$search}%")
                  ->orWhere('apellidos', 'like', "%{$search}%")
                  ->orWhere('ci', 'like', "%{$search}%")
                  ->orWhere('celular', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Obtener pacientes con paginación
        $patients = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Estadísticas para gráficos
        $ageGroups = [
            '0-18' => Patient::whereRaw('edad <= 18')->count(),
            '19-35' => Patient::whereRaw('edad > 18 AND edad <= 35')->count(),
            '36-50' => Patient::whereRaw('edad > 35 AND edad <= 50')->count(),
            '51-65' => Patient::whereRaw('edad > 50 AND edad <= 65')->count(),
            '65+' => Patient::whereRaw('edad > 65')->count(),
        ];
        
        // Conteos para resumen
        $totalPatients = Patient::count();
        $newPatientsThisMonth = Patient::whereRaw('MONTH(created_at) = MONTH(CURRENT_DATE())')->count();
        $activeAppointments = Appointment::where('estado', '!=', 'cancelada')->count();
        $pendingTreatments = TreatmentPerformed::whereRaw('costo > (SELECT COALESCE(SUM(monto), 0) FROM pagos WHERE pagos.tratamiento_id = tratamientos_realizados.id)')->count();
        
        $stats = [
            'total_patients' => $totalPatients,
            'new_patients_month' => $newPatientsThisMonth,
            'active_appointments' => $activeAppointments,
            'pending_treatments' => $pendingTreatments
        ];
        
        return view('patients.index', compact('patients', 'ageGroups', 'stats', 'search'));
    }

    /**
     * Muestra el formulario para crear un nuevo paciente
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Almacena un nuevo paciente en la base de datos
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:M,F,Otro',
            'celular' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:100',
            'ci' => 'nullable|string|max:12',
            'ci_exp' => 'nullable|string|max:5',
            'direccion' => 'nullable|string|max:500',
            'alergias' => 'nullable|string',
            'condiciones_medicas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Crear paciente
        $patient = Patient::create($request->all());

        return redirect()->route('patients.show', $patient->id)
            ->with('success', 'Paciente creado exitosamente');
    }

    /**
     * Muestra los detalles de un paciente específico
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $patient = Patient::findOrFail($id);
        
        // Obtener tratamientos con diagnóstico y pagos
        $treatments = TreatmentPerformed::with(['diagnosis', 'treatment', 'payments'])
            ->where('paciente_id', $id)
            ->orderBy('fecha', 'desc')
            ->get()
            ->map(function ($treatment) {
                // Calcular saldo pendiente
                $paid = $treatment->payments->sum('monto');
                $balance = $treatment->costo - $paid;
                
                // Determinar estado del tratamiento
                $status = 'Pendiente';
                if ($balance <= 0) {
                    $status = 'Completado';
                } elseif ($paid > 0) {
                    $status = 'En proceso';
                }
                
                return [
                    'id' => $treatment->id,
                    'date' => $treatment->fecha,
                    'diagnosis' => $treatment->diagnosis ? $treatment->diagnosis->nombre : $treatment->diagnostico_otro,
                    'treatment' => $treatment->treatment ? $treatment->treatment->nombre : $treatment->tratamiento_otro,
                    'tooth' => $treatment->pieza_dental ?? 'General',
                    'cost' => $treatment->costo,
                    'paid' => $paid,
                    'balance' => $balance,
                    'status' => $status
                ];
            })->toArray();
        
        // Obtener próximas citas
        $upcomingAppointments = Appointment::where('paciente_id', $id)
            ->where('fecha_hora', '>=', now())
            ->where('estado', '!=', 'cancelada')
            ->orderBy('fecha_hora', 'asc')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'date' => Carbon::parse($appointment->fecha_hora)->format('Y-m-d'),
                    'time' => Carbon::parse($appointment->fecha_hora)->format('H:i'),
                    'reason' => $appointment->motivo
                ];
            });
        
        return view('patients.show', compact('patient', 'treatments', 'upcomingAppointments'));
    }

    /**
     * Muestra el formulario para editar un paciente
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        return view('patients.edit', compact('patient'));
    }

    /**
     * Actualiza los datos de un paciente específico
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:M,F,Otro',
            'celular' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:100',
            'ci' => 'nullable|string|max:12',
            'ci_exp' => 'nullable|string|max:5',
            'direccion' => 'nullable|string|max:500',
            'alergias' => 'nullable|string',
            'condiciones_medicas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Actualizar paciente
        $patient = Patient::findOrFail($id);
        $patient->update($request->all());

        return redirect()->route('patients.show', $patient->id)
            ->with('success', 'Datos del paciente actualizados exitosamente');
    }

    /**
     * Elimina un paciente (soft delete)
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Verificar si tiene tratamientos o citas asociadas
        $patient = Patient::findOrFail($id);
        $hasAppointments = Appointment::where('paciente_id', $id)->exists();
        $hasTreatments = TreatmentPerformed::where('paciente_id', $id)->exists();
        
        if ($hasAppointments || $hasTreatments) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el paciente porque tiene citas o tratamientos asociados');
        }
        
        // Eliminar paciente
        $patient->delete();
        
        return redirect()->route('patients.index')
            ->with('success', 'Paciente eliminado exitosamente');
    }

    /**
     * Búsqueda AJAX de pacientes
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $search = $request->input('q');
        
        $patients = Patient::where('nombres', 'like', "%{$search}%")
            ->orWhere('apellidos', 'like', "%{$search}%")
            ->orWhere('ci', 'like', "%{$search}%")
            ->orWhere('celular', 'like', "%{$search}%")
            ->limit(5)
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'name' => $patient->nombres . ' ' . $patient->apellidos,
                    'age' => $patient->edad,
                    'phone' => $patient->celular,
                    'last_visit' => $patient->fecha_ultima_visita ? $patient->fecha_ultima_visita->format('d/m/Y') : 'Nunca'
                ];
            });
            
        return response()->json($patients);
    }
}