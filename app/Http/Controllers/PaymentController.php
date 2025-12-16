<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\TreatmentPerformed;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Muestra el listado de pagos
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Filtros para búsqueda
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $method = $request->input('method');
        
        $query = Payment::with(['treatmentPerformed.patient', 'treatmentPerformed.diagnosis', 'treatmentPerformed.treatment']);
        
        // Aplicar filtros si existen
        if ($search) {
            $query->whereHas('treatmentPerformed.patient', function($q) use ($search) {
                $q->where('nombres', 'like', "%{$search}%")
                  ->orWhere('apellidos', 'like', "%{$search}%");
            });
        }
        
        if ($dateFrom) {
            $query->where('fecha', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->where('fecha', '<=', $dateTo);
        }
        
        if ($method) {
            $query->where('metodo_pago', $method);
        }
        
        // Obtener pagos con paginación
        $payments = $query->orderBy('fecha', 'desc')->paginate(15);
        
        // Preparar datos para la vista
        $formattedPayments = $payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'date' => $payment->fecha,
                'amount' => $payment->monto,
                'method' => $this->translatePaymentMethod($payment->metodo_pago),
                'patient_id' => $payment->treatmentPerformed->patient->id,
                'patient_name' => $payment->treatmentPerformed->patient->nombres . ' ' . $payment->treatmentPerformed->patient->apellidos,
                'treatment_id' => $payment->treatmentPerformed->id,
                'treatment_name' => $payment->treatmentPerformed->treatment 
                                   ? $payment->treatmentPerformed->treatment->nombre 
                                   : $payment->treatmentPerformed->tratamiento_otro,
                'reference' => $payment->comprobante,
            ];
        });
        
        // Estadísticas
        $totalPayments = Payment::sum('monto');
        $totalToday = Payment::whereDate('fecha', now()->toDateString())->sum('monto');
        $totalWeek = Payment::whereBetween('fecha', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])->sum('monto');
        $totalMonth = Payment::whereRaw('MONTH(fecha) = MONTH(CURRENT_DATE()) AND YEAR(fecha) = YEAR(CURRENT_DATE())')->sum('monto');
        
        $methodStats = DB::table('pagos')
            ->select('metodo_pago', DB::raw('SUM(monto) as total'))
            ->groupBy('metodo_pago')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->metodo_pago => $item->total];
            });
        
        $stats = [
            'total' => $totalPayments,
            'today' => $totalToday,
            'week' => $totalWeek,
            'month' => $totalMonth,
            'methods' => $methodStats
        ];
        // dump($stats);   exit;
        
        return view('payments.index', compact('formattedPayments', 'payments', 'stats', 'search', 'dateFrom', 'dateTo', 'method'));
    }

    /**
     * Muestra el formulario para crear un nuevo pago
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $treatmentId = $request->input('treatment_id');
        $treatment = null;
        
        if ($treatmentId) {
            $treatment = TreatmentPerformed::with(['patient', 'diagnosis', 'treatment'])->findOrFail($treatmentId);
            
            // Calcular saldo pendiente
            $paid = $treatment->payments->sum('monto');
            $balance = $treatment->costo - $paid;
            
            $treatment->paid = $paid;
            $treatment->balance = $balance;
        }
        
        return view('payments.create', compact('treatment'));
    }

    /**
     * Almacena un nuevo pago en la base de datos
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'tratamiento_id' => 'required|exists:tratamientos_realizados,id',
            'fecha' => 'required|date',
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,otro',
            'comprobante' => 'nullable|string|max:100',
            'notas' => 'nullable|string|max:250',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verificar que el monto no exceda el saldo pendiente
        $treatment = TreatmentPerformed::findOrFail($request->input('tratamiento_id'));
        $paid = $treatment->payments->sum('monto');
        $balance = $treatment->costo - $paid;
        
        if ($request->input('monto') > $balance) {
            return redirect()->back()
                ->with('error', 'El monto del pago excede el saldo pendiente de Bs. ' . number_format($balance, 2))
                ->withInput();
        }

        // Crear pago
        $payment = Payment::create([
            'tratamiento_id' => $request->input('tratamiento_id'),
            'fecha' => $request->input('fecha'),
            'monto' => $request->input('monto'),
            'metodo_pago' => $request->input('metodo_pago'),
            'comprobante' => $request->input('comprobante'),
            'notas' => $request->input('notas'),
        ]);

        return redirect()->route('payments.show', $payment->id)
            ->with('success', 'Pago registrado exitosamente');
    }

    /**
     * Muestra los detalles de un pago específico
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $payment = Payment::with(['treatmentPerformed.patient', 'treatmentPerformed.diagnosis', 'treatmentPerformed.treatment'])->findOrFail($id);
        
        // Calcular saldo pendiente después del pago
        $treatment = $payment->treatmentPerformed;
        $allPayments = Payment::where('tratamiento_id', $treatment->id)->orderBy('fecha')->get();
        
        $runningBalance = $treatment->costo;
        $paymentPosition = -1;
        
        foreach ($allPayments as $index => $p) {
            $runningBalance -= $p->monto;
            if ($p->id == $payment->id) {
                $paymentPosition = $index;
            }
        }
        
        $balanceAfter = $runningBalance;
        
        // Si es el último pago, este es el saldo actual
        // Si no, calculamos el saldo después de este pago específico
        if ($paymentPosition < count($allPayments) - 1) {
            $balanceAfter = $treatment->costo;
            for ($i = 0; $i <= $paymentPosition; $i++) {
                $balanceAfter -= $allPayments[$i]->monto;
            }
        }
        
        return view('payments.show', compact('payment', 'balanceAfter'));
    }

    /**
     * Muestra el formulario para editar un pago
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $payment = Payment::with(['treatmentPerformed.patient'])->findOrFail($id);
        
        return view('payments.edit', compact('payment'));
    }

    /**
     * Actualiza los datos de un pago específico
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date',
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,otro',
            'comprobante' => 'nullable|string|max:100',
            'notas' => 'nullable|string|max:250',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Obtener el pago actual
        $payment = Payment::findOrFail($id);
        $treatment = $payment->treatmentPerformed;
        
        // Calcular el saldo pendiente excluyendo este pago
        $paidExcludingThis = $treatment->payments->where('id', '!=', $id)->sum('monto');
        $balanceExcludingThis = $treatment->costo - $paidExcludingThis;
        
        // Verificar que el nuevo monto no exceda el saldo pendiente
        if ($request->input('monto') > $balanceExcludingThis) {
            return redirect()->back()
                ->with('error', 'El monto del pago excede el saldo pendiente de Bs. ' . number_format($balanceExcludingThis, 2))
                ->withInput();
        }

        // Actualizar pago
        $payment->update([
            'fecha' => $request->input('fecha'),
            'monto' => $request->input('monto'),
            'metodo_pago' => $request->input('metodo_pago'),
            'comprobante' => $request->input('comprobante'),
            'notas' => $request->input('notas'),
        ]);

        return redirect()->route('payments.show', $payment->id)
            ->with('success', 'Pago actualizado exitosamente');
    }

    /**
     * Elimina un pago
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $treatmentId = $payment->tratamiento_id;
        
        // Eliminar pago
        $payment->delete();
        
        return redirect()->route('treatments.show', $treatmentId)
            ->with('success', 'Pago eliminado exitosamente');
    }
    
    /**
     * Muestra el reporte de pagos pendientes
     *
     * @return \Illuminate\View\View
     */
    public function pendingPayments()
    {
        // Usar la vista de la base de datos para obtener los saldos pendientes
        $pendingPayments = DB::table('v_saldos_pendientes')
            ->orderBy('saldo_pendiente', 'desc')
            ->get();
            
        $totalPending = $pendingPayments->sum('saldo_pendiente');
        
        return view('payments.pending', compact('pendingPayments', 'totalPending'));
    }
    
    /**
     * Traduce los métodos de pago de la base de datos a formato legible
     *
     * @param  string  $method
     * @return string
     */
    private function translatePaymentMethod($method)
    {
        $translations = [
            'efectivo' => 'Efectivo',
            'tarjeta' => 'Tarjeta de Crédito/Débito',
            'transferencia' => 'Transferencia Bancaria',
            'otro' => 'Otro'
        ];
        
        return $translations[$method] ?? $method;
    }
}