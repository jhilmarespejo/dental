@extends('layouts.app')

@section('title', 'Detalles del Pago')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3">Detalles del Pago</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Información del Pago</h6>
                    <div class="btn-group">
                        <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('¿Está seguro de eliminar este pago?')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información General</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Fecha:</th>
                                    <td>{{ \Carbon\Carbon::parse($payment->fecha)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Monto:</th>
                                    <td class="text-success font-weight-bold">
                                        Bs. {{ number_format($payment->monto, 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Método de Pago:</th>
                                    <td>
                                        @php
                                            $methods = [
                                                'efectivo' => 'Efectivo',
                                                'tarjeta' => 'Tarjeta de Crédito/Débito',
                                                'transferencia' => 'Transferencia Bancaria',
                                                'otro' => 'Otro'
                                            ];
                                        @endphp
                                        {{ $methods[$payment->metodo_pago] ?? $payment->metodo_pago }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Comprobante:</th>
                                    <td>{{ $payment->comprobante ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Saldo después del pago:</th>
                                    <td class="{{ $balanceAfter > 0 ? 'text-warning' : 'text-success' }} font-weight-bold">
                                        Bs. {{ number_format($balanceAfter, 2) }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Información del Tratamiento</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Paciente:</th>
                                    <td>
                                        <a href="{{ route('patients.show', $payment->treatmentPerformed->patient->id) }}">
                                            {{ $payment->treatmentPerformed->patient->nombres }} 
                                            {{ $payment->treatmentPerformed->patient->apellidos }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Diagnóstico:</th>
                                    <td>{{ $payment->treatmentPerformed->diagnosis->nombre ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Tratamiento:</th>
                                    <td>
                                        {{ $payment->treatmentPerformed->treatment->nombre ?? 
                                           $payment->treatmentPerformed->tratamiento_otro }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Costo Total:</th>
                                    <td>Bs. {{ number_format($payment->treatmentPerformed->costo, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha Tratamiento:</th>
                                    <td>
                                        {{ \Carbon\Carbon::parse($payment->treatmentPerformed->fecha)->format('d/m/Y') }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($payment->notas)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>Notas Adicionales</h5>
                            <div class="card">
                                <div class="card-body">
                                    {{ $payment->notas }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer">
                    <small class="text-muted">
                        Registrado el {{ $payment->created_at->format('d/m/Y H:i') }}
                        @if($payment->updated_at != $payment->created_at)
                            | Última actualización: {{ $payment->updated_at->format('d/m/Y H:i') }}
                        @endif
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Acciones rápidas -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Acciones Rápidas</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('treatments.show', $payment->tratamiento_id) }}" 
                           class="btn btn-info">
                            <i class="fas fa-stethoscope"></i> Ver Tratamiento
                        </a>
                        <a href="{{ route('payments.create') }}?treatment_id={{ $payment->tratamiento_id }}" 
                           class="btn btn-success">
                            <i class="fas fa-plus-circle"></i> Nuevo Pago para este Tratamiento
                        </a>
                        <a href="{{ route('payments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list"></i> Volver a la Lista
                        </a>
                    </div>
                </div>
            </div>

            <!-- Información de estado -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Estado del Tratamiento</h6>
                </div>
                <div class="card-body">
                    @php
                        $totalPagado = $payment->treatmentPerformed->payments->sum('monto');
                        $saldoRestante = $payment->treatmentPerformed->costo - $totalPagado;
                        $porcentaje = ($totalPagado / $payment->treatmentPerformed->costo) * 100;
                    @endphp
                    
                    <div class="mb-3">
                        <h6>Progreso de Pago</h6>
                        <div class="progress">
                            <div class="progress-bar {{ $porcentaje == 100 ? 'bg-success' : 'bg-info' }}" 
                                 role="progressbar" style="width: {{ $porcentaje }}%">
                                {{ number_format($porcentaje, 1) }}%
                            </div>
                        </div>
                    </div>
                    
                    <table class="table table-sm">
                        <tr>
                            <th>Costo Total:</th>
                            <td class="text-right">Bs. {{ number_format($payment->treatmentPerformed->costo, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Total Pagado:</th>
                            <td class="text-right text-success">
                                Bs. {{ number_format($totalPagado, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <th>Saldo Pendiente:</th>
                            <td class="text-right {{ $saldoRestante > 0 ? 'text-danger' : 'text-success' }}">
                                <strong>Bs. {{ number_format($saldoRestante, 2) }}</strong>
                            </td>
                        </tr>
                    </table>
                    
                    @if($saldoRestante > 0)
                    <div class="alert alert-warning mt-3">
                        <small>
                            <i class="fas fa-exclamation-circle"></i>
                            Este tratamiento aún tiene un saldo pendiente de 
                            Bs. {{ number_format($saldoRestante, 2) }}
                        </small>
                    </div>
                    @else
                    <div class="alert alert-success mt-3">
                        <small>
                            <i class="fas fa-check-circle"></i>
                            Este tratamiento ha sido completamente pagado.
                        </small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection