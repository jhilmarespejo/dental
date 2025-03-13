@extends('layouts.app')

@section('title', 'Detalles del Paciente - Consultorio')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-circle text-primary mr-2"></i> {{ $patient['name'] }} {{ $patient['lastname'] }}
        </h1>
        <div>
            <a href="{{ route('patients.edit', $patient['id']) }}" class="btn btn-warning mr-2">
                <i class="fas fa-edit"></i> Editar Paciente
            </a>
            <a href="{{ route('appointments.create') }}?patient_id={{ $patient['id'] }}" class="btn btn-success mr-2">
                <i class="fas fa-calendar-plus"></i> Nueva Cita
            </a>
            <button class="btn btn-primary add-treatment-btn">
                <i class="fas fa-plus-circle"></i> Nuevo Tratamiento
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Patient Information -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-id-card mr-1"></i> Información del Paciente
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="patient-avatar mx-auto">
                            <i class="fas fa-user"></i>
                        </div>
                        <h4 class="mt-3">{{ $patient['name'] }} {{ $patient['lastname'] }}</h4>
                        <p class="text-muted">Paciente desde {{ date('d/m/Y', strtotime($patient['created_at'])) }}</p>
                    </div>
                    
                    <hr>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-gray-800 font-weight-bold">Edad:</div>
                        <div class="col-7">{{ $patient['age'] }} años</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-gray-800 font-weight-bold">Fecha de Nac.:</div>
                        <div class="col-7">{{ date('d/m/Y', strtotime($patient['birthdate'])) }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-gray-800 font-weight-bold">Género:</div>
                        <div class="col-7">{{ $patient['gender'] == 'M' ? 'Masculino' : 'Femenino' }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-gray-800 font-weight-bold">CI:</div>
                        <div class="col-7">{{ $patient['ci'] }} {{ $patient['ci_exp'] }}</div>
                    </div>
                    
                    <hr>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-gray-800 font-weight-bold">Teléfono:</div>
                        <div class="col-7">
                            <a href="tel:{{ $patient['phone'] }}">{{ $patient['phone'] }}</a>
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-gray-800 font-weight-bold">Email:</div>
                        <div class="col-7">
                            <a href="mailto:{{ $patient['email'] }}">{{ $patient['email'] }}</a>
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-gray-800 font-weight-bold">Dirección:</div>
                        <div class="col-7">{{ $patient['address'] }}</div>
                    </div>
                    
                    <hr>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-gray-800 font-weight-bold">Alergias:</div>
                        <div class="col-7">
                            @if($patient['allergies'])
                                <span class="text-danger">{{ $patient['allergies'] }}</span>
                            @else
                                <span class="text-muted">Ninguna registrada</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-gray-800 font-weight-bold">Condiciones:</div>
                        <div class="col-7">
                            @if($patient['medical_conditions'])
                                <span class="text-warning">{{ $patient['medical_conditions'] }}</span>
                            @else
                                <span class="text-muted">Ninguna registrada</span>
                            @endif
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-gray-800 font-weight-bold">Última visita:</div>
                        <div class="col-7">{{ date('d/m/Y', strtotime($patient['last_visit'])) }}</div>
                    </div>
                </div>
            </div>

            <!-- Upcoming appointments -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-alt mr-1"></i> Próximas Citas
                    </h6>
                </div>
                <div class="card-body">
                    @if(count($upcoming_appointments) > 0)
                        @foreach($upcoming_appointments as $appointment)
                            <div class="border-left-primary pl-3 py-2 mb-3">
                                <div class="small text-gray-500">{{ date('d/m/Y', strtotime($appointment['date'])) }} - {{ $appointment['time'] }}</div>
                                <div class="font-weight-bold">{{ $appointment['reason'] }}</div>
                                <div class="mt-2">
                                    <a href="#" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a href="#" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No hay citas programadas</p>
                            <a href="{{ route('appointments.create') }}?patient_id={{ $patient['id'] }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-calendar-plus"></i> Programar Cita
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Treatment History -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history mr-1"></i> Historial de Tratamientos
                    </h6>
                    <a href="#" class="add-treatment-btn btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Tratamiento
                    </a>
                </div>
                <div class="card-body">
                    @if(count($treatments) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Diagnóstico</th>
                                        <th>Tratamiento</th>
                                        <th>Pieza</th>
                                        <th>Costo</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($treatments as $treatment)
                                    <tr>
                                        <td>{{ date('d/m/Y', strtotime($treatment['date'])) }}</td>
                                        <td>{{ $treatment['diagnosis'] }}</td>
                                        <td>{{ $treatment['treatment'] }}</td>
                                        <td>{{ $treatment['tooth'] }}</td>
                                        <td>Bs. {{ number_format($treatment['cost'], 2) }}</td>
                                        <td>
                                            @if($treatment['status'] == 'Completado')
                                                <span class="badge bg-success text-white">{{ $treatment['status'] }}</span>
                                            @elseif($treatment['status'] == 'En proceso')
                                                <span class="badge bg-info text-white">{{ $treatment['status'] }}</span>
                                            @else
                                                <span class="badge bg-warning text-white">{{ $treatment['status'] }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group" aria-label="Treatment actions">
                                                <button class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Registrar pago">
                                                    <i class="fas fa-dollar-sign"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-4x text-gray-300 mb-3"></i>
                            <p class="text-muted">No hay tratamientos registrados</p>
                            <button class="btn btn-primary add-treatment-btn">
                                <i class="fas fa-plus-circle"></i> Nuevo Tratamiento
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Dental Chart -->
            {{-- <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tooth mr-1"></i> Odontograma
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <img src="https://odonto.info/uploads/odonto/files/2016/09/Odontograma.png" class="img-fluid" alt="Odontograma" style="max-height: 400px;">
                    </div>
                    <div class="text-center">
                        <button class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar Odontograma
                        </button>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Los scripts para las funcionalidades de esta página se inicializan en app.js
</script>
@endpush