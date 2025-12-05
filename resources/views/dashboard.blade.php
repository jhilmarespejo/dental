@extends('layouts.app')

@section('title', 'Panel - Consultorio Dental')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Panel de Control</h1>
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-download fa-sm"></i> Generar Reporte
            </a>
            <a href="{{ route('appointments.create') }}" class="btn btn-primary">
                <i class="fas fa-calendar-plus fa-sm"></i> Nueva Cita
            </a>
        </div>
    </div>

    <!-- Content Row - Stats Cards -->
    <div class="row">
        <!-- Total Patients Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2 info-card primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 info-card-title">
                                Total Pacientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 info-card-value">{{ number_format($stats['total_patients']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Appointments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2 info-card success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 info-card-title">
                                Citas Hoy
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 info-card-value">{{ $stats['appointments_today'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Treatments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2 info-card info">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 info-card-title">
                                Tratamientos Activos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 info-card-value">{{ $stats['active_treatments'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-procedures fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2 info-card warning">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 info-card-title">
                                Ingresos Mensuales
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 info-card-value">Bs. {{ number_format($stats['revenue_month'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Charts and Tables -->
    <div class="row">
        <!-- Calendar / Today's Appointments -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-calendar-day me-1"></i> Citas de Hoy
                    </h6>
                    <a href="{{ route('appointments.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Nueva Cita
                    </a>
                </div>
                <div class="card-body">
                    @if(count($todayAppointments) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Hora</th>
                                        <th>Paciente</th>
                                        <th>Motivo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayAppointments as $appointment)
                                    <tr>
                                        <td>{{ $appointment['time'] }}</td>
                                        <td>{{ $appointment['patient'] }}</td>
                                        <td>{{ $appointment['reason'] }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('appointments.show', $appointment['id'] ?? 1) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="far fa-calendar-alt fa-4x text-gray-300 mb-3"></i>
                            <p class="text-muted">No hay citas programadas para hoy</p>
                            <a href="{{ route('appointments.create') }}" class="btn btn-primary">
                                <i class="fas fa-calendar-plus"></i> Programar Cita
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Patients Activity -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-user-clock me-1"></i> Actividad Reciente
                    </h6>
                    <a href="{{ route('patients.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-users"></i> Ver Pacientes
                    </a>
                </div>
                <div class="card-body">
                    @if(count($recentPatients) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Paciente</th>
                                        <th>Fecha</th>
                                        <th>Tratamiento</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPatients as $patient)
                                    <tr>
                                        <td>
                                            <a href="{{ route('patients.show', $patient['id']) }}">
                                                {{ $patient['name'] }}
                                            </a>
                                        </td>
                                        <td>{{ date('d/m/Y', strtotime($patient['date'])) }}</td>
                                        <td>{{ $patient['treatment'] }}</td>
                                        <td>
                                            @if($patient['status'] == 'Completado')
                                                <span class="badge bg-success">{{ $patient['status'] }}</span>
                                            @elseif($patient['status'] == 'En proceso')
                                                <span class="badge bg-info">{{ $patient['status'] }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ $patient['status'] }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-user-clock fa-4x text-gray-300 mb-3"></i>
                            <p class="text-muted">No hay actividad reciente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Charts -->
    <div class="row">
        <!-- Monthly Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-line me-1"></i> Ingresos Mensuales
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 300px;">
                        <canvas id="monthly-revenue-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Treatments by Type Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-pie me-1"></i> Tipos de Tratamientos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="treatments-by-type-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Payments Alert -->
    @if($stats['pending_payments'] > 0)
    <div class="row">
        <div class="col-12">
            <div class="card bg-warning text-white shadow mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-exclamation-triangle fa-fw"></i>
                            <strong>Atención:</strong> Hay pagos pendientes por Bs. {{ number_format($stats['pending_payments'], 2) }}
                        </div>
                        <a href="{{ route('payments.pending') }}" class="btn btn-light btn-sm">Ver Detalles</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Monthly Revenue Chart
        var revenueCtx = document.getElementById('monthly-revenue-chart');
        if (revenueCtx) {
            var revenueChart = new Chart(revenueCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_column($monthlyRevenue, 'month')) !!},
                    datasets: [{
                        label: 'Ingresos (Bs.)',
                        data: {!! json_encode(array_column($monthlyRevenue, 'revenue')) !!},
                        backgroundColor: '#4e73df',
                        borderColor: '#4e73df',
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Bs. ' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Treatments by Type Chart
        var treatmentsCtx = document.getElementById('treatments-by-type-chart');
        if (treatmentsCtx) {
            var treatmentsChart = new Chart(treatmentsCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode(array_column($treatmentTypes->toArray(), 'name')) !!},
                    datasets: [{
                        data: {!! json_encode(array_column($treatmentTypes->toArray(), 'count')) !!},
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
                        hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#e02d1b', '#717384'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    });
</script>
@endpush