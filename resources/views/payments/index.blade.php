@extends('layouts.app')

@section('title', 'Pagos')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3">Gestión de Pagos</h1>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Recaudado</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Bs. {{ number_format($stats['total'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Hoy</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Bs. {{ number_format($stats['today'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Esta Semana</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Bs. {{ number_format($stats['week'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Este Mes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Bs. {{ number_format($stats['month'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros de Búsqueda</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('payments.index') }}">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">Buscar Paciente</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ $search }}" placeholder="Nombre o apellido del paciente">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="date_from" class="form-label">Desde</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="date_to" class="form-label">Hasta</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="method" class="form-label">Método de Pago</label>
                        <select class="form-control" id="method" name="method">
                            <option value="">Todos</option>
                            <option value="efectivo" {{ $method == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                            <option value="tarjeta" {{ $method == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                            <option value="transferencia" {{ $method == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                            <option value="otro" {{ $method == 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Pagos -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Pagos</h6>
            <a href="{{ route('payments.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> Nuevo Pago
            </a>
        </div>
        <div class="card-body">
            @if($formattedPayments->isEmpty())
                <div class="alert alert-info">
                    No se encontraron pagos con los filtros aplicados.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Paciente</th>
                                <th>Tratamiento</th>
                                <th>Monto</th>
                                <th>Método</th>
                                <th>Comprobante</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formattedPayments as $payment)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($payment['date'])->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('patients.show', $payment['patient_id']) }}">
                                        {{ $payment['patient_name'] }}
                                    </a>
                                </td>
                                <td>{{ $payment['treatment_name'] }}</td>
                                <td class="text-success font-weight-bold">Bs. {{ number_format($payment['amount'], 2) }}</td>
                                <td>{{ $payment['method'] }}</td>
                                <td>{{ $payment['reference'] ?: 'N/A' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('payments.show', $payment['id']) }}" 
                                           class="btn btn-info btn-sm" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('payments.edit', $payment['id']) }}" 
                                           class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('payments.destroy', $payment['id']) }}" 
                                              method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                    title="Eliminar"
                                                    onclick="return confirm('¿Está seguro de eliminar este pago?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right">Total:</th>
                                <th class="text-success">Bs. {{ number_format($formattedPayments->sum('amount'), 2) }}</th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <!-- Paginación -->
                <div class="d-flex justify-content-center">
                    {{ $payments->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Estadísticas por método -->
    <div class="card shadow mt-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Distribución por Método de Pago</h6>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($stats['methods'] as $method => $total)
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6 class="card-title">
                                {{ $method == 'efectivo' ? 'Efectivo' : 
                                   ($method == 'tarjeta' ? 'Tarjeta' : 
                                   ($method == 'transferencia' ? 'Transferencia' : 'Otro')) }}
                            </h6>
                            <h4 class="text-primary">Bs. {{ number_format($total, 2) }}</h4>
                            <p class="text-muted">
                                {{ number_format(($total / $stats['total']) * 100, 1) }}%
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Inicializar tooltips
    $(document).ready(function() {
        $('[title]').tooltip();
    });
</script>
@endsection