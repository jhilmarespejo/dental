@extends('layouts.app')

@section('title', 'Editar Cita - Consultorio Dental')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Editar Cita</h1>
        <div>
            <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-info me-2">
                <i class="fas fa-eye"></i> Ver Detalles
            </a>
            <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Calendario
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Editar Cita - ID: {{ $appointment->id }}</h6>
                        <span class="badge bg-{{ $appointment->estado == 'confirmada' ? 'success' : ($appointment->estado == 'programada' ? 'info' : ($appointment->estado == 'completada' ? 'secondary' : 'danger')) }}">
                            {{ $appointment->estado == 'programada' ? 'Programada' : ($appointment->estado == 'confirmada' ? 'Confirmada' : ($appointment->estado == 'completada' ? 'Completada' : 'Cancelada')) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('appointments.update', $appointment->id) }}" id="appointmentForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Información del Paciente y Profesional -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-users me-2"></i>Paciente y Profesional
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="paciente_id" class="form-label required">Paciente</label>
                                <select id="paciente_id" 
                                        name="paciente_id" 
                                        class="form-select select2 @error('paciente_id') is-invalid @enderror" 
                                        required
                                        data-placeholder="Buscar paciente...">
                                    <option value=""></option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient['id'] }}" 
                                                {{ old('paciente_id', $appointment->paciente_id) == $patient['id'] ? 'selected' : '' }}>
                                            {{ $patient['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('paciente_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="profesional_id" class="form-label required">Profesional</label>
                                <select id="profesional_id" 
                                        name="profesional_id" 
                                        class="form-select select2 @error('profesional_id') is-invalid @enderror" 
                                        required
                                        data-placeholder="Seleccionar profesional...">
                                    <option value=""></option>
                                    @foreach($professionals as $professional)
                                        <option value="{{ $professional['id'] }}" 
                                                {{ old('profesional_id', $appointment->profesional_id) == $professional['id'] ? 'selected' : '' }}>
                                            {{ $professional['name'] }} 
                                            @if($professional['specialty'])
                                                - {{ $professional['specialty'] }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('profesional_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Fecha, Hora y Estado -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-calendar-alt me-2"></i>Horario y Estado
                                </h5>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="fecha" class="form-label required">Fecha</label>
                                <input type="date" 
                                       class="form-control @error('fecha') is-invalid @enderror" 
                                       id="fecha" 
                                       name="fecha" 
                                       value="{{ old('fecha', $appointment->fecha_hora->format('Y-m-d')) }}" 
                                       required>
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="hora" class="form-label required">Hora</label>
                                <input type="time" 
                                       class="form-control @error('hora') is-invalid @enderror" 
                                       id="hora" 
                                       name="hora" 
                                       value="{{ old('hora', $appointment->fecha_hora->format('H:i')) }}" 
                                       required 
                                       step="300">
                                @error('hora')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="estado" class="form-label required">Estado</label>
                                <select id="estado" 
                                        name="estado" 
                                        class="form-select @error('estado') is-invalid @enderror" 
                                        required>
                                    <option value="programada" {{ old('estado', $appointment->estado) == 'programada' ? 'selected' : '' }}>Programada</option>
                                    <option value="confirmada" {{ old('estado', $appointment->estado) == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                                    <option value="completada" {{ old('estado', $appointment->estado) == 'completada' ? 'selected' : '' }}>Completada</option>
                                    <option value="cancelada" {{ old('estado', $appointment->estado) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Detalles de la Cita -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-info-circle me-2"></i>Detalles de la Cita
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="duracion" class="form-label required">Duración (minutos)</label>
                                <input type="number" 
                                       class="form-control @error('duracion') is-invalid @enderror" 
                                       id="duracion" 
                                       name="duracion" 
                                       value="{{ old('duracion', $appointment->duracion) }}" 
                                       required 
                                       min="5" 
                                       max="180"
                                       step="5">
                                @error('duracion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Duración en minutos (mínimo 5, máximo 180)</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="motivo" class="form-label required">Motivo</label>
                                <input type="text" 
                                       class="form-control @error('motivo') is-invalid @enderror" 
                                       id="motivo" 
                                       name="motivo" 
                                       value="{{ old('motivo', $appointment->motivo) }}" 
                                       required 
                                       maxlength="250"
                                       placeholder="Ej: Revisión, Limpieza dental, Tratamiento...">
                                @error('motivo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="notas" class="form-label">Notas Adicionales</label>
                                <textarea class="form-control @error('notas') is-invalid @enderror" 
                                          id="notas" 
                                          name="notas" 
                                          rows="4"
                                          placeholder="Observaciones, instrucciones especiales, alergias a considerar...">{{ old('notas', $appointment->notas) }}</textarea>
                                @error('notas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Información del Sistema -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-database me-2"></i>Información del Sistema
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha de Creación</label>
                                <input type="text" 
                                       class="form-control bg-light" 
                                       value="{{ $appointment->created_at->format('d/m/Y H:i:s') }}" 
                                       readonly>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Última Actualización</label>
                                <input type="text" 
                                       class="form-control bg-light" 
                                       value="{{ now()->format('d/m/Y H:i:s') }}" 
                                       readonly>
                                <small class="form-text text-muted">Se actualizará al guardar los cambios</small>
                            </div>
                        </div>
                        
                        <!-- Vista previa -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h6 class="mb-0">Vista Previa de los Cambios</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Fecha:</strong> <span id="previewDate">-</span></p>
                                                <p><strong>Hora:</strong> <span id="previewTime">-</span></p>
                                                <p><strong>Duración:</strong> <span id="previewDuration">-</span> minutos</p>
                                                <p><strong>Estado:</strong> <span id="previewStatus">-</span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Motivo:</strong> <span id="previewReason">-</span></p>
                                                <p><strong>Notas:</strong> <span id="previewNotes" class="text-muted">-</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botones de acción -->
                        <div class="row mt-4">
                            <div class="col-md-12 d-flex justify-content-between">
                                <div>
                                    <button type="button" 
                                            class="btn btn-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteAppointmentModal">
                                        <i class="fas fa-trash"></i> Eliminar Cita
                                    </button>
                                </div>
                                <div>
                                    <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-secondary me-2">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Guardar Cambios
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
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
                    
                    <p>¿Está seguro que desea eliminar esta cita?</p>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota:</strong> La cita solo podrá ser eliminada si no tiene tratamientos asociados.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST" class="d-inline">
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
    .required::after {
        content: " *";
        color: #e74a3b;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
        
        // Función para actualizar vista previa
        function updateAppointmentPreview() {
            const fechaInput = document.getElementById('fecha');
            const horaInput = document.getElementById('hora');
            const duracionInput = document.getElementById('duracion');
            const motivoInput = document.getElementById('motivo');
            const estadoSelect = document.getElementById('estado');
            const notasTextarea = document.getElementById('notas');
            
            if (fechaInput && horaInput && duracionInput && motivoInput && estadoSelect) {
                const fecha = new Date(fechaInput.value + 'T' + horaInput.value);
                const duracion = duracionInput.value;
                
                // Calcular hora de fin
                const fin = new Date(fecha.getTime() + duracion * 60000);
                
                // Actualizar vista previa
                document.getElementById('previewDate').textContent = 
                    fecha.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                document.getElementById('previewTime').textContent = 
                    fecha.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) + ' - ' + 
                    fin.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                document.getElementById('previewDuration').textContent = duracion;
                document.getElementById('previewReason').textContent = motivoInput.value || 'No especificado';
                
                // Estado con color
                const estadoText = estadoSelect.options[estadoSelect.selectedIndex].text;
                const estadoColors = {
                    'Programada': 'info',
                    'Confirmada': 'success',
                    'Completada': 'secondary',
                    'Cancelada': 'danger'
                };
                const estadoColor = estadoColors[estadoText] || 'secondary';
                
                document.getElementById('previewStatus').innerHTML = 
                    `<span class="badge bg-${estadoColor}">${estadoText}</span>`;
                
                if (notasTextarea && notasTextarea.value.trim()) {
                    document.getElementById('previewNotes').textContent = notasTextarea.value;
                    document.getElementById('previewNotes').classList.remove('text-muted');
                } else {
                    document.getElementById('previewNotes').textContent = 'Ninguna';
                    document.getElementById('previewNotes').classList.add('text-muted');
                }
            }
        }
        
        // Escuchar cambios en los campos
        ['fecha', 'hora', 'duracion', 'motivo', 'estado', 'notas'].forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('input', updateAppointmentPreview);
                field.addEventListener('change', updateAppointmentPreview);
            }
        });
        
        // Actualizar vista previa inicial
        updateAppointmentPreview();
        
        // Validación de fecha mínima
        const fechaInput = document.getElementById('fecha');
        if (fechaInput) {
            const today = new Date().toISOString().split('T')[0];
            fechaInput.min = today;
        }
        
        // Confirmación antes de eliminar
        const deleteForm = document.querySelector('form[action*="destroy"]');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                if (!confirm('¿Está completamente seguro de eliminar esta cita? Esta acción es irreversible.')) {
                    e.preventDefault();
                }
            });
        }
        
        // Verificar disponibilidad del profesional (opcional)
        const profesionalSelect = document.getElementById('profesional_id');
        const form = document.getElementById('appointmentForm');
        
        if (profesionalSelect && form) {
            form.addEventListener('submit', function(e) {
                // Podrías agregar aquí una verificación AJAX de disponibilidad
                // antes de enviar el formulario
            });
        }
    });
</script>
@endpush