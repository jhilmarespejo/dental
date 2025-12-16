@extends('layouts.app')

@section('title', 'Nuevo Tratamiento - Consultorio Dental')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle text-primary me-2"></i> Nuevo Tratamiento
        </h1>
        <a href="{{ route('treatments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('treatments.store') }}" method="POST" enctype="multipart/form-data" id="treatmentForm">
                @csrf
                
                <!-- Datos del paciente -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Datos del Paciente</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="paciente_id" class="form-label">Paciente <span class="text-danger">*</span></label>
                                        <select class="form-select select2 @error('paciente_id') is-invalid @enderror" 
                                                id="paciente_id" name="paciente_id" required
                                                data-placeholder="Buscar paciente...">
                                            <option value=""></option>
                                            @foreach($patients as $patientItem)
                                                <option value="{{ $patientItem['id'] }}" 
                                                    {{ old('paciente_id', $selectedPatientId) == $patientItem['id'] ? 'selected' : '' }}>
                                                    {{ $patientItem['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('paciente_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="mt-2">
                                            <a href="{{ route('patients.create') }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-user-plus"></i> Nuevo Paciente
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="patient-info" class="{{ $patient ? '' : 'd-none' }}">
                                    @if($patient)
                                    <div class="alert alert-info">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>CI:</strong> {{ $patient->ci ?? 'No registrado' }}
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Edad:</strong> {{ $patient->edad ?? 'N/A' }} años
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Teléfono:</strong> {{ $patient->celular ?? 'No registrado' }}
                                            </div>
                                        </div>
                                        
                                        @if($patient->alergias || $patient->condiciones_medicas)
                                        <hr>
                                        <div class="row mt-2">
                                            @if($patient->alergias)
                                            <div class="col-md-6">
                                                <strong class="text-danger">Alergias:</strong> {{ $patient->alergias }}
                                            </div>
                                            @endif
                                            
                                            @if($patient->condiciones_medicas)
                                            <div class="col-md-6">
                                                <strong class="text-warning">Condiciones médicas:</strong> {{ $patient->condiciones_medicas }}
                                            </div>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Resto del formulario se mantiene igual... -->
                <!-- Datos del tratamiento -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Datos del Tratamiento</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('fecha') is-invalid @enderror" 
                                               id="fecha" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required>
                                        @error('fecha')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="profesional_id" class="form-label">Profesional <span class="text-danger">*</span></label>
                                        <select class="form-select select2 @error('profesional_id') is-invalid @enderror" 
                                                id="profesional_id" name="profesional_id" required
                                                data-placeholder="Seleccionar profesional...">
                                            <option value=""></option>
                                            @foreach($professionals as $professional)
                                                <option value="{{ $professional->id }}" 
                                                    {{ old('profesional_id') == $professional->id ? 'selected' : '' }}>
                                                    {{ $professional->apellidos }}, {{ $professional->nombres }} 
                                                    {{ $professional->especialidad ? "($professional->especialidad)" : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('profesional_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="diagnostico_id" class="form-label">Diagnóstico</label>
                                        <select class="form-select select2-with-tags @error('diagnostico_id') is-invalid @enderror" 
                                                id="diagnostico_id" name="diagnostico_id"
                                                data-placeholder="Seleccionar o ingresar diagnóstico...">
                                            <option value=""></option>
                                            @foreach($diagnosisList as $diagnosis)
                                                <option value="{{ $diagnosis->id }}" 
                                                    {{ old('diagnostico_id') == $diagnosis->id ? 'selected' : '' }}>
                                                    {{ $diagnosis->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('diagnostico_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3 {{ old('diagnostico_otro') ? '' : 'd-none' }}" id="diagnostico_otro_container">
                                        <label for="diagnostico_otro" class="form-label">Otro diagnóstico</label>
                                        <input type="text" class="form-control @error('diagnostico_otro') is-invalid @enderror" 
                                               id="diagnostico_otro" name="diagnostico_otro" value="{{ old('diagnostico_otro') }}">
                                        @error('diagnostico_otro')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="tratamiento_id" class="form-label">Tratamiento</label>
                                        <select class="form-select select2-with-tags @error('tratamiento_id') is-invalid @enderror" 
                                                id="tratamiento_id" name="tratamiento_id"
                                                data-placeholder="Seleccionar o ingresar tratamiento...">
                                            <option value=""></option>
                                            @foreach($treatmentList as $treatmentItem)
                                                <option value="{{ $treatmentItem->id }}" 
                                                    data-cost="{{ $treatmentItem->costo_sugerido }}"
                                                    {{ old('tratamiento_id') == $treatmentItem->id ? 'selected' : '' }}>
                                                    {{ $treatmentItem->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tratamiento_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3 {{ old('tratamiento_otro') ? '' : 'd-none' }}" id="tratamiento_otro_container">
                                        <label for="tratamiento_otro" class="form-label">Otro tratamiento</label>
                                        <input type="text" class="form-control @error('tratamiento_otro') is-invalid @enderror" 
                                               id="tratamiento_otro" name="tratamiento_otro" value="{{ old('tratamiento_otro') }}">
                                        @error('tratamiento_otro')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="pieza_dental" class="form-label">Pieza Dental</label>
                                        <input type="text" class="form-control @error('pieza_dental') is-invalid @enderror" 
                                               id="pieza_dental" name="pieza_dental" value="{{ old('pieza_dental') }}" 
                                               placeholder="Ej: 36, 11-13">
                                        @error('pieza_dental')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="costo" class="form-label">Costo (Bs.) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" 
                                               class="form-control @error('costo') is-invalid @enderror" 
                                               id="costo" name="costo" value="{{ old('costo') }}" required>
                                        @error('costo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="cita_id" class="form-label">Cita asociada</label>
                                        <select class="form-select @error('cita_id') is-invalid @enderror" 
                                                id="cita_id" name="cita_id">
                                            <option value="">Ninguna</option>
                                            <!-- Las citas se cargarán dinámicamente via AJAX cuando se seleccione un paciente -->
                                        </select>
                                        @error('cita_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="observaciones" class="form-label">Observaciones</label>
                                        <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                                  id="observaciones" name="observaciones" rows="3">{{ old('observaciones') }}</textarea>
                                        @error('observaciones')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pago inicial -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" 
                                           id="register_initial_payment" name="register_initial_payment" 
                                           {{ old('register_initial_payment') ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold text-primary" 
                                           for="register_initial_payment">Registrar pago inicial</label>
                                </div>
                            </div>
                            <div class="card-body {{ old('register_initial_payment') ? '' : 'd-none' }}" 
                                 id="payment_section">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="monto_pago_inicial" class="form-label">Monto (Bs.)</label>
                                        <input type="number" step="0.01" min="0" 
                                               class="form-control @error('monto_pago_inicial') is-invalid @enderror" 
                                               id="monto_pago_inicial" name="monto_pago_inicial" 
                                               value="{{ old('monto_pago_inicial') }}">
                                        @error('monto_pago_inicial')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="metodo_pago" class="form-label">Método de pago</label>
                                        <select class="form-select @error('metodo_pago') is-invalid @enderror" 
                                                id="metodo_pago" name="metodo_pago">
                                            <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>
                                                Efectivo
                                            </option>
                                            <option value="tarjeta" {{ old('metodo_pago') == 'tarjeta' ? 'selected' : '' }}>
                                                Tarjeta de Crédito/Débito
                                            </option>
                                            <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>
                                                Transferencia Bancaria
                                            </option>
                                            <option value="otro" {{ old('metodo_pago') == 'otro' ? 'selected' : '' }}>
                                                Otro
                                            </option>
                                        </select>
                                        @error('metodo_pago')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="comprobante" class="form-label">Comprobante/Referencia</label>
                                        <input type="text" class="form-control @error('comprobante') is-invalid @enderror" 
                                               id="comprobante" name="comprobante" value="{{ old('comprobante') }}">
                                        @error('comprobante')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Imágenes -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">Imágenes (opcional)</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="imagenes" class="form-label">Adjuntar imágenes</label>
                                    <input type="file" class="form-control @error('imagenes') is-invalid @enderror" 
                                           id="imagenes" name="imagenes[]" multiple accept="image/*">
                                    <div class="form-text">Puedes seleccionar múltiples imágenes (máximo 2MB cada una)</div>
                                    @error('imagenes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @error('imagenes.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div id="image-previews" class="row mt-3">
                                    <!-- Las vistas previas de las imágenes se mostrarán aquí -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="row">
                    <div class="col-md-12 text-end">
                        <a href="{{ route('treatments.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Guardar Tratamiento
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .select2-container--bootstrap-5 .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px) !important;
    }
    
    .image-preview {
        position: relative;
        margin-bottom: 15px;
    }
    
    .image-preview .remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(255, 0, 0, 0.7);
        color: white;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        text-align: center;
        line-height: 25px;
        cursor: pointer;
        border: none;
    }
    
    .image-preview img {
        max-height: 150px;
        max-width: 100%;
        border-radius: 5px;
        border: 1px solid #ddd;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Inicializar Select2 para pacientes (EXACTAMENTE COMO EN APPOINTMENTS)
        $('#paciente_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Buscar paciente...',
            allowClear: true
        });
        
        // Inicializar Select2 para profesionales
        $('#profesional_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Seleccionar profesional...',
            allowClear: true
        });
        
        // Manejar cambio de paciente
        $('#paciente_id').on('change', function() {
            const selectedOption = $(this).find(':selected');
            const patientInfo = $('#patient-info');
            
            if ($(this).val()) {
                patientInfo.removeClass('d-none');
                // Cargar las citas disponibles para este paciente
                loadPatientAppointments($(this).val());
            } else {
                patientInfo.addClass('d-none');
                $('#cita_id').empty().append('<option value="">Ninguna</option>');
            }
        });
        
        // Inicializar Select2 para diagnósticos
        $('#diagnostico_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Seleccionar o ingresar diagnóstico...',
            allowClear: true,
            tags: true,
            createTag: function(params) {
                return {
                    id: 'new:' + params.term,
                    text: params.term,
                    newTag: true
                };
            }
        });
        
        // Manejar cambio de diagnóstico
        $('#diagnostico_id').on('change', function() {
            const selectedOption = $(this).find(':selected');
            const diagnosticoOtroContainer = $('#diagnostico_otro_container');
            
            if (selectedOption.length && selectedOption.data('newTag')) {
                diagnosticoOtroContainer.removeClass('d-none');
                $('#diagnostico_otro').val(selectedOption.text()).prop('required', true);
            } else {
                diagnosticoOtroContainer.addClass('d-none');
                $('#diagnostico_otro').val('').prop('required', false);
            }
        });
        
        // Inicializar Select2 para tratamientos
        $('#tratamiento_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Seleccionar o ingresar tratamiento...',
            allowClear: true,
            tags: true,
            createTag: function(params) {
                return {
                    id: 'new:' + params.term,
                    text: params.term,
                    newTag: true
                };
            }
        });
        
        // Manejar cambio de tratamiento
        $('#tratamiento_id').on('change', function() {
            const selectedOption = $(this).find(':selected');
            const tratamientoOtroContainer = $('#tratamiento_otro_container');
            
            if (selectedOption.length && selectedOption.data('newTag')) {
                tratamientoOtroContainer.removeClass('d-none');
                $('#tratamiento_otro').val(selectedOption.text()).prop('required', true);
            } else {
                tratamientoOtroContainer.addClass('d-none');
                $('#tratamiento_otro').val('').prop('required', false);
            }
            
            // Establecer costo sugerido si está disponible
            if (selectedOption.length && selectedOption.data('cost') && $('#costo').val() === '') {
                $('#costo').val(selectedOption.data('cost'));
            }
        });
        
        // Toggle de sección de pago inicial
        $('#register_initial_payment').change(function() {
            if ($(this).is(':checked')) {
                $('#payment_section').removeClass('d-none');
                $('#monto_pago_inicial').prop('required', true);
                $('#metodo_pago').prop('required', true);
            } else {
                $('#payment_section').addClass('d-none');
                $('#monto_pago_inicial').prop('required', false);
                $('#metodo_pago').prop('required', false);
            }
        });
        
        // Vista previa de imágenes
        $('#imagenes').change(function() {
            $('#image-previews').empty();
            
            const files = this.files;
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const previewHTML = `
                        <div class="col-md-3 image-preview">
                            <button type="button" class="remove-btn" data-index="${i}">
                                <i class="fas fa-times"></i>
                            </button>
                            <img src="${e.target.result}" alt="Vista previa">
                            <div class="small text-muted mt-1">${file.name}</div>
                        </div>
                    `;
                    
                    $('#image-previews').append(previewHTML);
                };
                
                reader.readAsDataURL(file);
            }
        });
        
        // Eliminar vista previa de imagen
        $(document).on('click', '.remove-btn', function(e) {
            e.preventDefault();
            const index = $(this).data('index');
            const input = document.getElementById('imagenes');
            const dt = new DataTransfer();
            
            for (let i = 0; i < input.files.length; i++) {
                if (i !== index) {
                    dt.items.add(input.files[i]);
                }
            }
            
            input.files = dt.files;
            $(this).closest('.image-preview').remove();
        });
        
        // Cargar citas del paciente
        function loadPatientAppointments(patientId) {
            // Construir URL correctamente
            const url = `/api/patients/${patientId}/appointments`;
            
            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    const citaSelect = $('#cita_id');
                    
                    // Limpiar opciones existentes
                    citaSelect.empty().append('<option value="">Ninguna</option>');
                    
                    // Agregar las citas recientes del paciente
                    if (data.length > 0) {
                        $.each(data, function(index, appointment) {
                            const fecha = new Date(appointment.fecha_hora);
                            const optionText = fecha.toLocaleDateString('es-ES') + ' ' + 
                                            fecha.toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'}) + 
                                            ' - ' + appointment.motivo;
                            citaSelect.append(new Option(optionText, appointment.id));
                        });
                    }
                },
                error: function() {
                    console.error('Error al cargar las citas del paciente');
                }
            });
        }
        // Si hay un paciente seleccionado inicialmente
        @if($selectedPatientId)
            // Cargar citas del paciente seleccionado
            loadPatientAppointments({{ $selectedPatientId }});
        @endif
    });
</script>
@endpush