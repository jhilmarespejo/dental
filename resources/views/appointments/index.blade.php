@extends('layouts.app')
<style>'
    .fc-button-group {
        display: none !important;
    }
</style>
@section('title', 'Gestión de Citas - Consultorio')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Citas</h1>
        <a href="#" class="btn btn-primary new-appointment-btn">
            <i class="fas fa-calendar-plus"></i> Nueva Cita
        </a>
    </div>

    <div class="row">
        <!-- Calendar View -->
        <div class="col-xl-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-calendar-alt me-1"></i> Calendario de Citas
                    </h6>
                </div>
                <div class="card-body">
                    <div id="appointments-calendar" style="height: 500px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Today's Appointments -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-calendar-day me-1"></i> Citas de Hoy
                    </h6>
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
                                        {{-- <th>Acciones</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($today as $appointment)
                                    <tr>
                                        <td>{{ $appointment['time'] }}</td>
                                        <td>{{ $appointment['patient'] }}</td>
                                        <td>{{ $appointment['reason'] }}</td>
                                        <td>
                                            @if($appointment['status'] == 'Confirmada')
                                                <span class="badge bg-success">{{ $appointment['status'] }}</span>
                                            @elseif($appointment['status'] == 'Pendiente')
                                                <span class="badge bg-warning">{{ $appointment['status'] }}</span>
                                            @else
                                                <span class="badge bg-info">{{ $appointment['status'] }}</span>
                                            @endif
                                        </td>
                                        {{-- <td class="text-center">
                                            <!-- Botones de acciones comentados
                                            <div class="btn-group" role="group" aria-label="Appointment actions">
                                                <a href="{{ route('appointments.show', $appointment['id']) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Marcar como completada">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Cancelar cita">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            -->
                                            {{-- <a href="{{ route('appointments.show', $appointment['id']) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a> --}}
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
                        <i class="fas fa-calendar-week me-1"></i> Próximas Citas
                    </h6>
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
                                        {{-- <th>Acciones</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcoming as $appointment)
                                    <tr>
                                        <td>{{ $appointment['date'] }}</td>
                                        <td>{{ $appointment['time'] }}</td>
                                        <td>{{ $appointment['patient'] }}</td>
                                        <td>{{ $appointment['reason'] }}</td>
                                        <td>
                                            @if($appointment['status'] == 'Confirmada')
                                                <span class="badge bg-success">{{ $appointment['status'] }}</span>
                                            @elseif($appointment['status'] == 'Pendiente')
                                                <span class="badge bg-warning">{{ $appointment['status'] }}</span>
                                            @else
                                                <span class="badge bg-info">{{ $appointment['status'] }}</span>
                                            @endif
                                        </td>
                                        {{-- <td class="text-center">
                                            <!-- Botones de acciones comentados
                                            <div class="btn-group" role="group" aria-label="Appointment actions">
                                                <a href="{{ route('appointments.show', $appointment['id']) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Cancelar cita">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            -->
                                            <a href="{{ route('appointments.show', $appointment['id']) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td> --}}
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar fa-4x text-gray-300 mb-3"></i>
                            <p class="text-muted">No hay próximas citas programadas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Appointment Statistics -->
    <div class="row">
        <div class="col-xl-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-line me-1"></i> Estadísticas de Citas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2 info-card primary">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col me-2">
                                            <div class="text-xs fw-bold text-uppercase mb-1 info-card-title">
                                                Total Citas
                                            </div>
                                            <div class="h5 mb-0 fw-bold text-gray-800 info-card-value">124</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2 info-card success">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col me-2">
                                            <div class="text-xs fw-bold text-uppercase mb-1 info-card-title">
                                                Completadas
                                            </div>
                                            <div class="h5 mb-0 fw-bold text-gray-800 info-card-value">86</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2 info-card info">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col me-2">
                                            <div class="text-xs fw-bold text-uppercase mb-1 info-card-title">
                                                Pendientes
                                            </div>
                                            <div class="h5 mb-0 fw-bold text-gray-800 info-card-value">24</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2 info-card warning">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col me-2">
                                            <div class="text-xs fw-bold text-uppercase mb-1 info-card-title">
                                                Canceladas
                                            </div>
                                            <div class="h5 mb-0 fw-bold text-gray-800 info-card-value">14</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar-times fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />

<script>
    // esconder botones de acciones
    document.querySelectorAll('.fc-button-group').forEach(function(btnGroup) {
        btnGroup.style.display = 'none';
    });
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar calendario
        var calendarEl = document.getElementById('appointments-calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                //right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            initialView: 'dayGridMonth', // Solo vista mensual
            locale: 'es',
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                day: 'Día'
            },
            height: 'auto',
            timeZone: 'local',
            dayMaxEvents: true,
            navLinks: true,
            selectable: true,
            selectMirror: true,
            events: [
                {
                    id: '1',
                    title: 'Sofía García - Revisión ortodoncia',
                    start: '2025-02-10T09:00:00',
                    end: '2025-02-10T09:30:00',
                    backgroundColor: '#4e73df',
                    borderColor: '#4e73df'
                },
                {
                    id: '2',
                    title: 'Diego Flores - Extracción muela del juicio',
                    start: '2025-02-10T10:30:00',
                    end: '2025-02-10T11:30:00',
                    backgroundColor: '#e74a3b',
                    borderColor: '#e74a3b'
                },
                {
                    id: '3',
                    title: 'Patricia Rojas - Limpieza dental',
                    start: '2025-02-10T12:00:00',
                    end: '2025-02-10T13:00:00',
                    backgroundColor: '#1cc88a',
                    borderColor: '#1cc88a'
                },
                {
                    id: '4',
                    title: 'Miguel Torres - Endodoncia pieza 27',
                    start: '2025-02-10T15:00:00',
                    end: '2025-02-10T16:00:00',
                    backgroundColor: '#e74a3b',
                    borderColor: '#e74a3b'
                },
                {
                    id: '5',
                    title: 'Laura Vargas - Evaluación inicial',
                    start: '2025-02-10T16:30:00',
                    end: '2025-02-10T17:30:00',
                    backgroundColor: '#4e73df',
                    borderColor: '#4e73df'
                },
                {
                    id: '6',
                    title: 'Fernando Castro - Control post-operatorio',
                    start: '2025-02-10T18:00:00',
                    end: '2025-02-10T18:30:00',
                    backgroundColor: '#1cc88a',
                    borderColor: '#1cc88a'
                },
                {
                    id: '7',
                    title: 'Carmen Mendoza - Evaluación brackets',
                    start: '2025-02-11T09:30:00',
                    end: '2025-02-11T10:30:00',
                    backgroundColor: '#4e73df',
                    borderColor: '#4e73df'
                },
                {
                    id: '8',
                    title: 'Javier Gutiérrez - Revisión periódica',
                    start: '2025-02-11T11:00:00',
                    end: '2025-02-11T12:00:00',
                    backgroundColor: '#1cc88a',
                    borderColor: '#1cc88a'
                },
                {
                    id: '9',
                    title: 'Marcela Suárez - Blanqueamiento dental',
                    start: '2025-02-11T14:30:00',
                    end: '2025-02-11T16:00:00',
                    backgroundColor: '#4e73df',
                    borderColor: '#4e73df'
                },
                {
                    id: '10',
                    title: 'Ricardo Molina - Revisión prótesis',
                    start: '2025-02-12T10:00:00',
                    end: '2025-02-12T11:00:00',
                    backgroundColor: '#1cc88a',
                    borderColor: '#1cc88a'
                },
                {
                    id: '11',
                    title: 'Verónica Paz - Empaste pieza 14',
                    start: '2025-02-12T12:30:00',
                    end: '2025-02-12T13:30:00',
                    backgroundColor: '#e74a3b',
                    borderColor: '#e74a3b'
                },
                {
                    id: '12',
                    title: 'Gustavo Ramírez - Evaluación ortodoncia',
                    start: '2025-02-12T16:00:00',
                    end: '2025-02-12T17:00:00',
                    backgroundColor: '#4e73df',
                    borderColor: '#4e73df'
                }
            ],
            select: function(info) {
                // Abrir modal para nueva cita
                document.querySelector('.new-appointment-btn').click();
            },
            eventClick: function(info) {
                // Mostrar detalles de la cita
                Swal.fire({
                    title: 'Detalles de la Cita',
                    html: `
                        <div class="text-start">
                            <p><strong>Paciente:</strong> ${info.event.title.split(' - ')[0]}</p>
                            <p><strong>Motivo:</strong> ${info.event.title.split(' - ')[1]}</p>
                            <p><strong>Fecha:</strong> ${new Date(info.event.start).toLocaleDateString()}</p>
                            <p><strong>Hora:</strong> ${new Date(info.event.start).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} - ${new Date(info.event.end).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Cerrar'
                });
            }
        });
        calendar.render();
        
        // Enlazar botón de nueva cita con el modal
        document.querySelector('.new-appointment-btn').addEventListener('click', function(e) {
            e.preventDefault();
            const appointmentModal = new bootstrap.Modal(document.getElementById('newAppointmentModal'));
            appointmentModal.show();
        });
    });
</script>
@endpush