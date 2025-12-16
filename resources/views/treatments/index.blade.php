@extends('layouts.app')

@section('title', 'Tratamientos - Consultorio Dental')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tratamientos</h1>
        <div>
            <a href="{{ route('treatments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Nuevo Tratamiento
            </a>
        </div>
    </div>

    <!-- Search and Filters Box -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-body">
                    <form action="{{ route('treatments.index') }}" method="GET" id="search-treatment-form">
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Buscar</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" name="search" id="search" class="form-control" 
                                        placeholder="Paciente, tratamiento, diagnóstico..." value="{{ $search ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Estado</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="" {{ $status == '' ? 'selected' : '' }}>Todos</option>
                                    <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pendientes</option>
                                    <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completados</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">Fecha desde</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateFrom ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">Fecha hasta</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateTo ?? '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i> Filtrar
                                </button>
                                @if(!empty($search) || !empty($status) || !empty($dateFrom) || !empty($dateTo))
                                    <a href="{{ route('treatments.index') }}" class="btn btn-outline-secondary ms-2">
                                        <i class="fas fa-times me-1"></i> Limpiar filtros
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Treatments List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-procedures me-1"></i> Lista de Tratamientos
            </h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow animated--fade-in"
                    aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Opciones:</div>
                    <a class="dropdown-item" href="#" id="exportCsvBtn">
                        <i class="fas fa-file-csv fa-sm fa-fw me-2 text-gray-400"></i> Exportar a CSV
                    </a>
                    <a class="dropdown-item" href="#" id="printBtn">
                        <i class="fas fa-print fa-sm fa-fw me-2 text-gray-400"></i> Imprimir lista
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($formattedTreatments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover datatable_tratments" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Paciente</th>
                                <th>Diagnóstico</th>
                                <th>Tratamiento</th>
                                <th>Pieza</th>
                                <th>Costo</th>
                                <th>Pagado</th>
                                <th>Saldo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formattedTreatments as $treatment)
                            <tr>
                                <td>{{ date('d/m/Y', strtotime($treatment['date'])) }}</td>
                                <td>
                                    <a href="{{ route('patients.show', $treatment['patient_id']) }}">
                                        {{ $treatment['patient_name'] }}
                                    </a>
                                </td>
                                <td>{{ $treatment['diagnosis'] }}</td>
                                <td>{{ $treatment['treatment'] }}</td>
                                <td>{{ $treatment['tooth'] }}</td>
                                <td>Bs. {{ number_format($treatment['cost'], 2) }}</td>
                                <td>Bs. {{ number_format($treatment['paid'], 2) }}</td>
                                <td>Bs. {{ number_format($treatment['balance'], 2) }}</td>
                                <td>
                                    @if($treatment['status'] == 'Completado')
                                        <span class="badge bg-success text-white">{{ $treatment['status'] }}</span>
                                    @elseif($treatment['status'] == 'En proceso')
                                        <span class="badge bg-info text-white">{{ $treatment['status'] }}</span>
                                    @else
                                        <span class="badge bg-warning">{{ $treatment['status'] }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group" aria-label="Treatment actions">
                                        <a href="{{ route('treatments.show', $treatment['id']) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('treatments.edit', $treatment['id']) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($treatment['balance'] > 0)
                                        <a href="{{ route('payments.create', ['treatment_id' => $treatment['id']]) }}" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Registrar pago">
                                            <i class="fas fa-dollar-sign"></i>
                                        </a>
                                        @endif
                                        <button class="btn btn-sm btn-danger delete-treatment-btn" data-treatment-id="{{ $treatment['id'] }}" data-bs-toggle="tooltip" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    {{ $treatments->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-procedures fa-4x text-gray-300 mb-3"></i>
                    <p class="text-muted">No se encontraron tratamientos</p>
                    <a href="{{ route('treatments.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Registrar Tratamiento
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="row">
        <div class="col-xl-6 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-bar me-1"></i> Resumen Financiero
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $totalCost = array_sum(array_column($formattedTreatments->toArray(), 'cost'));
                        $totalPaid = array_sum(array_column($formattedTreatments->toArray(), 'paid'));
                        $totalBalance = array_sum(array_column($formattedTreatments->toArray(), 'balance'));
                        
                        $completedCount = count(array_filter($formattedTreatments->toArray(), function($t) {
                            return $t['status'] == 'Completado';
                        }));
                        
                        $inProgressCount = count(array_filter($formattedTreatments->toArray(), function($t) {
                            return $t['status'] == 'En proceso';
                        }));
                        
                        $pendingCount = count(array_filter($formattedTreatments->toArray(), function($t) {
                            return $t['status'] == 'Pendiente';
                        }));
                    @endphp
                    
                    <div class="row">
                        {{-- <div class="col-md-4 mb-3">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Facturado
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Bs. {{ number_format($totalCost, 2) }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total Pagado
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Bs. {{ number_format($totalPaid, 2) }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Saldo Pendiente
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Bs. {{ number_format($totalBalance, 2) }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <canvas id="status-chart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-calendar-alt me-1"></i> Tratamientos por Mes
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="monthly-treatments-chart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Treatment Modal -->
    <div class="modal fade" id="deleteTreatmentModal" tabindex="-1" aria-labelledby="deleteTreatmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteTreatmentModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea eliminar este tratamiento? Esta acción no se puede deshacer.</p>
                    <p><strong>Nota:</strong> No se puede eliminar un tratamiento que tenga pagos asociados.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deleteTreatmentForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar DataTable con opciones personalizadas
        $('.datatable_treatments').DataTable({
            destroy: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
            },
            order: [[0, 'desc']], // Ordenar por fecha (primera columna) descendente
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            // Deshabilitar ordenamiento para la columna de acciones
            columnDefs: [
                { orderable: false, targets: -1 } // Última columna
            ]
        });
        
        // Manejar eliminación de tratamiento
        const deleteButtons = document.querySelectorAll('.delete-treatment-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const treatmentId = this.getAttribute('data-treatment-id');
                const form = document.getElementById('deleteTreatmentForm');
                form.action = `/treatments/${treatmentId}`;
                
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteTreatmentModal'));
                deleteModal.show();
            });
        });
        
        // Exportar a CSV
        document.getElementById('exportCsvBtn').addEventListener('click', function(e) {
            e.preventDefault();
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('format', 'csv');
            window.location.href = currentUrl.toString();
        });
        
        // Imprimir lista
        document.getElementById('printBtn').addEventListener('click', function(e) {
            e.preventDefault();
            window.print();
        });
        
        // Gráfico de estados
        const statusChart = document.getElementById('status-chart');
        if (statusChart) {
            new Chart(statusChart, {
                type: 'doughnut',
                data: {
                    labels: ['Completados', 'En Proceso', 'Pendientes'],
                    datasets: [{
                        data: [{{ $completedCount }}, {{ $inProgressCount }}, {{ $pendingCount }}],
                        backgroundColor: ['#1cc88a', '#36b9cc', '#f6c23e'],
                        hoverBackgroundColor: ['#17a673', '#2c9faf', '#f4b619'],
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
        
        // Tratamientos por mes (últimos 6 meses)
        const monthlyTreatmentsChart = document.getElementById('monthly-treatments-chart');
        if (monthlyTreatmentsChart) {
            // Obtener datos para el gráfico mensual mediante AJAX
            $.ajax({
                url: '/api/treatments/monthly',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    new Chart(monthlyTreatmentsChart, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Tratamientos',
                                data: data.counts,
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
                                        stepSize: 1
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
                },
                error: function() {
                    // Si falla la llamada AJAX, mostrar un gráfico con datos de ejemplo
                    new Chart(monthlyTreatmentsChart, {
                        type: 'bar',
                        data: {
                            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio'],
                            datasets: [{
                                label: 'Tratamientos',
                                data: [10, 15, 12, 18, 20, 15],
                                backgroundColor: '#4e73df',
                                borderColor: '#4e73df',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
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
            });
        }
    });
</script>
@endpush