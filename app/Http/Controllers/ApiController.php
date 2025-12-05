<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Professional;
use App\Models\Appointment;
use App\Models\Treatment;
use App\Models\Diagnosis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApiController extends Controller
{
    /**
     * Obtener lista de pacientes para selects y autocompletar
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPatients(Request $request)
    {
        $search = $request->input('q');
        $limit = $request->input('limit', 50);
        
        $query = Patient::select('id', 'nombres', 'apellidos', 'edad', 'celular', 'email');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombres', 'like', "%{$search}%")
                  ->orWhere('apellidos', 'like', "%{$search}%")
                  ->orWhere('ci', 'like', "%{$search}%")
                  ->orWhere('celular', 'like', "%{$search}%");
            });
        }
        
        $patients = $query->orderBy('apellidos')
                         ->orderBy('nombres')
                         ->limit($limit)
                         ->get()
                         ->map(function ($patient) {
                             return [
                                 'id' => $patient->id,
                                 'nombre_completo' => $patient->apellidos . ', ' . $patient->nombres,
                                 'edad' => $patient->edad,
                                 'telefono' => $patient->celular,
                                 'email' => $patient->email
                             ];
                         });
        
        return response()->json($patients);
    }
    
    /**
     * Obtener lista de profesionales para selects y autocompletar
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfessionals(Request $request)
    {
        $search = $request->input('q');
        $limit = $request->input('limit', 50);
        $onlyActive = $request->input('only_active', true);
        
        $query = Professional::select('id', 'nombres', 'apellidos', 'especialidad', 'telefono', 'email');
        
        if ($onlyActive) {
            $query->where('estado', 'activo');
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombres', 'like', "%{$search}%")
                  ->orWhere('apellidos', 'like', "%{$search}%")
                  ->orWhere('especialidad', 'like', "%{$search}%")
                  ->orWhere('ci', 'like', "%{$search}%");
            });
        }
        
        $professionals = $query->orderBy('apellidos')
                         ->orderBy('nombres')
                         ->limit($limit)
                         ->get()
                         ->map(function ($prof) {
                             return [
                                 'id' => $prof->id,
                                 'nombre_completo' => $prof->apellidos . ', ' . $prof->nombres,
                                 'especialidad' => $prof->especialidad,
                                 'telefono' => $prof->telefono,
                                 'email' => $prof->email
                             ];
                         });
        
        return response()->json($professionals);
    }
    
    /**
     * Obtener lista de diagnósticos para selects y autocompletar
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDiagnoses(Request $request)
    {
        $search = $request->input('q');
        $limit = $request->input('limit', 50);
        
        $query = Diagnosis::select('id', 'nombre', 'descripcion');
        
        if ($search) {
            $query->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
        }
        
        $diagnoses = $query->orderBy('nombre')
                         ->limit($limit)
                         ->get()
                         ->map(function ($item) {
                             return [
                                 'id' => $item->id,
                                 'nombre' => $item->nombre,
                                 'descripcion' => $item->descripcion
                             ];
                         });
        
        return response()->json($diagnoses);
    }
    
    /**
     * Obtener lista de tratamientos para selects y autocompletar
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTreatments(Request $request)
    {
        $search = $request->input('q');
        $limit = $request->input('limit', 50);
        
        $query = Treatment::select('id', 'nombre', 'descripcion', 'costo_sugerido');
        
        if ($search) {
            $query->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
        }
        
        $treatments = $query->orderBy('nombre')
                         ->limit($limit)
                         ->get()
                         ->map(function ($item) {
                             return [
                                 'id' => $item->id,
                                 'nombre' => $item->nombre,
                                 'descripcion' => $item->descripcion,
                                 'costo_sugerido' => $item->costo_sugerido
                             ];
                         });
        
        return response()->json($treatments);
    }
    
    /**
     * Obtener notificaciones para el panel de control
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotifications(Request $request)
    {
        $notifications = [];
        
        // Obtener citas para hoy
        $appointmentsToday = Appointment::with(['patient'])
            ->whereDate('fecha_hora', Carbon::today())
            ->where('estado', 'programada')
            ->orderBy('fecha_hora')
            ->get();
            
        foreach ($appointmentsToday as $appointment) {
            $notifications[] = [
                'id' => 'appointment_' . $appointment->id,
                'type' => 'appointment',
                'message' => 'Cita con ' . $appointment->patient->nombres . ' ' . $appointment->patient->apellidos . ' a las ' . Carbon::parse($appointment->fecha_hora)->format('H:i'),
                'time' => Carbon::parse($appointment->fecha_hora)->diffForHumans(),
                'url' => route('appointments.show', $appointment->id)
            ];
        }
        
        // Obtener pagos pendientes
        $pendingPayments = DB::table('v_saldos_pendientes')
            ->whereRaw('saldo_pendiente >= 500') // Solo mostrar saldos importantes
            ->orderBy('saldo_pendiente', 'desc')
            ->limit(5)
            ->get();
            
        foreach ($pendingPayments as $payment) {
            $notifications[] = [
                'id' => 'payment_' . $payment->tratamiento_id,
                'type' => 'payment',
                'message' => 'Pago pendiente de ' . $payment->paciente . ' por Bs. ' . number_format($payment->saldo_pendiente, 2),
                'time' => Carbon::parse($payment->fecha)->diffForHumans(),
                'url' => route('treatments.show', $payment->tratamiento_id)
            ];
        }
        
        // Ordenar notificaciones por tiempo (más recientes primero)
        usort($notifications, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });
        
        // Limitar a 5 notificaciones
        $notifications = array_slice($notifications, 0, 5);
        
        return response()->json($notifications);
    }
    
    /**
     * Verificar disponibilidad de horarios para citas
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAvailability(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'profesional_id' => 'required|exists:profesionales,id',
            'fecha' => 'required|date_format:Y-m-d',
            'hora_inicio' => 'required|date_format:H:i',
            'duracion' => 'required|integer|min:15|max:180',
            'appointment_id' => 'nullable|integer' // Para excluir en edición
        ]);

        if ($validator->fails()) {
            return response()->json([
                'available' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $profesionalId = $request->input('profesional_id');
        $fecha = $request->input('fecha');
        $horaInicio = $request->input('hora_inicio');
        $duracion = $request->input('duracion');
        $appointmentId = $request->input('appointment_id');
        
        $inicio = Carbon::parse($fecha . ' ' . $horaInicio);
        $fin = $inicio->copy()->addMinutes($duracion);
        
        // Verificar si hay conflictos con otras citas
        $query = Appointment::where('profesional_id', $profesionalId)
            ->where(function ($q) use ($inicio, $fin) {
                // Cita que comienza durante otra
                $q->where(function ($sq) use ($inicio, $fin) {
                    $sq->where('fecha_hora', '<=', $inicio)
                       ->whereRaw("DATE_ADD(fecha_hora, INTERVAL duracion MINUTE) > ?", [$inicio]);
                })
                // O cita que termina durante otra
                ->orWhere(function ($sq) use ($inicio, $fin) {
                    $sq->where('fecha_hora', '<', $fin)
                       ->where('fecha_hora', '>=', $inicio);
                });
            });
            
        // Excluir la cita actual si estamos editando
        if ($appointmentId) {
            $query->where('id', '!=', $appointmentId);
        }
        
        $conflicts = $query->get();
        
        if ($conflicts->count() > 0) {
            return response()->json([
                'available' => false,
                'message' => 'El profesional ya tiene una cita programada en este horario',
                'conflicts' => $conflicts->map(function ($c) {
                    return [
                        'id' => $c->id,
                        'start' => Carbon::parse($c->fecha_hora)->format('H:i'),
                        'end' => Carbon::parse($c->fecha_hora)->addMinutes($c->duracion)->format('H:i')
                    ];
                })
            ]);
        }
        
        // Verificar el horario laboral (asumiendo 8:00 a 20:00)
        $startTime = Carbon::parse($fecha . ' 08:00:00');
        $endTime = Carbon::parse($fecha . ' 20:00:00');
        
        if ($inicio < $startTime || $fin > $endTime) {
            return response()->json([
                'available' => false,
                'message' => 'El horario seleccionado está fuera del horario laboral (8:00 - 20:00)'
            ]);
        }
        
        return response()->json([
            'available' => true,
            'message' => 'Horario disponible'
        ]);
    }
}