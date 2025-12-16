@extends('layouts.app')

@section('title', 'Registrar Pago')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3">Registrar Nuevo Pago</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información del Pago</h6>
                </div>
                <div class="card-body">
                    @if($treatment)
                    <!-- Información del tratamiento -->
                    <div class="alert alert-info mb-4">
                        <h5 class="alert-heading">Información del Tratamiento</h5>
                        <p><strong>Paciente:</strong> {{ $treatment->patient->nombres }} {{ $treatment->patient->apellidos }}</p>
                        <p><strong>Tratamiento:</strong> 
                            {{ $treatment->treatment ? $treatment->treatment->nombre : $treatment->tratamiento_otro }}
                        </p>
                        <p><strong>Costo Total:</strong> Bs. {{ number_format($treatment->costo, 2) }}</p>
                        <p><strong>Pagado:</strong> Bs. {{ number_format($treatment->paid, 2) }}</p>
                        <p><strong>Saldo Pendiente:</strong> <span class="text-danger font-weight-bold">
                            Bs. {{ number_format($treatment->balance, 2) }}
                        </span></p>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('payments.store') }}">
                        @csrf
                        
                        @if($treatment)
                        <input type="hidden" name="tratamiento_id" value="{{ $treatment->id }}">
                        @else
                        <div class="form-group mb-3">
                            <label for="tratamiento_id" class="form-label">Tratamiento *</label>
                            <select class="form-control @error('tratamiento_id') is-invalid @enderror" 
                                    id="tratamiento_id" name="tratamiento_id" required>
                                <option value="">Seleccione un tratamiento</option>
                                <!-- Aquí deberías cargar los tratamientos con saldo pendiente -->
                            </select>
                            @error('tratamiento_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha" class="form-label">Fecha *</label>
                                <input type="date" class="form-control @error('fecha') is-invalid @enderror" 
                                       id="fecha" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required>
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="monto" class="form-label">Monto (Bs.) *</label>
                                <input type="number" step="0.01" min="0.01" 
                                       class="form-control @error('monto') is-invalid @enderror" 
                                       id="monto" name="monto" value="{{ old('monto') }}" 
                                       @if($treatment) max="{{ $treatment->balance }}" @endif
                                       required>
                                @error('monto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($treatment)
                                <small class="form-text text-muted">
                                    Máximo permitido: Bs. {{ number_format($treatment->balance, 2) }}
                                </small>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="metodo_pago" class="form-label">Método de Pago *</label>
                                <select class="form-control @error('metodo_pago') is-invalid @enderror" 
                                        id="metodo_pago" name="metodo_pago" required>
                                    <option value="">Seleccione método</option>
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

                            <div class="col-md-6 mb-3">
                                <label for="comprobante" class="form-label">Número de Comprobante</label>
                                <input type="text" class="form-control @error('comprobante') is-invalid @enderror" 
                                       id="comprobante" name="comprobante" value="{{ old('comprobante') }}"
                                       maxlength="100">
                                @error('comprobante')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="notas" class="form-label">Notas</label>
                            <textarea class="form-control @error('notas') is-invalid @enderror" 
                                      id="notas" name="notas" rows="3" maxlength="250">{{ old('notas') }}</textarea>
                            @error('notas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('payments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Registrar Pago
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información Importante</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Instrucciones:</h6>
                        <ul class="mb-0 pl-3">
                            <li>Verifique que el monto no exceda el saldo pendiente</li>
                            <li>Registre correctamente el método de pago</li>
                            <li>Si es posible, incluya el número de comprobante</li>
                            <li>Revise la fecha antes de guardar</li>
                        </ul>
                    </div>
                    
                    @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Validación del monto
    document.getElementById('monto').addEventListener('change', function() {
        var max = parseFloat(this.max);
        var value = parseFloat(this.value);
        
        if (value > max) {
            alert('El monto no puede exceder Bs. ' + max.toFixed(2));
            this.value = max.toFixed(2);
        }
    });
</script>
@endsection