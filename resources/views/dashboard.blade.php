@extends('layouts.app')

@section('title', 'Dashboard - Consultorio')

<style>
.content-wrapper {
    margin-left: 14rem;
    transition: all 0.2s;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    width: calc(100% - 14rem); /* Asegurar que ocupe todo el espacio restante */
}

.content-wrapper.toggled {
    margin-left: 6.5rem;
    width: calc(100% - 6.5rem); /* Ajustar el ancho cuando el sidebar está colapsado */
}
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(0);
        transition: transform 0.3s ease-in-out;
    }
    
    .sidebar.mobile-hidden {
        transform: translateX(-100%);
    }
    
    .content-wrapper {
        margin-left: 6.5rem;
        width: calc(100% - 6.5rem);
        transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
    }
    
    .content-wrapper.full-width {
        margin-left: 0;
        width: 100%;
    }
}
</style>

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Panel</h1>
        <div>
            {{-- <a href="{{ route('reports.index') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-download fa-sm"></i> Generar Reporte
            </a> --}}
            <a href="#" class="btn btn-primary new-appointment-btn">
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800 info-card-value">{{ $stats['total_patients'] }}</div>
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
                    <a href="#" class="btn btn-sm btn-primary new-appointment-btn">
                        <i class="fas fa-plus"></i> Nueva Cita
                    </a>
                </div>
                <div class="card-body">
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
                                @foreach($today_appointments as $appointment)
                                <tr>
                                    <td>{{ $appointment['time'] }}</td>
                                    <td>{{ $appointment['patient'] }}</td>
                                    <td>{{ $appointment['reason'] }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
                                @foreach($recent_patients as $patient)
                                <tr>
                                    <td>
                                        <a href="{{ route('patients.show', $patient['id']) }}">
                                            {{ $patient['name'] }}
                                        </a>
                                    </td>
                                    <td>{{ $patient['date'] }}</td>
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
    <div class="row">
        <div class="col-12">
            <div class="card bg-warning text-white shadow mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-exclamation-triangle fa-fw"></i>
                            <strong>Atención:</strong> Hay pagos pendientes por Bs. {{ number_format($stats['pending_payments'], 2) }}
                        </div>
                        <button class="btn btn-light btn-sm" id="view-pending-payments-btn">Ver Detalles</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Pending Payments Modal -->
    @include('partials.payments-modal')
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Botón de pagos pendientes
        document.getElementById('view-pending-payments-btn').addEventListener('click', function() {
            const pendingPaymentsModal = new bootstrap.Modal(document.getElementById('pendingPaymentsModal'));
            pendingPaymentsModal.show();
        });
        
        // Botones de registrar pago
        document.querySelectorAll('.register-payment-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const patientId = this.getAttribute('data-patient-id');
                const treatmentId = this.getAttribute('data-treatment-id');
                
                // Cerrar el modal de pagos pendientes
                const pendingPaymentsModal = bootstrap.Modal.getInstance(document.getElementById('pendingPaymentsModal'));
                pendingPaymentsModal.hide();
                
                // Abrir el modal de registro de pago
                document.getElementById('payment-patient-id').value = patientId;
                document.getElementById('payment-treatment-id').value = treatmentId;
                
                const registerPaymentModal = new bootstrap.Modal(document.getElementById('registerPaymentModal'));
                registerPaymentModal.show();
            });
        });
        
        // Guardar pago
        document.getElementById('savePaymentBtn').addEventListener('click', function() {
            const registerPaymentModal = bootstrap.Modal.getInstance(document.getElementById('registerPaymentModal'));
            registerPaymentModal.hide();
            
            // Mostrar alerta de éxito
            const alertPlaceholder = document.createElement('div');
            alertPlaceholder.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-4" role="alert" style="z-index: 1050;">
                    <strong>¡Éxito!</strong> Pago registrado correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            document.body.appendChild(alertPlaceholder);
            
            // Eliminar alerta después de 3 segundos
            setTimeout(() => {
                alertPlaceholder.remove();
            }, 3000);
        });
        
        // Monthly Revenue Chart
        var revenueCtx = document.getElementById('monthly-revenue-chart').getContext('2d');
        var revenueChart = new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                datasets: [{
                    label: 'Ingresos (Bs.)',
                    data: [35000, 28500, 42000, 38000, 33000, 42500, 47000, 38500, 40000, 42500, 43000, {{ $stats['revenue_month'] }}],
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

        // Treatments by Type Chart
        var treatmentsCtx = document.getElementById('treatments-by-type-chart').getContext('2d');
        var treatmentsChart = new Chart(treatmentsCtx, {
            type: 'doughnut',
            data: {
                labels: ['Limpieza', 'Empastes', 'Extracciones', 'Conductos', 'Blanqueamiento', 'Otros'],
                datasets: [{
                    data: [30, 22, 17, 12, 9, 10],
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
    });
</script>
@endpush