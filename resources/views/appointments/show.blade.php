@extends('layouts.app')

@section('title', 'Detalles de Cita - Consultorio Dental')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalles de la Cita</h1>
        <div>
            <a href="{{ route('appointments.edit', $formattedAppointment['id']) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Calendario
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Información de la Cita -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-alt me-2"></i>Información de la Cita
                    </h6>
                    <span class="badge bg-{{ $formattedAppointment['status'] == 'Confirmada' ? 'success' : ($formattedAppointment['status'] == 'Programada' ? 'info' : ($formattedAppointment['status'] == 'Completada' ? 'secondary' : 'danger')) }}">
                        {{ $formattedAppointment['status'] }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-user me-2"></i>Paciente
                            </h6>
                            <div class="mb-3">
                                <strong>Nombre:</strong>
                                <p class="mb-1">
                                    <a href="{{ route('patients.show', $formattedAppointment['patient_id']) }}">
                                        {{ $formattedAppointment['patient_name'] }}
                                    </a>
                                </p>
                            </div>
                            <div class="mb-3">
                                <strong>Teléfono:</strong>
                                <p class="mb-1">{{ $formattedAppointment['patient_phone'] ?? 'No registrado' }}</p>
                            </div>
                            <div class="mb-3">
                                <strong>Email:</strong>
                                <p class="mb-1">{{ $formattedAppointment['patient_email'] ?? 'No registrado' }}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-user-md me-2"></i>Profesional
                            </h6>
                            <div class="mb-3">
                                <strong>Nombre:</strong>
                                <p class="mb-1">{{ $formattedAppointment['professional_name'] }}</p>
                            </div>
                            
                            <h6 class="text-primary mb-3 mt-4">
                                <i class="fas fa-clock me-2"></i>Horario
                            </h6>
                            <div class="mb-3">
                                <strong>Fecha:</strong>
                                <p class="mb-1">
                                    {{ \Carbon\Carbon::parse($formattedAppointment['date'])->format('d/m/Y') }}
                                    ({{ \Carbon\Carbon::parse($formattedAppointment['date'])->locale('es')->dayName }})
                                </p>
                            </div>
                            <div class="mb-3">
                                <strong>Hora:</strong>
                                <p class="mb-1">{{ $formattedAppointment['time'] }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-sticky-note me-2"></i>Detalles
                            </h6>
                            <div class="mb-3">
                                <strong>Motivo:</strong>
                                <p class="mb-1">{{ $formattedAppointment['reason'] }}</p>
                            </div>
                            
                            @if($formattedAppointment['notes'])
                            <div class="mb-3">
                                <strong>Notas:</strong>
                                <div class="border rounded p-3 bg-light">
                                    {{ $formattedAppointment['notes'] }}
                                </div>
                            </div>
                            @endif
                            
                            <div class="mb-3">
                                <strong>Creado el:</strong>
                                <p class="mb-1">{{ \Carbon\Carbon::parse($formattedAppointment['created_at'])->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Acciones -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="btn-group" role="group">
                                @if($formattedAppointment['status'] == 'Programada')
                                <form action="{{ route('appointments.update.status', $formattedAppointment['id']) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="confirmada">
                                    <button type="submit" class="btn btn-success me-2">
                                        <i class="fas fa-check"></i> Confirmar Cita
                                    </button>
                                </form>
                                @endif
                                
                                @if($formattedAppointment['status'] == 'Confirmada')
                                <form action="{{ route('appointments.update.status', $formattedAppointment['id']) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="completada">
                                    <button type="submit" class="btn btn-info me-2">
                                        <i class="fas fa-check-circle"></i> Marcar como Completada
                                    </button>
                                </form>
                                @endif
                                
                                @if($formattedAppointment['status'] != 'cancelada' && $formattedAppointment['status'] != 'completada')
                                <form action="{{ route('appointments.update.status', $formattedAppointment['id']) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="cancelada">
                                    <button type="submit" class="btn btn-danger me-2" 
                                            onclick="return confirm('¿Está seguro de cancelar esta cita?')">
                                        <i class="fas fa-times"></i> Cancelar Cita
                                    </button>
                                </form>
                                @endif
                                
                                <button type="button" class="btn btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteAppointmentModal">
                                    <i class="fas fa-trash"></i> Eliminar Cita
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Historial del Paciente -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Historial de Citas del Paciente
                    </h6>
                </div>
                <div class="card-body">
                    @if(count($previousAppointments) > 0)
                        <div class="list-group">
                            @foreach($previousAppointments as $prevAppointment)
                            <a href="{{ route('appointments.show', $prevAppointment['id']) }}" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $prevAppointment['reason'] }}</h6>
                                    <small>{{ $prevAppointment['date'] }}</small>
                                </div>
                                <p class="mb-1">
                                    <span class="badge bg-{{ $prevAppointment['status'] == 'Confirmada' ? 'success' : ($prevAppointment['status'] == 'Programada' ? 'info' : ($prevAppointment['status'] == 'Completada' ? 'secondary' : 'danger')) }}">
                                        {{ $prevAppointment['status'] }}
                                    </span>
                                </p>
                                @if($prevAppointment['notes'])
                                <small class="text-muted">{{ Str::limit($prevAppointment['notes'], 50) }}</small>
                                @endif
                            </a>
                            @endforeach
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="{{ route('patients.show', $formattedAppointment['patient_id']) }}" 
                               class="btn btn-sm btn-outline-primary">
                                Ver todas las citas del paciente
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-history fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No hay citas anteriores registradas</p>
                            <p class="text-muted">Esta es la primera cita del paciente</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Acciones Rápidas -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('appointments.create') }}?patient_id={{ $formattedAppointment['patient_id'] }}" 
                           class="btn btn-outline-primary text-start">
                            <i class="fas fa-calendar-plus me-2"></i>Nueva Cita para este Paciente
                        </a>
                        <a href="{{ route('treatments.create', $formattedAppointment['patient_id']) }}" 
                           class="btn btn-outline-success text-start">
                            <i class="fas fa-procedures me-2"></i>Nuevo Tratamiento
                        </a>
                        <a href="{{ route('patients.edit', $formattedAppointment['patient_id']) }}" 
                           class="btn btn-outline-warning text-start">
                            <i class="fas fa-user-edit me-2"></i>Editar Datos del Paciente
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para eliminar cita -->
    <div class="modal fade" id="deleteAppointmentModal" tabindex="-1" aria-labelledby="deleteAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteAppointmentModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>¡Atención!</strong> Esta acción no se puede deshacer.
                    </div>
                    
                    <p>¿Está seguro que desea eliminar la cita del paciente <strong>{{ $formattedAppointment['patient_name'] }}</strong>?</p>
                    <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($formattedAppointment['date'])->format('d/m/Y') }}</p>
                    <p><strong>Hora:</strong> {{ $formattedAppointment['time'] }}</p>
                    <p><strong>Motivo:</strong> {{ $formattedAppointment['reason'] }}</p>
                    
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota:</strong> La cita solo podrá ser eliminada si no tiene tratamientos asociados.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <form action="{{ route('appointments.destroy', $formattedAppointment['id']) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Sí, Eliminar Cita
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .btn-group .btn {
        margin-right: 5px;
    }
    
    .btn-group .btn:last-child {
        margin-right: 0;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirmación antes de cambiar estado
        document.querySelectorAll('form[action*="update.status"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                const estado = this.querySelector('input[name="estado"]').value;
                let mensaje = '';
                
                switch(estado) {
                    case 'confirmada':
                        mensaje = '¿Está seguro de confirmar esta cita?';
                        break;
                    case 'completada':
                        mensaje = '¿Está seguro de marcar esta cita como completada?';
                        break;
                    case 'cancelada':
                        mensaje = '¿Está seguro de cancelar esta cita?';
                        break;
                }
                
                if (mensaje && !confirm(mensaje)) {
                    e.preventDefault();
                }
            });
        });
        
        // Inicializar tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush