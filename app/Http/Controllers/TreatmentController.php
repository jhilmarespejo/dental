<?php

namespace App\Http\Controllers;

use App\Models\TreatmentPerformed;
use App\Models\Treatment;
use App\Models\Diagnosis;
use App\Models\Patient;
use App\Models\Professional;
use App\Models\Payment;
use App\Models\TreatmentImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class TreatmentController extends Controller
{
    /**
     * Muestra el listado de tratamientos
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Filtros para búsqueda
        $search = $request->input('search');
        $status = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        
        $query = TreatmentPerformed::with(['patient', 'professional', 'diagnosis', 'treatment', 'payments']);
        
        // Aplicar filtros si existen
        if ($search) {
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('nombres', 'like', "%{$search}%")
                  ->orWhere('apellidos', 'like', "%{$search}%");
            })->orWhereHas('diagnosis', function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%");
            })->orWhereHas('treatment', function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%");
            })->orWhere('diagnostico_otro', 'like', "%{$search}%")
              ->orWhere('tratamiento_otro', 'like', "%{$search}%");
        }
        
        if ($status === 'pending') {
            $query->whereRaw('costo > (SELECT COALESCE(SUM(monto), 0) FROM pagos WHERE pagos.tratamiento_id = tratamientos_realizados.id)');
        } elseif ($status === 'completed') {
            $query->whereRaw('costo <= (SELECT COALESCE(SUM(monto), 0) FROM pagos WHERE pagos.tratamiento_id = tratamientos_realizados.id)');
        }
        
        if ($dateFrom) {
            $query->where('fecha', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->where('fecha', '<=', $dateTo);
        }
        
        // Obtener tratamientos con paginación
        $treatments = $query->orderBy('fecha', 'desc')->paginate(10);
        
        // Preparar datos para la vista
        $formattedTreatments = $treatments->map(function ($treatment) {
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
                'patient_id' => $treatment->patient->id,
                'patient_name' => $treatment->patient->nombres . ' ' . $treatment->patient->apellidos,
                'professional_id' => $treatment->professional->id,
                'professional_name' => $treatment->professional->nombres . ' ' . $treatment->professional->apellidos,
                'diagnosis' => $treatment->diagnosis ? $treatment->diagnosis->nombre : $treatment->diagnostico_otro,
                'treatment' => $treatment->treatment ? $treatment->treatment->nombre : $treatment->tratamiento_otro,
                'tooth' => $treatment->pieza_dental ?? 'General',
                'cost' => $treatment->costo,
                'paid' => $paid,
                'balance' => $balance,
                'status' => $status
            ];
        });
        
        // Obtener catálogos para filtros
        $diagnosisList = Diagnosis::orderBy('nombre')->get();
        $treatmentList = Treatment::orderBy('nombre')->get();
        
        return view('treatments.index', compact('formattedTreatments', 'treatments', 'diagnosisList', 'treatmentList', 'search', 'status', 'dateFrom', 'dateTo'));
    }

    /**
     * Muestra el formulario para crear un nuevo tratamiento
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $selectedPatientId = $request->input('patient_id');
        $patient = null;
        
        if ($selectedPatientId) {
            $patient = Patient::find($selectedPatientId);
        }
        
        // Obtener catálogos
        $diagnosisList = Diagnosis::orderBy('nombre')->get();
        $treatmentList = Treatment::orderBy('nombre')->get();
        $professionals = Professional::where('estado', 'activo')
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();
        
        // Obtener todos los pacientes para el selector (COMO EL APPOINTMENT CONTROLLER)
        $patients = Patient::select('id', 'nombres', 'apellidos', 'ci', 'edad', 'celular')
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get()
            ->map(function ($patientItem) {
                return [
                    'id' => $patientItem->id,
                    'name' => $patientItem->nombres . ' ' . $patientItem->apellidos . 
                            ($patientItem->ci ? ' (' . $patientItem->ci . ')' : '')
                ];
            });
        
        return view('treatments.create', compact(
            'patient', 
            'patients', // ← ESTO ES LO QUE FALTA
            'diagnosisList', 
            'treatmentList', 
            'professionals',
            'selectedPatientId'
        ));
    }

    /**
     * Almacena un nuevo tratamiento en la base de datos
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
            'fecha' => 'required|date',
            'diagnostico_id' => 'nullable|exists:diagnosticos,id',
            'diagnostico_otro' => 'nullable|required_without:diagnostico_id|string|max:250',
            'tratamiento_id' => 'nullable|exists:tratamientos,id',
            'tratamiento_otro' => 'nullable|required_without:tratamiento_id|string|max:250',
            'pieza_dental' => 'nullable|string|max:10',
            'costo' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
            'monto_pago_inicial' => 'nullable|numeric|min:0',
            'metodo_pago' => 'nullable|required_with:monto_pago_inicial|in:efectivo,tarjeta,transferencia,otro',
            'comprobante' => 'nullable|string|max:100',
            'imagenes.*' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Crear tratamiento
        $treatment = TreatmentPerformed::create([
            'paciente_id' => $request->input('paciente_id'),
            'profesional_id' => $request->input('profesional_id'),
            'cita_id' => $request->input('cita_id'),
            'fecha' => $request->input('fecha'),
            'diagnostico_id' => $request->input('diagnostico_id'),
            'diagnostico_otro' => $request->input('diagnostico_otro'),
            'tratamiento_id' => $request->input('tratamiento_id'),
            'tratamiento_otro' => $request->input('tratamiento_otro'),
            'pieza_dental' => $request->input('pieza_dental'),
            'costo' => $request->input('costo'),
            'observaciones' => $request->input('observaciones'),
        ]);
        
        // Si se proporcionó un pago inicial, registrarlo
        if ($request->has('monto_pago_inicial') && $request->input('monto_pago_inicial') > 0) {
            Payment::create([
                'tratamiento_id' => $treatment->id,
                'fecha' => $request->input('fecha'),
                'monto' => $request->input('monto_pago_inicial'),
                'metodo_pago' => $request->input('metodo_pago'),
                'comprobante' => $request->input('comprobante'),
                'notas' => 'Pago inicial al crear el tratamiento',
            ]);
        }
        
        // Subir imágenes si existen
        if ($request->hasFile('imagenes')) {
            $orden = 1;
            foreach ($request->file('imagenes') as $image) {
                $path = $image->store('tratamientos/' . $treatment->id, 'public');
                
                TreatmentImage::create([
                    'tratamiento_id' => $treatment->id,
                    'ruta_archivo' => $path,
                    'nombre_archivo' => $image->getClientOriginalName(),
                    'tipo_archivo' => $image->getMimeType(),
                    'descripcion' => 'Imagen del tratamiento',
                    'tamano' => $image->getSize(),
                    'orden' => $orden++,
                ]);
            }
        }

        return redirect()->route('treatments.show', $treatment->id)
            ->with('success', 'Tratamiento registrado exitosamente');
    }

    /**
     * Muestra los detalles de un tratamiento específico
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $treatment = TreatmentPerformed::with(['patient', 'professional', 'diagnosis', 'treatment', 'payments', 'images', 'appointment'])->findOrFail($id);
        
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
        
        // Preparar pagos para la vista
        $payments = $treatment->payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'date' => $payment->fecha,
                'amount' => $payment->monto,
                'method' => $this->translatePaymentMethod($payment->metodo_pago),
                'reference' => $payment->comprobante,
                'notes' => $payment->notas,
            ];
        });
        
        return view('treatments.show', compact('treatment', 'status', 'paid', 'balance', 'payments'));
    }

    /**
     * Muestra el formulario para editar un tratamiento
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $treatment = TreatmentPerformed::with(['patient', 'professional', 'diagnosis', 'treatment', 'images'])->findOrFail($id);
        
        // Obtener catálogos
        $diagnosisList = Diagnosis::orderBy('nombre')->get();
        $treatmentList = Treatment::orderBy('nombre')->get();
        $professionals = Professional::where('estado', 'activo')->orderBy('apellidos')->orderBy('nombres')->get();
        
        return view('treatments.edit', compact('treatment', 'diagnosisList', 'treatmentList', 'professionals'));
    }

    /**
     * Actualiza los datos de un tratamiento específico
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'profesional_id' => 'required|exists:profesionales,id',
            'fecha' => 'required|date',
            'diagnostico_id' => 'nullable|exists:diagnosticos,id',
            'diagnostico_otro' => 'nullable|required_without:diagnostico_id|string|max:250',
            'tratamiento_id' => 'nullable|exists:tratamientos,id',
            'tratamiento_otro' => 'nullable|required_without:tratamiento_id|string|max:250',
            'pieza_dental' => 'nullable|string|max:10',
            'costo' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
            'imagenes.*' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'eliminar_imagenes' => 'nullable|array',
            'eliminar_imagenes.*' => 'exists:tratamiento_imagenes,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Actualizar tratamiento
        $treatment = TreatmentPerformed::findOrFail($id);
        $treatment->update([
            'profesional_id' => $request->input('profesional_id'),
            'fecha' => $request->input('fecha'),
            'diagnostico_id' => $request->input('diagnostico_id'),
            'diagnostico_otro' => $request->input('diagnostico_otro'),
            'tratamiento_id' => $request->input('tratamiento_id'),
            'tratamiento_otro' => $request->input('tratamiento_otro'),
            'pieza_dental' => $request->input('pieza_dental'),
            'costo' => $request->input('costo'),
            'observaciones' => $request->input('observaciones'),
        ]);
        
        // Eliminar imágenes si se solicitó
        if ($request->has('eliminar_imagenes')) {
            $imagesToDelete = TreatmentImage::whereIn('id', $request->input('eliminar_imagenes'))->get();
            
            foreach ($imagesToDelete as $image) {
                // Eliminar archivo físico
                Storage::disk('public')->delete($image->ruta_archivo);
                
                // Eliminar registro
                $image->delete();
            }
        }
        
        // Subir nuevas imágenes si existen
        if ($request->hasFile('imagenes')) {
            // Obtener el último orden
            $ultimoOrden = TreatmentImage::where('tratamiento_id', $treatment->id)->max('orden') ?? 0;
            $orden = $ultimoOrden + 1;
            
            foreach ($request->file('imagenes') as $image) {
                $path = $image->store('tratamientos/' . $treatment->id, 'public');
                
                TreatmentImage::create([
                    'tratamiento_id' => $treatment->id,
                    'ruta_archivo' => $path,
                    'nombre_archivo' => $image->getClientOriginalName(),
                    'tipo_archivo' => $image->getMimeType(),
                    'descripcion' => 'Imagen del tratamiento',
                    'tamano' => $image->getSize(),
                    'orden' => $orden++,
                ]);
            }
        }

        return redirect()->route('treatments.show', $treatment->id)
            ->with('success', 'Tratamiento actualizado exitosamente');
    }

    /**
     * Elimina un tratamiento
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $treatment = TreatmentPerformed::findOrFail($id);
        
        // Verificar si tiene pagos asociados
        if ($treatment->payments()->exists()) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el tratamiento porque tiene pagos asociados');
        }
        
        // Eliminar imágenes asociadas
        foreach ($treatment->images as $image) {
            // Eliminar archivo físico
            Storage::disk('public')->delete($image->ruta_archivo);
            
            // Eliminar registro
            $image->delete();
        }
        
        // Eliminar tratamiento
        $treatment->delete();
        
        return redirect()->route('treatments.index')
            ->with('success', 'Tratamiento eliminado exitosamente');
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
    


        /**
     * Obtener pacientes para autocomplete (AJAX)
     */
    public function searchPatients(Request $request)
    {
        $query = $request->input('q');
        
        $patients = Patient::where('nombres', 'like', "%{$query}%")
            ->orWhere('apellidos', 'like', "%{$query}%")
            ->orWhere('ci', 'like', "%{$query}%")
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->limit(10)
            ->get(['id', 'nombres', 'apellidos', 'ci', 'edad', 'celular']);
        
        return response()->json($patients);
    }

    
    /**
     * Buscar catálogos para autocompletar (AJAX)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchCatalog(Request $request)
    {
        $type = $request->input('type');
        $search = $request->input('q');
        
        $results = [];
        
        if ($type === 'diagnosis') {
            $results = Diagnosis::where('nombre', 'like', "%{$search}%")
                ->orderBy('nombre')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'text' => $item->nombre,
                        'description' => $item->descripcion
                    ];
                });
        } elseif ($type === 'treatment') {
            $results = Treatment::where('nombre', 'like', "%{$search}%")
                ->orderBy('nombre')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'text' => $item->nombre,
                        'description' => $item->descripcion,
                        'cost' => $item->costo_sugerido
                    ];
                });
        }
        
        return response()->json($results);
    }
}