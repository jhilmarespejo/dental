@extends('layouts.app')

@section('title', 'Gestión de Citas - Consultorio Dental')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Citas</h1>
        <a href="{{ route('appointments.create') }}" class="btn btn-primary">
            <i class="fas fa-calendar-plus"></i> Nueva Cita
        </a>
    </div>

    <!-- Content Row - Stats -->
    <div class="row mb-4">
        <!-- Total Appointments -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2 info-card primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 info-card-title">
                                Total Citas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 info-card-value">{{ number_format($stats['total']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Appointments -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2 info-card success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 info-card-title">
                                Completadas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 info-card-value">{{ number_format($stats['completed']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Appointments -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2 info-card info">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 info-card-title">
                                Pendientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 info-card-value">{{ number_format($stats['pending']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancelled Appointments -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2 info-card warning">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 info-card-title">
                                Canceladas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 info-card-value">{{ number_format($stats['cancelled']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar View -->
    <div class="row">
        <div class="col-xl-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-calendar-alt me-1"></i> Calendario de Citas
                    </h6>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" id="prev-month">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-primary" id="today-btn">Hoy</button>
                        <button class="btn btn-sm btn-outline-primary" id="next-month">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="appointments-calendar" style="min-height: 600px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's and Upcoming Appointments -->
    <div class="row">
        <!-- Today's Appointments -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-calendar-day me-1"></i> Citas de Hoy - {{ date('d/m/Y') }}
                    </h6>
                    <span class="badge bg-primary">{{ count($today) }} citas</span>
                </div>
                <div class="card-body">
                    @if(count($today) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Hora</th>
                                        <th>Paciente</th>
                                        <th>Motivo</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($today as $appointment)
                                    <tr>
                                        <td>{{ $appointment['time'] }}</td>
                                        <td>
                                            <a href="{{ route('patients.show', $appointment['patient_id']) }}">
                                                {{ $appointment['patient'] }}
                                            </a>
                                        </td>
                                        <td>{{ $appointment['reason'] }}</td>
                                        <td>
                                            @if($appointment['status'] == 'Confirmada')
                                                <span class="badge bg-success">{{ $appointment['status'] }}</span>
                                            @elseif($appointment['status'] == 'Programada')
                                                <span class="badge bg-info">{{ $appointment['status'] }}</span>
                                            @elseif($appointment['status'] == 'Completada')
                                                <span class="badge bg-secondary">{{ $appointment['status'] }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ $appointment['status'] }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('appointments.show', $appointment['id']) }}" 
                                                   class="btn btn-info"
                                                   data-bs-toggle="tooltip" 
                                                   title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('appointments.edit', $appointment['id']) }}" 
                                                   class="btn btn-warning"
                                                   data-bs-toggle="tooltip" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-check fa-4x text-gray-300 mb-3"></i>
                            <p class="text-muted">No hay citas programadas para hoy</p>
                            <a href="{{ route('appointments.create') }}" class="btn btn-primary">
                                <i class="fas fa-calendar-plus"></i> Programar Cita
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-calendar-week me-1"></i> Próximas Citas (7 días)
                    </h6>
                    <span class="badge bg-primary">{{ count($upcoming) }} citas</span>
                </div>
                <div class="card-body">
                    @if(count($upcoming) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Paciente</th>
                                        <th>Motivo</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcoming as $appointment)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($appointment['date'])->format('d/m/Y') }}</td>
                                        <td>{{ $appointment['time'] }}</td>
                                        <td>
                                            <a href="{{ route('patients.show', $appointment['patient_id']) }}">
                                                {{ $appointment['patient'] }}
                                            </a>
                                        </td>
                                        <td>{{ $appointment['reason'] }}</td>
                                        <td>
                                            @if($appointment['status'] == 'Confirmada')
                                                <span class="badge bg-success">{{ $appointment['status'] }}</span>
                                            @elseif($appointment['status'] == 'Programada')
                                                <span class="badge bg-info">{{ $appointment['status'] }}</span>
                                            @elseif($appointment['status'] == 'Completada')
                                                <span class="badge bg-secondary">{{ $appointment['status'] }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ $appointment['status'] }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('appointments.show', $appointment['id']) }}" 
                                                   class="btn btn-info"
                                                   data-bs-toggle="tooltip" 
                                                   title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('appointments.edit', $appointment['id']) }}" 
                                                   class="btn btn-warning"
                                                   data-bs-toggle="tooltip" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar fa-4x text-gray-300 mb-3"></i>
                            <p class="text-muted">No hay próximas citas programadas</p>
                            <a href="{{ route('appointments.create') }}" class="btn btn-primary">
                                <i class="fas fa-calendar-plus"></i> Programar Cita
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-xl-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-bolt me-1"></i> Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('appointments.create') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-calendar-plus fa-2x mb-2"></i><br>
                                Nueva Cita
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('patients.create') }}" class="btn btn-success btn-block">
                                <i class="fas fa-user-plus fa-2x mb-2"></i><br>
                                Nuevo Paciente
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('appointments.index') }}?view=calendar" class="btn btn-info btn-block">
                                <i class="fas fa-calendar-alt fa-2x mb-2"></i><br>
                                Vista Calendario
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('appointments.index') }}?view=list" class="btn btn-warning btn-block">
                                <i class="fas fa-list fa-2x mb-2"></i><br>
                                Vista Lista
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
<style>
    .fc .fc-button-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .fc .fc-button-primary:hover {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
    }
    
    .fc .fc-button-primary:disabled {
        background-color: #6c757d;
        border-color: #6c757d;
    }
    
    .fc-daygrid-event {
        cursor: pointer;
    }
    
    .fc-event-title {
        font-weight: 600;
        font-size: 0.85rem;
    }
    
    .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--dark-color);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar calendario
        const calendarEl = document.getElementById('appointments-calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            locale: 'es',
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                day: 'Día'
            },
            firstDay: 1, // Lunes como primer día
            navLinks: true,
            dayMaxEvents: true,
            editable: false,
            selectable: true,
            selectMirror: true,
            events: function(fetchInfo, successCallback, failureCallback) {
                // Obtener citas desde la API
                fetch('{{ route("appointments.calendar.events") }}?start=' + fetchInfo.startStr + '&end=' + fetchInfo.endStr)
                    .then(response => response.json())
                    .then(data => {
                        successCallback(data);
                    })
                    .catch(error => {
                        console.error('Error al cargar eventos:', error);
                        failureCallback(error);
                    });
            },
            eventClick: function(info) {
                // Mostrar detalles de la cita
                const event = info.event;
                const extendedProps = event.extendedProps;
                
                Swal.fire({
                    title: 'Detalles de la Cita',
                    html: `
                        <div class="text-start">
                            <p><strong>Paciente:</strong> ${extendedProps.patient_name}</p>
                            <p><strong>Profesional:</strong> ${extendedProps.professional_name}</p>
                            <p><strong>Motivo:</strong> ${extendedProps.reason}</p>
                            <p><strong>Fecha:</strong> ${event.start.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                            <p><strong>Hora:</strong> ${event.start.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })} - ${event.end.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })}</p>
                            <p><strong>Duración:</strong> ${extendedProps.duration} minutos</p>
                            <p><strong>Estado:</strong> <span class="badge bg-${extendedProps.status === 'Confirmada' ? 'success' : 'info'}">${extendedProps.status}</span></p>
                            ${extendedProps.notes ? `<p><strong>Notas:</strong> ${extendedProps.notes}</p>` : ''}
                        </div>
                    `,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Ver Detalles',
                    cancelButtonText: 'Cerrar',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/appointments/' + event.id;
                    }
                });
            },
            select: function(selectInfo) {
                // Crear nueva cita al seleccionar fecha/hora
                const selectedDate = selectInfo.start;
                const endDate = selectInfo.end;
                
                // Redirigir a creación de cita con fecha preseleccionada
                const params = new URLSearchParams({
                    fecha: selectedDate.toISOString().split('T')[0],
                    hora: selectedDate.getHours().toString().padStart(2, '0') + ':' + selectedDate.getMinutes().toString().padStart(2, '0')
                });
                
                window.location.href = '{{ route("appointments.create") }}?' + params.toString();
            },
            eventDidMount: function(info) {
                // Añadir tooltip a los eventos
                const extendedProps = info.event.extendedProps;
                const tooltipContent = `
                    <strong>${extendedProps.patient_name}</strong><br>
                    <strong>Profesional:</strong> ${extendedProps.professional_name}<br>
                    <strong>Motivo:</strong> ${extendedProps.reason}<br>
                    <strong>Hora:</strong> ${info.event.start.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })} - ${info.event.end.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })}
                `;
                
                $(info.el).tooltip({
                    title: tooltipContent,
                    html: true,
                    placement: 'top'
                });
            }
        });
        
        calendar.render();
        
        // Controladores para los botones de navegación personalizados
        document.getElementById('prev-month').addEventListener('click', function() {
            calendar.prev();
        });
        
        document.getElementById('today-btn').addEventListener('click', function() {
            calendar.today();
        });
        
        document.getElementById('next-month').addEventListener('click', function() {
            calendar.next();
        });
        
        // Actualizar calendario cada 5 minutos
        setInterval(function() {
            calendar.refetchEvents();
        }, 5 * 60 * 1000);
        
        // Inicializar tooltips de Bootstrap
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush