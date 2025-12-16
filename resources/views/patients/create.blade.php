@extends('layouts.app')

@section('title', 'Nuevo Paciente - Consultorio Dental')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nuevo Paciente</h1>
        <div>
            <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Datos del Paciente</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('patients.store') }}" id="patientForm">
                        @csrf
                        
                        <!-- Información Personal -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-user me-2"></i>Información Personal
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="nombres" class="form-label required">Nombres</label>
                                <input type="text" 
                                       class="form-control @error('nombres') is-invalid @enderror" 
                                       id="nombres" 
                                       name="nombres" 
                                       value="{{ old('nombres') }}" 
                                       required
                                       maxlength="100">
                                @error('nombres')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="apellidos" class="form-label required">Apellidos</label>
                                <input type="text" 
                                       class="form-control @error('apellidos') is-invalid @enderror" 
                                       id="apellidos" 
                                       name="apellidos" 
                                       value="{{ old('apellidos') }}" 
                                       required
                                       maxlength="100">
                                @error('apellidos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="fecha_nacimiento" class="form-label required">Fecha de Nacimiento</label>
                                <input type="date" 
                                       class="form-control @error('fecha_nacimiento') is-invalid @enderror" 
                                       id="fecha_nacimiento" 
                                       name="fecha_nacimiento" 
                                       value="{{ old('fecha_nacimiento') }}" 
                                       required>
                                @error('fecha_nacimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="genero" class="form-label required">Género</label>
                                <select class="form-select @error('genero') is-invalid @enderror" 
                                        id="genero" 
                                        name="genero" 
                                        required>
                                    <option value="">Seleccionar...</option>
                                    <option value="M" {{ old('genero') == 'M' ? 'selected' : '' }}>Masculino</option>
                                    <option value="F" {{ old('genero') == 'F' ? 'selected' : '' }}>Femenino</option>
                                    <option value="Otro" {{ old('genero') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('genero')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="ci" class="form-label">Cédula de Identidad</label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control @error('ci') is-invalid @enderror" 
                                           id="ci" 
                                           name="ci" 
                                           value="{{ old('ci') }}"
                                           maxlength="12"
                                           placeholder="Ej: 12345678">
                                    <input type="text" 
                                           class="form-control w-25 @error('ci_exp') is-invalid @enderror" 
                                           id="ci_exp" 
                                           name="ci_exp" 
                                           value="{{ old('ci_exp') }}"
                                           maxlength="5"
                                           placeholder="Exp">
                                </div>
                                @error('ci')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @error('ci_exp')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Información de Contacto -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-address-book me-2"></i>Información de Contacto
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="celular" class="form-label">Teléfono/Celular</label>
                                <input type="tel" 
                                       class="form-control @error('celular') is-invalid @enderror" 
                                       id="celular" 
                                       name="celular" 
                                       value="{{ old('celular') }}"
                                       maxlength="15"
                                       placeholder="Ej: 78765432">
                                @error('celular')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}"
                                       maxlength="100"
                                       placeholder="ejemplo@correo.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <textarea class="form-control @error('direccion') is-invalid @enderror" 
                                          id="direccion" 
                                          name="direccion" 
                                          rows="2"
                                          maxlength="500">{{ old('direccion') }}</textarea>
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Información Médica -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-file-medical me-2"></i>Información Médica
                                </h5>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="alergias" class="form-label">Alergias</label>
                                <textarea class="form-control @error('alergias') is-invalid @enderror" 
                                          id="alergias" 
                                          name="alergias" 
                                          rows="3">{{ old('alergias') }}</textarea>
                                <small class="form-text text-muted">Liste las alergias del paciente (medicamentos, alimentos, materiales dentales, etc.)</small>
                                @error('alergias')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="condiciones_medicas" class="form-label">Condiciones Médicas</label>
                                <textarea class="form-control @error('condiciones_medicas') is-invalid @enderror" 
                                          id="condiciones_medicas" 
                                          name="condiciones_medicas" 
                                          rows="3">{{ old('condiciones_medicas') }}</textarea>
                                <small class="form-text text-muted">Enfermedades crónicas, medicamentos actuales, condiciones preexistentes</small>
                                @error('condiciones_medicas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Botones de acción -->
                        <div class="row mt-4">
                            <div class="col-md-12 d-flex justify-content-end">
                                <a href="{{ route('patients.index') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Paciente
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
        // Validación del formulario
        const form = document.getElementById('patientForm');
        const fechaNacimiento = document.getElementById('fecha_nacimiento');
        
        // Establecer fecha máxima (hoy)
        const today = new Date().toISOString().split('T')[0];
        fechaNacimiento.max = today;
        
        // Calcular edad automáticamente cuando cambie la fecha de nacimiento
        fechaNacimiento.addEventListener('change', function() {
            if (this.value) {
                const birthDate = new Date(this.value);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();
                
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                
                // Podrías mostrar la edad en un campo si lo necesitas
                console.log('Edad calculada:', age);
            }
        });
        
        // Validación de cédula (formato simple)
        const ciInput = document.getElementById('ci');
        if (ciInput) {
            ciInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
        
        // Validación de teléfono
        const celularInput = document.getElementById('celular');
        if (celularInput) {
            celularInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9+]/g, '');
            });
        }
        
        // Máscara para CI exp
        const ciExpInput = document.getElementById('ci_exp');
        if (ciExpInput) {
            ciExpInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '');
            });
        }
    });
</script>
@endpush