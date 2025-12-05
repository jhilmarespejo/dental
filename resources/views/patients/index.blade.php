@extends('layouts.app')

@section('title', 'Pacientes - Consultorio Dental')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pacientes</h1>
        <div>
            <a href="{{ route('patients.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus me-1"></i> Nuevo Paciente
            </a>
        </div>
    </div>

    <!-- Search Box -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-body">
                    <form action="{{ route('patients.index') }}" method="GET" id="search-patient-form">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" name="search" id="search-patient-input" class="form-control" 
                                        placeholder="Buscar por nombre, apellido, CI o teléfono" value="{{ $search ?? '' }}">
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                @if(!empty($search))
                                    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Limpiar filtros
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Patients List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-users me-1"></i> Lista de Pacientes
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
            @if($patients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover datatable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Edad</th>
                                <th>Teléfono</th>
                                <th>Última visita</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patients as $patient)
                            <tr>
                                <td>{{ $patient->id }}</td>
                                <td>{{ $patient->nombres }}</td>
                                <td>{{ $patient->apellidos }}</td>
                                <td>{{ $patient->edad }}</td>
                                <td>{{ $patient->celular ?? 'No registrado' }}</td>
                                <td>{{ $patient->fecha_ultima_visita ? date('d/m/Y', strtotime($patient->fecha_ultima_visita)) : 'Nunca' }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group" aria-label="Patient actions">
                                        <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('treatments.create', ['patient_id' => $patient->id]) }}" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Nuevo tratamiento">
                                            <i class="fas fa-plus-circle"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger delete-patient-btn" data-patient-id="{{ $patient->id }}" data-bs-toggle="tooltip" title="Eliminar">
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
                    {{ $patients->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-4x text-gray-300 mb-3"></i>
                    <p class="text-muted">No se encontraron pacientes</p>
                    <a href="{{ route('patients.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Registrar Paciente
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Patient Statistics -->
    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-pie me-1"></i> Pacientes por edad
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie" style="height: 300px;">
                        <canvas id="patients-by-age-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-clipboard-list me-1"></i> Resumen de pacientes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-primary text-white shadow">
                                <div class="card-body">
                                    <div class="text-xs fw-bold text-uppercase mb-1">Total Pacientes</div>
                                    <div class="h5 mb-0 fw-bold">{{ $stats['total_patients'] }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-success text-white shadow">
                                <div class="card-body">
                                    <div class="text-xs fw-bold text-uppercase mb-1">Nuevos este mes</div>
                                    <div class="h5 mb-0 fw-bold">{{ $stats['new_patients_month'] }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-info text-white shadow">
                                <div class="card-body">
                                    <div class="text-xs fw-bold text-uppercase mb-1">Citas activas</div>
                                    <div class="h5 mb-0 fw-bold">{{ $stats['active_appointments'] }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-warning text-white shadow">
                                <div class="card-body">
                                    <div class="text-xs fw-bold text-uppercase mb-1">Tratamientos pendientes</div>
                                    <div class="h5 mb-0 fw-bold">{{ $stats['pending_treatments'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Patient Modal -->
    <div class="modal fade" id="deletePatientModal" tabindex="-1" aria-labelledby="deletePatientModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePatientModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea eliminar este paciente? Esta acción no se puede deshacer.</p>
                    <p><strong>Nota:</strong> No se puede eliminar un paciente que tenga citas o tratamientos asociados.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deletePatientForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .chart-pie {
        position: relative;
        height: 15rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar el gráfico de pacientes por edad
        const ageChartEl = document.getElementById('patients-by-age-chart');
        if (ageChartEl) {
            const ageGroups = @json($ageGroups);
            
            const ageChart = new Chart(ageChartEl, {
                type: 'pie',
                data: {
                    labels: Object.keys(ageGroups),
                    datasets: [{
                        data: Object.values(ageGroups),
                        backgroundColor: [
                            '#4e73df',
                            '#1cc88a',
                            '#36b9cc',
                            '#f6c23e',
                            '#e74a3b'
                        ],
                        hoverBackgroundColor: [
                            '#2e59d9',
                            '#17a673',
                            '#2c9faf',
                            '#f4b619',
                            '#e02d1b'
                        ],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
        
        // Manejar eliminación de paciente
        const deleteButtons = document.querySelectorAll('.delete-patient-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const patientId = this.getAttribute('data-patient-id');
                const form = document.getElementById('deletePatientForm');
                form.action = `/patients/${patientId}`;
                
                const deleteModal = new bootstrap.Modal(document.getElementById('deletePatientModal'));
                deleteModal.show();
            });
        });
        
        // Exportar a CSV
        document.getElementById('exportCsvBtn').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = "{{ route('patients.index') }}?format=csv";
        });
        
        // Imprimir lista
        document.getElementById('printBtn').addEventListener('click', function(e) {
            e.preventDefault();
            window.print();
        });
    });
</script>
@endpush