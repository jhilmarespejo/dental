@extends('layouts.app')

@section('title', 'Pagos Pendientes')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3">Reporte de Pagos Pendientes</h1>
        </div>
    </div>

    <!-- Resumen -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total de Pagos Pendientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Bs. {{ number_format($totalPending, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Tratamientos con Saldo Pendiente</h6>
            <a href="{{ route('payments.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver a Pagos
            </a>
        </div>
        <div class="card-body">
            @if($pendingPayments->isEmpty())
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    ¡Excelente! No hay pagos pendientes.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Paciente</th>
                                <th>Tratamiento</th>
                                <th>Fecha Tratamiento</th>
                                <th>Costo Total</th>
                                <th>Pagado</th>
                                <th>Saldo Pendiente</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingPayments as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->paciente }}</td>
                                <td>{{ $item->tratamiento }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->fecha_tratamiento)->format('d/m/Y') }}</td>
                                <td class="text-right">Bs. {{ number_format($item->costo_total, 2) }}</td>
                                <td class="text-right text-success">
                                    Bs. {{ number_format($item->total_pagado, 2) }}
                                </td>
                                <td class="text-right text-danger font-weight-bold">
                                    Bs. {{ number_format($item->saldo_pendiente, 2) }}
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('treatments.show', $item->tratamiento_id) }}" 
                                           class="btn btn-info btn-sm" title="Ver tratamiento">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('payments.create') }}?treatment_id={{ $item->tratamiento_id }}" 
                                           class="btn btn-success btn-sm" title="Registrar pago">
                                            <i class="fas fa-plus"></i> Pago
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-right">Totales:</th>
                                <th class="text-right text-success">
                                    Bs. {{ number_format($pendingPayments->sum('total_pagado'), 2) }}
                                </th>
                                <th class="text-right text-danger">
                                    Bs. {{ number_format($pendingPayments->sum('saldo_pendiente'), 2) }}
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Distribución de saldos -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Distribución de Saldos</h6>
                            </div>
                            <div class="card-body">
                                @php
                                    $categorias = [
                                        'alto' => $pendingPayments->where('saldo_pendiente', '>', 1000)->count(),
                                        'medio' => $pendingPayments->whereBetween('saldo_pendiente', [500, 1000])->count(),
                                        'bajo' => $pendingPayments->where('saldo_pendiente', '<', 500)->count()
                                    ];
                                @endphp
                                
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Saldo Alto (> Bs. 1,000)
                                        <span class="badge badge-danger badge-pill">{{ $categorias['alto'] }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Saldo Medio (Bs. 500 - 1,000)
                                        <span class="badge badge-warning badge-pill">{{ $categorias['medio'] }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Saldo Bajo (< Bs. 500)
                                        <span class="badge badge-success badge-pill">{{ $categorias['bajo'] }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Recomendaciones</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    <h6><i class="fas fa-lightbulb"></i> Sugerencias:</h6>
                                    <ul class="mb-0 pl-3">
                                        <li>Priorice los tratamientos con saldo alto</li>
                                        <li>Contacte a pacientes con saldos pendientes mayores a 30 días</li>
                                        <li>Considere planes de pago para saldos elevados</li>
                                        <li>Actualice regularmente este reporte</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Inicializar tooltips
        $('[title]').tooltip();
        
        // Ordenar tabla
        $('#dataTable').DataTable({
            "order": [[6, "desc"]], // Ordenar por saldo pendiente descendente
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            }
        });
    });
</script>
@endsection