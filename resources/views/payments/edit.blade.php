@extends('layouts.app')

@section('title', 'Editar Pago')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3">Editar Pago</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Editar Información del Pago</h6>
                </div>
                <div class="card-body">
                    <!-- Información del tratamiento -->
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading">Tratamiento Asociado</h6>
                        <p><strong>Paciente:</strong> {{ $payment->treatmentPerformed->patient->nombres }} 
                           {{ $payment->treatmentPerformed->patient->apellidos }}</p>
                        <p><strong>Costo Total:</strong> Bs. {{ number_format($payment->treatmentPerformed->costo, 2) }}</p>
                        @php
                            $paidExcludingThis = $payment->treatmentPerformed->payments
                                ->where('id', '!=', $payment->id)
                                ->sum('monto');
                            $balanceExcludingThis = $payment->treatmentPerformed->costo - $paidExcludingThis;
                        @endphp
                        <p><strong>Saldo disponible para modificar:</strong> 
                           <span class="text-primary font-weight-bold">
                               Bs. {{ number_format($balanceExcludingThis, 2) }}
                           </span></p>
                    </div>

                    <form method="POST" action="{{ route('payments.update', $payment->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha" class="form-label">Fecha *</label>
                                <input type="date" class="form-control @error('fecha') is-invalid @enderror" 
                                       id="fecha" name="fecha" 
                                       value="{{ old('fecha', $payment->fecha) }}" required>
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="monto" class="form-label">Monto (Bs.) *</label>
                                <input type="number" step="0.01" min="0.01" 
                                       class="form-control @error('monto') is-invalid @enderror" 
                                       id="monto" name="monto" 
                                       value="{{ old('monto', $payment->monto) }}" 
                                       max="{{ $balanceExcludingThis }}"
                                       required>
                                @error('monto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Máximo permitido: Bs. {{ number_format($balanceExcludingThis, 2) }}
                                </small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="metodo_pago" class="form-label">Método de Pago *</label>
                                <select class="form-control @error('metodo_pago') is-invalid @enderror" 
                                        id="metodo_pago" name="metodo_pago" required>
                                    <option value="">Seleccione método</option>
                                    <option value="efectivo" {{ old('metodo_pago', $payment->metodo_pago) == 'efectivo' ? 'selected' : '' }}>
                                        Efectivo
                                    </option>
                                    <option value="tarjeta" {{ old('metodo_pago', $payment->metodo_pago) == 'tarjeta' ? 'selected' : '' }}>
                                        Tarjeta de Crédito/Débito
                                    </option>
                                    <option value="transferencia" {{ old('metodo_pago', $payment->metodo_pago) == 'transferencia' ? 'selected' : '' }}>
                                        Transferencia Bancaria
                                    </option>
                                    <option value="otro" {{ old('metodo_pago', $payment->metodo_pago) == 'otro' ? 'selected' : '' }}>
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
                                       id="comprobante" name="comprobante" 
                                       value="{{ old('comprobante', $payment->comprobante) }}"
                                       maxlength="100">
                                @error('comprobante')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="notas" class="form-label">Notas</label>
                            <textarea class="form-control @error('notas') is-invalid @enderror" 
                                      id="notas" name="notas" rows="3" maxlength="250">{{ old('notas', $payment->notas) }}</textarea>
                            @error('notas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Pago
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información Original</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Fecha original:</th>
                            <td>{{ \Carbon\Carbon::parse($payment->fecha)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Monto original:</th>
                            <td>Bs. {{ number_format($payment->monto, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Método original:</th>
                            <td>
                                @php
                                    $methods = [
                                        'efectivo' => 'Efectivo',
                                        'tarjeta' => 'Tarjeta',
                                        'transferencia' => 'Transferencia',
                                        'otro' => 'Otro'
                                    ];
                                @endphp
                                {{ $methods[$payment->metodo_pago] ?? $payment->metodo_pago }}
                            </td>
                        </tr>
                        <tr>
                            <th>Comprobante original:</th>
                            <td>{{ $payment->comprobante ?: 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if(session('error'))
            <div class="alert alert-danger mt-3">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif
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