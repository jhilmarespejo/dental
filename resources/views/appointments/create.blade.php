@extends('layouts.app')

@section('title', 'Nueva Cita - Consultorio Dental')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nueva Cita</h1>
        <div>
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
                    <h6 class="m-0 font-weight-bold text-primary">Datos de la Cita</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('appointments.store') }}" id="appointmentForm">
                        @csrf
                        
                        <!-- Información del Paciente -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-user me-2"></i>Información del Paciente
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
                                                {{ old('paciente_id', $selectedPatientId) == $patient['id'] ? 'selected' : '' }}>
                                            {{ $patient['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('paciente_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="mt-2">
                                    <a href="{{ route('patients.create') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="fas fa-user-plus"></i> Nuevo Paciente
                                    </a>
                                </div>
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
                                                {{ old('profesional_id') == $professional['id'] ? 'selected' : '' }}>
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
                        
                        <!-- Fecha y Hora -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-clock me-2"></i>Fecha y Hora
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="fecha" class="form-label required">Fecha</label>
                                <input type="date" 
                                       class="form-control @error('fecha') is-invalid @enderror" 
                                       id="fecha" 
                                       name="fecha" 
                                       value="{{ old('fecha', request('fecha', date('Y-m-d'))) }}" 
                                       required 
                                       min="{{ date('Y-m-d') }}">
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="hora" class="form-label required">Hora</label>
                                <input type="time" 
                                       class="form-control @error('hora') is-invalid @enderror" 
                                       id="hora" 
                                       name="hora" 
                                       value="{{ old('hora', request('hora', date('H:00', strtotime('+1 hour')))) }}" 
                                       required 
                                       step="300">
                                @error('hora')
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
                                <label for="duracion" class="form-label required">Duración</label>
                                <select id="duracion" 
                                        name="duracion" 
                                        class="form-select @error('duracion') is-invalid @enderror" 
                                        required>
                                    <option value="15" {{ old('duracion', '30') == '15' ? 'selected' : '' }}>15 minutos</option>
                                    <option value="30" {{ old('duracion', '30') == '30' ? 'selected' : '' }}>30 minutos</option>
                                    <option value="45" {{ old('duracion') == '45' ? 'selected' : '' }}>45 minutos</option>
                                    <option value="60" {{ old('duracion') == '60' ? 'selected' : '' }}>1 hora</option>
                                    <option value="90" {{ old('duracion') == '90' ? 'selected' : '' }}>1 hora 30 minutos</option>
                                    <option value="120" {{ old('duracion') == '120' ? 'selected' : '' }}>2 horas</option>
                                </select>
                                @error('duracion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="motivo" class="form-label required">Motivo de la Cita</label>
                                <input type="text" 
                                       class="form-control @error('motivo') is-invalid @enderror" 
                                       id="motivo" 
                                       name="motivo" 
                                       value="{{ old('motivo') }}" 
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
                                          placeholder="Observaciones, instrucciones especiales, alergias a considerar...">{{ old('notas') }}</textarea>
                                @error('notas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Vista previa -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h6 class="mb-0">Vista Previa de la Cita</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Fecha:</strong> <span id="previewDate">-</span></p>
                                                <p><strong>Hora:</strong> <span id="previewTime">-</span></p>
                                                <p><strong>Duración:</strong> <span id="previewDuration">-</span> minutos</p>
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
                            <div class="col-md-12 d-flex justify-content-end">
                                <a href="{{ route('appointments.index') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Cita
                                </button>
                            </div>
                        </div>
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
            const duracionSelect = document.getElementById('duracion');
            const motivoInput = document.getElementById('motivo');
            const notasTextarea = document.getElementById('notas');
            
            if (fechaInput && horaInput && duracionSelect && motivoInput) {
                const fecha = new Date(fechaInput.value + 'T' + horaInput.value);
                const duracion = duracionSelect.value;
                
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
        ['fecha', 'hora', 'duracion', 'motivo', 'notas'].forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('input', updateAppointmentPreview);
                field.addEventListener('change', updateAppointmentPreview);
            }
        });
        
        // Actualizar vista previa inicial
        updateAppointmentPreview();
        
        // Validar disponibilidad del profesional
        const form = document.getElementById('appointmentForm');
        const profesionalSelect = document.getElementById('profesional_id');
        const fechaInput = document.getElementById('fecha');
        const horaInput = document.getElementById('hora');
        const duracionSelect = document.getElementById('duracion');
        
        function checkAvailability() {
            if (profesionalSelect.value && fechaInput.value && horaInput.value && duracionSelect.value) {
                // Aquí podrías agregar una llamada AJAX para verificar disponibilidad
                console.log('Verificando disponibilidad...');
            }
        }
        
        if (profesionalSelect) profesionalSelect.addEventListener('change', checkAvailability);
        if (fechaInput) fechaInput.addEventListener('change', checkAvailability);
        if (horaInput) horaInput.addEventListener('change', checkAvailability);
        if (duracionSelect) duracionSelect.addEventListener('change', checkAvailability);
    });
</script>
@endpush