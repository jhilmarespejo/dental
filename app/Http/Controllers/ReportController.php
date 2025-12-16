<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\TreatmentPerformed;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Muestra la página principal de reportes
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('reports.index');
    }
    
    /**
     * Genera un reporte de ingresos
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function revenue(Request $request)
    {
        // Filtros para el reporte
        $period = $request->input('period', 'monthly');
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));
        $dateStart = $request->input('date_start', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateEnd = $request->input('date_end', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        // Preparar datos según el período seleccionado
        $labels = [];
        $revenueData = [];
        $treatmentsData = [];
        
        if ($period === 'daily') {
            // Reporte diario (por mes)
            $daysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;
            
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::createFromDate($year, $month, $day);
                $labels[] = $day;
                
                // Obtener pagos para este día
                $revenue = Payment::whereDate('fecha', $date->format('Y-m-d'))->sum('monto');
                $revenueData[] = $revenue;
                
                // Obtener tratamientos para este día
                $treatments = TreatmentPerformed::whereDate('fecha', $date->format('Y-m-d'))->count();
                $treatmentsData[] = $treatments;
            }
            
            $period_name = Carbon::createFromDate($year, $month, 1)->format('F Y');
        } elseif ($period === 'monthly') {
            // Reporte mensual (por año)
            for ($month = 1; $month <= 12; $month++) {
                $date = Carbon::createFromDate($year, $month, 1);
                $labels[] = $date->format('M');
                
                // Obtener pagos para este mes
                $revenue = Payment::whereYear('fecha', $year)
                                ->whereMonth('fecha', $month)
                                ->sum('monto');
                $revenueData[] = $revenue;
                
                // Obtener tratamientos para este mes
                $treatments = TreatmentPerformed::whereYear('fecha', $year)
                                   ->whereMonth('fecha', $month)
                                   ->count();
                $treatmentsData[] = $treatments;
            }
            
            $period_name = $year;
        } else {
            // Reporte por rango de fechas
            $start = Carbon::parse($dateStart);
            $end = Carbon::parse($dateEnd);
            $diff = $end->diffInDays($start);
            
            // Si el rango es grande, agrupar por semanas o meses
            if ($diff > 60) {
                // Agrupar por meses
                $currentDate = $start->copy()->startOfMonth();
                while ($currentDate <= $end) {
                    $labels[] = $currentDate->format('M Y');
                    
                    $monthStart = $currentDate->copy()->startOfMonth();
                    $monthEnd = $currentDate->copy()->endOfMonth();
                    
                    // Obtener pagos para este mes
                    $revenue = Payment::whereBetween('fecha', [$monthStart, $monthEnd])->sum('monto');
                    $revenueData[] = $revenue;
                    
                    // Obtener tratamientos para este mes
                    $treatments = TreatmentPerformed::whereBetween('fecha', [$monthStart, $monthEnd])->count();
                    $treatmentsData[] = $treatments;
                    
                    $currentDate->addMonth();
                }
            } elseif ($diff > 14) {
                // Agrupar por semanas
                $currentDate = $start->copy()->startOfWeek();
                while ($currentDate <= $end) {
                    $weekEnd = $currentDate->copy()->endOfWeek();
                    $labels[] = $currentDate->format('d/m') . ' - ' . $weekEnd->format('d/m');
                    
                    // Obtener pagos para esta semana
                    $revenue = Payment::whereBetween('fecha', [$currentDate, $weekEnd])->sum('monto');
                    $revenueData[] = $revenue;
                    
                    // Obtener tratamientos para esta semana
                    $treatments = TreatmentPerformed::whereBetween('fecha', [$currentDate, $weekEnd])->count();
                    $treatmentsData[] = $treatments;
                    
                    $currentDate->addWeek();
                }
            } else {
                // Agrupar por días
                $currentDate = $start->copy();
                while ($currentDate <= $end) {
                    $labels[] = $currentDate->format('d/m');
                    
                    // Obtener pagos para este día
                    $revenue = Payment::whereDate('fecha', $currentDate->format('Y-m-d'))->sum('monto');
                    $revenueData[] = $revenue;
                    
                    // Obtener tratamientos para este día
                    $treatments = TreatmentPerformed::whereDate('fecha', $currentDate->format('Y-m-d'))->count();
                    $treatmentsData[] = $treatments;
                    
                    $currentDate->addDay();
                }
            }
            
            $period_name = $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
        }
        
        // Resumen
        $summary = [
            'total_revenue' => array_sum($revenueData),
            'total_treatments' => array_sum($treatmentsData),
            'avg_revenue' => count($revenueData) > 0 ? array_sum($revenueData) / count($revenueData) : 0,
            'avg_treatments' => count($treatmentsData) > 0 ? array_sum($treatmentsData) / count($treatmentsData) : 0
        ];
        
        // Mejores métodos de pago
        $paymentMethods = Payment::select('metodo_pago', DB::raw('SUM(monto) as total'))
                               ->groupBy('metodo_pago')
                               ->orderBy('total', 'desc')
                               ->get();
                               
        // Mejores tratamientos por ingresos
        $topTreatments = DB::table('tratamientos_realizados')
                          ->select('tratamiento_id', 'tratamiento_otro', DB::raw('SUM(costo) as total_cost'), DB::raw('COUNT(*) as count'))
                          ->groupBy('tratamiento_id', 'tratamiento_otro')
                          ->orderBy('total_cost', 'desc')
                          ->limit(5)
                          ->get()
                          ->map(function ($item) {
                              $treatmentName = $item->tratamiento_id 
                                  ? DB::table('tratamientos')->where('id', $item->tratamiento_id)->value('nombre') 
                                  : $item->tratamiento_otro;
                                  
                              return [
                                  'name' => $treatmentName ?? 'Otro',
                                  'count' => $item->count,
                                  'total' => $item->total_cost
                              ];
                          });
                          
        return view('reports.revenue', compact(
            'period', 'year', 'month', 'dateStart', 'dateEnd', 
            'labels', 'revenueData', 'treatmentsData', 
            'period_name', 'summary', 'paymentMethods', 'topTreatments'
        ));
    }
    
    /**
     * Genera un reporte de tratamientos
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function treatments(Request $request)
    {
        // Filtros para el reporte
        $period = $request->input('period', 'yearly');
        $year = $request->input('year', date('Y'));
        $dateStart = $request->input('date_start', Carbon::now()->startOfYear()->format('Y-m-d'));
        $dateEnd = $request->input('date_end', Carbon::now()->endOfYear()->format('Y-m-d'));
        
        // Tratamientos por tipo
        $treatmentsByType = DB::table('tratamientos_realizados')
                            ->leftJoin('tratamientos', 'tratamientos_realizados.tratamiento_id', '=', 'tratamientos.id')
                            ->select(
                                DB::raw('COALESCE(tratamientos.nombre, tratamientos_realizados.tratamiento_otro, "Otro") as nombre'),
                                DB::raw('COUNT(*) as total')
                            )
                            ->groupBy(DB::raw('COALESCE(tratamientos.nombre, tratamientos_realizados.tratamiento_otro, "Otro")'))
                            ->orderBy('total', 'desc')
                            ->get();
                            
        // Tratamientos por profesional
        $treatmentsByProfessional = DB::table('tratamientos_realizados')
                                   ->join('profesionales', 'tratamientos_realizados.profesional_id', '=', 'profesionales.id')
                                   ->select(
                                       DB::raw('CONCAT(profesionales.nombres, " ", profesionales.apellidos) as nombre'),
                                       DB::raw('COUNT(*) as total')
                                   )
                                   ->groupBy('profesionales.id', 'profesionales.nombres', 'profesionales.apellidos')
                                   ->orderBy('total', 'desc')
                                   ->get();
                                   
        // Tratamientos por mes (últimos 12 meses)
        $treatmentsByMonth = [];
        $labels = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $count = TreatmentPerformed::whereYear('fecha', $date->year)
                                     ->whereMonth('fecha', $date->month)
                                     ->count();
                                     
            $treatmentsByMonth[] = $count;
        }
        
        // Diagnósticos más comunes
        $commonDiagnoses = DB::table('tratamientos_realizados')
                          ->leftJoin('diagnosticos', 'tratamientos_realizados.diagnostico_id', '=', 'diagnosticos.id')
                          ->select(
                              DB::raw('COALESCE(diagnosticos.nombre, tratamientos_realizados.diagnostico_otro, "Otro") as nombre'),
                              DB::raw('COUNT(*) as total')
                          )
                          ->groupBy(DB::raw('COALESCE(diagnosticos.nombre, tratamientos_realizados.diagnostico_otro, "Otro")'))
                          ->orderBy('total', 'desc')
                          ->limit(10)
                          ->get();
                          
        // Resumen
        $totalTreatments = TreatmentPerformed::count();
        $totalRevenue = TreatmentPerformed::sum('costo');
        $avgTreatmentCost = $totalTreatments > 0 ? ($totalRevenue / $totalTreatments) : 0;
                          
        return view('reports.treatments', compact(
            'period', 'year', 'dateStart', 'dateEnd',
            'treatmentsByType', 'treatmentsByProfessional', 
            'labels', 'treatmentsByMonth', 'commonDiagnoses',
            'totalTreatments', 'totalRevenue', 'avgTreatmentCost'
        ));
    }
    
    /**
     * Genera un reporte de pacientes
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function patients(Request $request)
    {
        // Filtros para el reporte
        $period = $request->input('period', 'yearly');
        $year = $request->input('year', date('Y'));
        
        // Pacientes por edad
        $patientsByAge = [
            '0-18' => Patient::whereRaw('edad <= 18')->count(),
            '19-35' => Patient::whereRaw('edad > 18 AND edad <= 35')->count(),
            '36-50' => Patient::whereRaw('edad > 35 AND edad <= 50')->count(),
            '51-65' => Patient::whereRaw('edad > 50 AND edad <= 65')->count(),
            '65+' => Patient::whereRaw('edad > 65')->count(),
        ];
        
        // Pacientes por género
        $patientsByGender = Patient::select('genero', DB::raw('COUNT(*) as total'))
                                ->groupBy('genero')
                                ->get()
                                ->mapWithKeys(function ($item) {
                                    $gender = $item->genero;
                                    if ($gender === 'M') $gender = 'Masculino';
                                    elseif ($gender === 'F') $gender = 'Femenino';
                                    elseif ($gender === 'Otro') $gender = 'Otro';
                                    else $gender = 'No especificado';
                                    
                                    return [$gender => $item->total];
                                });
                                
        // Nuevos pacientes por mes (últimos 12 meses)
        $newPatientsByMonth = [];
        $labels = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $count = Patient::whereYear('created_at', $date->year)
                           ->whereMonth('created_at', $date->month)
                           ->count();
                           
            $newPatientsByMonth[] = $count;
        }
        
        // Pacientes más frecuentes (con más tratamientos)
        $frequentPatients = DB::table('tratamientos_realizados')
                           ->join('pacientes', 'tratamientos_realizados.paciente_id', '=', 'pacientes.id')
                           ->select(
                               'pacientes.id',
                               DB::raw('CONCAT(pacientes.nombres, " ", pacientes.apellidos) as nombre'),
                               'pacientes.edad',
                               DB::raw('COUNT(*) as tratamientos'),
                               DB::raw('SUM(tratamientos_realizados.costo) as total_gastado')
                           )
                           ->groupBy('pacientes.id', 'pacientes.nombres', 'pacientes.apellidos', 'pacientes.edad')
                           ->orderBy('tratamientos', 'desc')
                           ->limit(10)
                           ->get();
        
        // Pacientes que más gastan
        $topSpendingPatients = DB::table('tratamientos_realizados')
                              ->join('pacientes', 'tratamientos_realizados.paciente_id', '=', 'pacientes.id')
                              ->select(
                                  'pacientes.id',
                                  DB::raw('CONCAT(pacientes.nombres, " ", pacientes.apellidos) as nombre'),
                                  'pacientes.edad',
                                  DB::raw('COUNT(*) as tratamientos'),
                                  DB::raw('SUM(tratamientos_realizados.costo) as total_gastado')
                              )
                              ->groupBy('pacientes.id', 'pacientes.nombres', 'pacientes.apellidos', 'pacientes.edad')
                              ->orderBy('total_gastado', 'desc')
                              ->limit(10)
                              ->get();
                              
        // Resumen
        $totalPatients = Patient::count();
        $newPatientsThisYear = Patient::whereYear('created_at', date('Y'))->count();
        $newPatientsLastYear = Patient::whereYear('created_at', date('Y')-1)->count();
        $activePatients = Patient::whereNotNull('fecha_ultima_visita')
                                ->whereRaw('DATEDIFF(CURRENT_DATE, fecha_ultima_visita) <= 365')
                                ->count();
                                
        return view('reports.patients', compact(
            'period', 'year',
            'patientsByAge', 'patientsByGender',
            'labels', 'newPatientsByMonth',
            'frequentPatients', 'topSpendingPatients',
            'totalPatients', 'newPatientsThisYear', 'newPatientsLastYear', 'activePatients'
        ));
    }
    
    /**
     * Exporta datos a diferentes formatos
     *
     * @param string $type
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function export($type, Request $request)
    {
        $format = $request->input('format', 'csv');
        
        if ($type === 'patients') {
            return $this->exportPatients($format);
        } elseif ($type === 'treatments') {
            return $this->exportTreatments($format);
        } elseif ($type === 'payments') {
            return $this->exportPayments($format);
        } elseif ($type === 'appointments') {
            return $this->exportAppointments($format);
        }
        
        return response('Tipo de exportación no válido', 400);
    }
    
    /**
     * Exporta la lista de pacientes
     *
     * @param string $format
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function exportPatients($format)
    {
        $patients = Patient::select(
            'id', 'nombres', 'apellidos', 'fecha_nacimiento', 'edad', 'genero',
            'celular', 'email', 'ci', 'ci_exp', 'direccion', 'alergias',
            'condiciones_medicas', 'fecha_ultima_visita', 'created_at'
        )->orderBy('apellidos')->orderBy('nombres')->get();
        
        $filename = 'pacientes_' . date('Y-m-d') . '.' . $format;
        $headers = [
            'ID', 'Nombres', 'Apellidos', 'Fecha Nacimiento', 'Edad', 'Género',
            'Celular', 'Email', 'CI', 'Expedido', 'Dirección', 'Alergias',
            'Condiciones Médicas', 'Última Visita', 'Fecha Registro'
        ];
        
        return $this->generateExport($patients, $headers, $filename, $format);
    }
    
    /**
     * Exporta la lista de tratamientos
     *
     * @param string $format
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function exportTreatments($format)
    {
        $treatments = TreatmentPerformed::with(['patient', 'professional', 'diagnosis', 'treatment'])
            ->select('id', 'paciente_id', 'profesional_id', 'fecha', 'diagnostico_id', 'diagnostico_otro',
                    'tratamiento_id', 'tratamiento_otro', 'pieza_dental', 'costo', 'observaciones', 'created_at')
            ->orderBy('fecha', 'desc')
            ->get()
            ->map(function ($treatment) {
                return [
                    'id' => $treatment->id,
                    'paciente' => optional($treatment->patient)->nombres . ' ' . optional($treatment->patient)->apellidos,
                    'profesional' => optional($treatment->professional)->nombres . ' ' . optional($treatment->professional)->apellidos,
                    'fecha' => $treatment->fecha->format('Y-m-d'),
                    'diagnostico' => optional($treatment->diagnosis)->nombre ?? $treatment->diagnostico_otro,
                    'tratamiento' => optional($treatment->treatment)->nombre ?? $treatment->tratamiento_otro,
                    'pieza_dental' => $treatment->pieza_dental,
                    'costo' => $treatment->costo,
                    'observaciones' => $treatment->observaciones,
                    'fecha_registro' => $treatment->created_at->format('Y-m-d H:i:s')
                ];
            });
        
        $filename = 'tratamientos_' . date('Y-m-d') . '.' . $format;
        $headers = [
            'ID', 'Paciente', 'Profesional', 'Fecha', 'Diagnóstico', 'Tratamiento',
            'Pieza Dental', 'Costo', 'Observaciones', 'Fecha Registro'
        ];
        
        return $this->generateExport($treatments, $headers, $filename, $format);
    }
    
    /**
     * Exporta la lista de pagos
     *
     * @param string $format
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function exportPayments($format)
    {
        $payments = Payment::with(['treatmentPerformed.patient'])
            ->select('id', 'tratamiento_id', 'fecha', 'monto', 'metodo_pago', 'comprobante', 'notas', 'created_at')
            ->orderBy('fecha', 'desc')
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'tratamiento_id' => $payment->tratamiento_id,
                    'paciente' => optional($payment->treatmentPerformed)->patient 
                                ? optional($payment->treatmentPerformed->patient)->nombres . ' ' . optional($payment->treatmentPerformed->patient)->apellidos
                                : 'N/A',
                    'fecha' => $payment->fecha,
                    'monto' => $payment->monto,
                    'metodo_pago' => $payment->metodo_pago,
                    'comprobante' => $payment->comprobante,
                    'notas' => $payment->notas,
                    'fecha_registro' => $payment->created_at->format('Y-m-d H:i:s')
                ];
            });
        
        $filename = 'pagos_' . date('Y-m-d') . '.' . $format;
        $headers = [
            'ID', 'Tratamiento ID', 'Paciente', 'Fecha', 'Monto', 'Método de Pago',
            'Comprobante', 'Notas', 'Fecha Registro'
        ];
        
        return $this->generateExport($payments, $headers, $filename, $format);
    }
    
    /**
     * Genera un archivo de exportación
     *
     * @param \Illuminate\Support\Collection $data
     * @param array $headers
     * @param string $filename
     * @param string $format
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function generateExport($data, $headers, $filename, $format)
    {
        if ($format === 'csv') {
            $callback = function() use ($data, $headers) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $headers);
                
                foreach ($data as $row) {
                    fputcsv($file, array_values((array) $row));
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } elseif ($format === 'xlsx') {
            // Implementar exportación a Excel si se requiere
            return response('Formato no soportado', 400);
        } elseif ($format === 'pdf') {
            // Implementar exportación a PDF si se requiere
            return response('Formato no soportado', 400);
        }
        
        return response('Formato no soportado', 400);
    }
}