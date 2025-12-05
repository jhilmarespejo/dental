@extends('layouts.app')

@section('title', 'Detalles del Paciente - Consultorio Dental')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-circle text-primary me-2"></i> {{ $patient->nombres }} {{ $patient->apellidos }}
        </h1>
        <div>
            <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit"></i> Editar Paciente
            </a>
            <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="btn btn-success me-2">
                <i class="fas fa-calendar-plus"></i> Nueva Cita
            </a>
            <a href="{{ route('treatments.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Nuevo Tratamiento
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Patient Information -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-id-card me-1"></i> Información del Paciente
                    </h6>
                    <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="patient-avatar mx-auto rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 3rem;">
                            {{ substr($patient->nombres, 0, 1) }}{{ substr($patient->apellidos, 0, 1) }}
                        </div>
                        <h4 class="mt-3">{{ $patient->nombres }} {{ $patient->apellidos }}</h4>
                        <p class="text-muted">Paciente desde {{ $patient->created_at->format('d/m/Y') }}</p>
                    </div>
                    
                    <hr>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-gray-800 fw-bold">Edad:</div>
                        <div class="col-7">{{ $patient->edad }} años</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-gray-800 fw-bold">Fecha de Nac.:</div>
                        {{-- <div class="col-7">/*{{ $patient->fecha_nacimiento }}*/</div> --}}
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-gray-800 fw-bold">Género:</div>
                        <div class="col-7">
                            @if($patient->genero == 'M')
                                Masculino
                            @elseif($patient->genero == 'F')
                                Femenino
                            @else
                                {{ $patient->genero }}
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-gray-800 fw-bold">CI:</div>
                        <div class="col-7">{{ $patient->ci ?? 'No registrado' }} {{ $patient->ci_exp }}</div>
                    </div>
                    
                    <hr>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-gray-800 fw-bold">Teléfono:</div>
                        <div class="col-7">
                            @if($patient->celular)
                                <a href="tel:{{ $patient->celular }}">{{ $patient->celular }}</a>
                            @else
                                <span class="text-muted">No registrado</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-gray-800 fw-bold">Email:</div>
                        <div class="col-7">
                            @if($patient->email)
                                <a href="mailto:{{ $patient->email }}">{{ $patient->email }}</a>
                            @else
                                <span class="text-muted">No registrado</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-gray-800 fw-bold">Dirección:</div>
                        <div class="col-7">{{ $patient->direccion ?? 'No registrada' }}</div>
                    </div>
                    
                    <hr>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-gray-800 fw-bold">Alergias:</div>
                        <div class="col-7">
                            @if($patient->alergias)
                                <span class="text-danger">{{ $patient->alergias }}</span>
                            @else
                                <span class="text-muted">Ninguna registrada</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-gray-800 fw-bold">Condiciones:</div>
                        <div class="col-7">
                            @if($patient->condiciones_medicas)
                                <span class="text-warning">{{ $patient->condiciones_medicas }}</span>
                            @else
                                <span class="text-muted">Ninguna registrada</span>
                            @endif
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row mb-2">
                        <div class="col-5 text-gray-800 fw-bold">Última visita:</div>
                        <div class="col-7">
                            @if($patient->fecha_ultima_visita)
                                {{ $patient->fecha_ultima_visita}}
                                {{-- <small class="text-muted">({{ $patient->fecha_ultima_visita->diffForHumans() }})</small> --}}
                            @else
                                <span class="text-muted">Nunca</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming appointments -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-calendar-alt me-1"></i> Próximas Citas
                    </h6>
                    <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus"></i> Nueva
                    </a>
                </div>
                <div class="card-body">
                    @if(count($upcomingAppointments) > 0)
                        @foreach($upcomingAppointments as $appointment)
                            <div class="border-start border-3 border-primary ps-3 py-2 mb-3">
                                <div class="small text-gray-500">{{ date('d/m/Y', strtotime($appointment['date'])) }} - {{ $appointment['time'] }}</div>
                                <div class="fw-bold">{{ $appointment['reason'] }}</div>
                                <div class="mt-2">
                                    <a href="{{ route('appointments.show', $appointment['id']) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a href="{{ route('appointments.edit', $appointment['id']) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No hay citas programadas</p>
                            <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="btn btn-sm btn-primary">
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
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-history me-1"></i> Historial de Tratamientos
                    </h6>
                    <a href="{{ route('treatments.create', ['patient_id' => $patient->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Tratamiento
                    </a>
                </div>
                <div class="card-body">
                    @if(count($treatments) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover datatable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
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
                                    @foreach($treatments as $treatment)
                                    <tr>
                                        <td>{{ date('d/m/Y', strtotime($treatment['date'])) }}</td>
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
                            <a href="{{ route('treatments.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle"></i> Nuevo Tratamiento
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-money-bill-wave me-1"></i> Resumen Financiero
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary text-white shadow h-100">
                                <div class="card-body">
                                    <div class="text-xs fw-bold text-uppercase mb-1">Total Tratamientos</div>
                                    @php
                                        $totalTreatments = count($treatments);
                                        $totalCost = array_sum(array_column($treatments, 'cost'));
                                        $totalPaid = array_sum(array_column($treatments, 'paid'));
                                        $totalBalance = array_sum(array_column($treatments, 'balance'));
                                    @endphp
                                    <div class="h5 mb-0 fw-bold">{{ $totalTreatments }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white shadow h-100">
                                <div class="card-body">
                                    <div class="text-xs fw-bold text-uppercase mb-1">Total Facturado</div>
                                    <div class="h5 mb-0 fw-bold">Bs. {{ number_format($totalCost, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-info text-white shadow h-100">
                                <div class="card-body">
                                    <div class="text-xs fw-bold text-uppercase mb-1">Total Pagado</div>
                                    <div class="h5 mb-0 fw-bold">Bs. {{ number_format($totalPaid, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card {{ $totalBalance > 0 ? 'bg-warning' : 'bg-dark' }} text-white shadow h-100">
                                <div class="card-body">
                                    <div class="text-xs fw-bold text-uppercase mb-1">Saldo Pendiente</div>
                                    <div class="h5 mb-0 fw-bold">Bs. {{ number_format($totalBalance, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($totalBalance > 0)
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i> 
                        <strong>Atención:</strong> Este paciente tiene un saldo pendiente de Bs. {{ number_format($totalBalance, 2) }}.
                        <div class="mt-2">
                            <a href="{{ route('payments.pending') }}?patient_id={{ $patient->id }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Dental Chart -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-tooth me-1"></i> Odontograma
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <div style="width: 100%; max-width: 800px; margin: 0 auto;">
                            <!-- Odontograma using SVG -->
                            <svg viewBox="0 0 800 300" xmlns="http://www.w3.org/2000/svg" class="img-fluid">
                                <!-- Superior teeth -->
                                <g id="superiorTeeth">
                                    @for($i = 18; $i >= 11; $i--)
                                    <g class="tooth" id="tooth-{{ $i }}">
                                        <rect x="{{ 65 + (18-$i) * 40 }}" y="20" width="35" height="35" fill="white" stroke="black" stroke-width="1"/>
                                        <text x="{{ 82 + (18-$i) * 40 }}" y="42" text-anchor="middle" font-size="16">{{ $i }}</text>
                                    </g>
                                    @endfor
                                    
                                    @for($i = 21; $i <= 28; $i++)
                                    <g class="tooth" id="tooth-{{ $i }}">
                                        <rect x="{{ 385 + ($i-21) * 40 }}" y="20" width="35" height="35" fill="white" stroke="black" stroke-width="1"/>
                                        <text x="{{ 402 + ($i-21) * 40 }}" y="42" text-anchor="middle" font-size="16">{{ $i }}</text>
                                    </g>
                                    @endfor
                                </g>
                                
                                <!-- Inferior teeth -->
                                <g id="inferiorTeeth">
                                    @for($i = 48; $i >= 41; $i--)
                                    <g class="tooth" id="tooth-{{ $i }}">
                                        <rect x="{{ 65 + (48-$i) * 40 }}" y="170" width="35" height="35" fill="white" stroke="black" stroke-width="1"/>
                                        <text x="{{ 82 + (48-$i) * 40 }}" y="192" text-anchor="middle" font-size="16">{{ $i }}</text>
                                    </g>
                                    @endfor
                                    
                                    @for($i = 31; $i <= 38; $i++)
                                    <g class="tooth" id="tooth-{{ $i }}">
                                        <rect x="{{ 385 + ($i-31) * 40 }}" y="170" width="35" height="35" fill="white" stroke="black" stroke-width="1"/>
                                        <text x="{{ 402 + ($i-31) * 40 }}" y="192" text-anchor="middle" font-size="16">{{ $i }}</text>
                                    </g>
                                    @endfor
                                </g>
                                
                                <!-- Tooth status -->
                                @foreach($treatments as $treatment)
                                    @php
                                    $toothNumber = $treatment['tooth'];
                                    @endphp
                                    
                                    @if(is_numeric($toothNumber) && $toothNumber >= 11 && $toothNumber <= 48)
                                        @php
                                        // Determine position
                                        $x = 0;
                                        $y = 0;
                                        
                                        if($toothNumber >= 11 && $toothNumber <= 18) {
                                            $x = 82 + (18-$toothNumber) * 40;
                                            $y = 20;
                                        } elseif($toothNumber >= 21 && $toothNumber <= 28) {
                                            $x = 402 + ($toothNumber-21) * 40;
                                            $y = 20;
                                        } elseif($toothNumber >= 31 && $toothNumber <= 38) {
                                            $x = 402 + ($toothNumber-31) * 40;
                                            $y = 170;
                                        } elseif($toothNumber >= 41 && $toothNumber <= 48) {
                                            $x = 82 + (48-$toothNumber) * 40;
                                            $y = 170;
                                        }
                                        
                                        // Determine color based on treatment status
                                        $color = '#1cc88a'; // success - default
                                        if($treatment['status'] == 'En proceso') {
                                            $color = '#36b9cc'; // info
                                        } elseif($treatment['status'] == 'Pendiente') {
                                            $color = '#f6c23e'; // warning
                                        }
                                        @endphp
                                        
                                        <rect x="{{ $x - 17.5 }}" y="{{ $y }}" width="35" height="35" fill="{{ $color }}" stroke="black" stroke-width="1" fill-opacity="0.5"/>
                                        <text x="{{ $x }}" y="{{ $y + 22 }}" text-anchor="middle" font-size="16">{{ $toothNumber }}</text>
                                    @endif
                                @endforeach
                                
                                <!-- Legend -->
                                <g id="legend" transform="translate(350, 240)">
                                    <rect x="0" y="0" width="20" height="20" fill="#1cc88a" fill-opacity="0.5" stroke="black"/>
                                    <text x="25" y="15" font-size="12">Tratamiento Completado</text>
                                    
                                    <rect x="200" y="0" width="20" height="20" fill="#36b9cc" fill-opacity="0.5" stroke="black"/>
                                    <text x="225" y="15" font-size="12">En Proceso</text>
                                    
                                    <rect x="0" y="30" width="20" height="20" fill="#f6c23e" fill-opacity="0.5" stroke="black"/>
                                    <text x="25" y="45" font-size="12">Pendiente</text>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Inicializar DataTable con opciones personalizadas
        $('.datatable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
            },
            order: [[0, 'desc']], // Ordenar por fecha (primera columna) descendente
            pageLength: 5, // Mostrar 5 filas por página
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]]
        });
    });
</script>
@endpush