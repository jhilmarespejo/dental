<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Appointment;
use App\Models\TreatmentPerformed;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Muestra la página del dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Estadísticas para tarjetas de resumen
        $stats = [
            'total_patients' => Patient::count(),
            'appointments_today' => Appointment::whereDate('fecha_hora', now()->toDateString())->count(),
            'active_treatments' => TreatmentPerformed::whereRaw('costo > (SELECT COALESCE(SUM(monto), 0) FROM pagos WHERE pagos.tratamiento_id = tratamientos_realizados.id)')->count(),
            'revenue_month' => Payment::whereRaw('MONTH(fecha) = MONTH(CURRENT_DATE()) AND YEAR(fecha) = YEAR(CURRENT_DATE())')->sum('monto'),
            'pending_payments' => DB::table('v_saldos_pendientes')->sum('saldo_pendiente'),
        ];
        
        // Pacientes recientes con tratamientos
        $recentPatients = TreatmentPerformed::with(['patient', 'treatment'])
            ->orderBy('fecha', 'desc')
            ->limit(5)
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
                    'id' => $treatment->patient->id,
                    'name' => $treatment->patient->nombres . ' ' . $treatment->patient->apellidos,
                    'date' => \Carbon\Carbon::parse($treatment->fecha)->format('Y-m-d'),
                    'treatment' => $treatment->treatment 
                                  ? $treatment->treatment->nombre 
                                  : $treatment->tratamiento_otro,
                    'status' => $status
                ];
            });
            
        // Citas para hoy
        $todayAppointments = Appointment::with(['patient', 'professional'])
            ->whereDate('fecha_hora', now()->toDateString())
            ->orderBy('fecha_hora')
            ->get()
            ->map(function ($appointment) {
                return [
                    'time' => Carbon::parse($appointment->fecha_hora)->format('H:i'), // Convertimos a Carbon antes de formatear
                    'patient' => $appointment->patient->nombres . ' ' . $appointment->patient->apellidos,
                    'reason' => $appointment->motivo
                ];
            });
            
        // Datos para gráfico de ingresos mensuales
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Payment::whereYear('fecha', $month->year)
                            ->whereMonth('fecha', $month->month)
                            ->sum('monto');
                            
            $monthlyRevenue[] = [
                'month' => $month->format('M'),
                'revenue' => $revenue
            ];
        }
        
        // Datos para gráfico de tipos de tratamientos
        $treatmentTypes = TreatmentPerformed::select('tratamiento_id', DB::raw('count(*) as total'))
            ->whereNotNull('tratamiento_id')
            ->groupBy('tratamiento_id')
            ->orderBy('total', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($item) {
                $treatment = DB::table('tratamientos')->where('id', $item->tratamiento_id)->first();
                return [
                    'name' => $treatment ? $treatment->nombre : 'Otro',
                    'count' => $item->total
                ];
            });
            
        // Si hay menos de 6 tipos, agregar "Otros" con el resto
        $topTreatmentsCount = $treatmentTypes->sum('count');
        $totalTreatments = TreatmentPerformed::count();
        
        if ($topTreatmentsCount < $totalTreatments) {
            $treatmentTypes->push([
                'name' => 'Otros',
                'count' => $totalTreatments - $topTreatmentsCount
            ]);
        }
        
        return view('dashboard', compact('stats', 'recentPatients', 'todayAppointments', 'monthlyRevenue', 'treatmentTypes'));
    }
}