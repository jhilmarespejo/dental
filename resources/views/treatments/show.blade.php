@extends('layouts.app')

@section('title', 'Detalles de Tratamiento - Consultorio Dental')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-clipboard-list text-primary me-2"></i> Detalles del Tratamiento
        </h1>
        <div>
            <a href="{{ route('treatments.edit', $treatment->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit"></i> Editar
            </a>
            @if($balance > 0)
            <a href="{{ route('payments.create', ['treatment_id' => $treatment->id]) }}" class="btn btn-success me-2">
                <i class="fas fa-dollar-sign"></i> Registrar Pago
            </a>
            @endif
            <a href="{{ route('treatments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-info-circle me-1"></i> Información del Tratamiento
                    </h6>
                    <span class="badge bg-{{ $status == 'Completado' ? 'success' : ($status == 'En proceso' ? 'info' : 'warning') }}">
                        {{ $status }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="mb-2">Datos Generales</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Paciente:</th>
                                    <td>
                                        <a href="{{ route('patients.show', $treatment->patient->id) }}">
                                            {{ $treatment->patient->nombres }} {{ $treatment->patient->apellidos }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Profesional:</th>
                                    <td>{{ $treatment->professional->nombres }} {{ $treatment->professional->apellidos }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha:</th>
                                    <td>{{ $treatment->fecha }}</td>
                                </tr>
                                @if($treatment->appointment)
                                <tr>
                                    <th>Cita asociada:</th>
                                    <td>
                                        <a href="{{ route('appointments.show', $treatment->appointment->id) }}">
                                            {{ $treatment->appointment->fecha_hora->format('d/m/Y H:i') }}
                                        </a>
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-2">Datos del Tratamiento</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Diagnóstico:</th>
                                    <td>{{ $treatment->diagnosis ? $treatment->diagnosis->nombre : $treatment->diagnostico_otro }}</td>
                                </tr>
                                <tr>
                                    <th>Tratamiento:</th>
                                    <td>{{ $treatment->treatment ? $treatment->treatment->nombre : $treatment->tratamiento_otro }}</td>
                                </tr>
                                <tr>
                                    <th>Pieza dental:</th>
                                    <td>{{ $treatment->pieza_dental ?? 'General' }}</td>
                                </tr>
                                <tr>
                                    <th>Costo:</th>
                                    <td>Bs. {{ number_format($treatment->costo, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($treatment->observaciones)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-light">
                                <h5 class="alert-heading">Observaciones</h5>
                                <p>{{ $treatment->observaciones }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 text-center">
                                            <h6 class="text-primary mb-1">Costo Total</h6>
                                            <h4 class="mb-0">Bs. {{ number_format($treatment->costo, 2) }}</h4>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <h6 class="text-success mb-1">Pagado</h6>
                                            <h4 class="mb-0">Bs. {{ number_format($paid, 2) }}</h4>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <h6 class="text-{{ $balance > 0 ? 'warning' : 'dark' }} mb-1">Saldo</h6>
                                            <h4 class="mb-0">Bs. {{ number_format($balance, 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historial de pagos -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="mb-3">Historial de Pagos</h5>
                            @if(count($payments) > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Monto</th>
                                                <th>Método</th>
                                                <th>Referencia</th>
                                                <th>Notas</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($payments as $payment)
                                            <tr>
                                                <td>{{ date('d/m/Y', strtotime($payment['date'])) }}</td>
                                                <td>Bs. {{ number_format($payment['amount'], 2) }}</td>
                                                <td>{{ $payment['method'] }}</td>
                                                <td>{{ $payment['reference'] ?? 'N/A' }}</td>
                                                <td>{{ $payment['notes'] ?? 'N/A' }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('payments.show', $payment['id']) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i> No hay pagos registrados.
                                    @if($balance > 0)
                                        <a href="{{ route('payments.create', ['treatment_id' => $treatment->id]) }}" class="btn btn-sm btn-success ms-2">
                                            <i class="fas fa-dollar-sign"></i> Registrar Pago
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar de Información -->
        <div class="col-xl-4 col-lg-5">
            <!-- Información del Paciente -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-user me-1"></i> Información del Paciente
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="patient-avatar mx-auto rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ substr($treatment->patient->nombres, 0, 1) }}{{ substr($treatment->patient->apellidos, 0, 1) }}
                        </div>
                        <h6 class="mt-2">{{ $treatment->patient->nombres }} {{ $treatment->patient->apellidos }}</h6>
                        <p class="small text-muted mb-0">{{ $treatment->patient->edad }} años</p>
                    </div>
                    
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">CI:</th>
                            <td>{{ $treatment->patient->ci ?? 'No registrado' }} {{ $treatment->patient->ci_exp }}</td>
                        </tr>
                        <tr>
                            <th>Teléfono:</th>
                            <td>{{ $treatment->patient->celular ?? 'No registrado' }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $treatment->patient->email ?? 'No registrado' }}</td>
                        </tr>
                    </table>
                    
                    <div class="mt-3 text-center">
                        <a href="{{ route('patients.show', $treatment->patient->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-user-circle"></i> Ver Perfil Completo
                        </a>
                    </div>
                </div>
            </div>

            <!-- Imágenes del Tratamiento -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-images me-1"></i> Imágenes
                    </h6>
                </div>
                <div class="card-body">
                    @if(count($treatment->images) > 0)
                        <div class="row">
                            @foreach($treatment->images as $image)
                                <div class="col-md-6 mb-3">
                                    <a href="{{ asset('storage/' . $image->ruta_archivo) }}" data-lightbox="treatment-images" data-title="{{ $image->descripcion ?? 'Imagen del tratamiento' }}">
                                        <img src="{{ asset('storage/' . $image->ruta_archivo) }}" class="img-fluid img-thumbnail" alt="Imagen del tratamiento">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-image fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No hay imágenes asociadas a este tratamiento</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Acciones Adicionales -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-cog me-1"></i> Acciones
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('treatments.edit', $treatment->id) }}" class="btn btn-warning btn-block">
                            <i class="fas fa-edit me-1"></i> Editar Tratamiento
                        </a>
                        @if($balance > 0)
                        <a href="{{ route('payments.create', ['treatment_id' => $treatment->id]) }}" class="btn btn-success btn-block">
                            <i class="fas fa-dollar-sign me-1"></i> Registrar Pago
                        </a>
                        @endif
                        <button type="button" class="btn btn-danger btn-block" data-bs-toggle="modal" data-bs-target="#deleteTreatmentModal">
                            <i class="fas fa-trash me-1"></i> Eliminar Tratamiento
                        </button>
                        <a href="{{ route('treatments.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Treatment Modal -->
    <div class="modal fade" id="deleteTreatmentModal" tabindex="-1" aria-labelledby="deleteTreatmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteTreatmentModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea eliminar este tratamiento? Esta acción no se puede deshacer.</p>
                    <p><strong>Nota:</strong> No se puede eliminar un tratamiento que tenga pagos asociados.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('treatments.destroy', $treatment->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
<style>
    .patient-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script>
    // Configuración de lightbox para imágenes
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true,
        'albumLabel': "Imagen %1 de %2"
    });
</script>
@endpush