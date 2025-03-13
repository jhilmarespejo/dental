<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Consultorio - Sistema de Gestión para Consultorios Dentales')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }
        
        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            overflow-x: hidden;
        }
        
        /* Sidebar styles */
        .sidebar {
            width: 14rem;
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 10%, #224abe 100%);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.2s;
            color: #fff;
        }
        
        .sidebar-brand {
            height: 4.375rem;
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: 800;
            padding: 1.5rem 1rem;
            text-align: center;
            letter-spacing: 0.05rem;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar-brand-icon {
            margin-right: 0.5rem;
        }
        
        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin: 0 1rem 1rem;
        }
        
        .sidebar .nav-item {
            position: relative;
        }
        
        .sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.2s;
        }
        
        .sidebar .nav-link i {
            margin-right: 0.5rem;
            width: 1.25rem;
            text-align: center;
        }
        
        .sidebar .nav-link:hover, 
        .sidebar .nav-link:active,
        .sidebar .nav-link:focus {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar.toggled {
            width: 6.5rem;
        }
        
        .sidebar.toggled .nav-link span {
            display: none;
        }
        
        .sidebar.toggled .nav-link i {
            margin-right: 0;
            font-size: 1.25rem;
        }
        
        .sidebar.toggled .sidebar-brand {
            justify-content: center;
            padding: 1.5rem 0;
        }
        
        .sidebar.toggled .sidebar-brand-icon {
            margin-right: 0;
        }
        
        /* Main content styles */
        .content-wrapper {
            margin-left: 14rem;
            transition: all 0.2s;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .content-wrapper.toggled {
            margin-left: 6.5rem;
        }
        
        /* Topbar styles */
        .topbar {
            height: 4.375rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            z-index: 1;
        }
        
        .topbar .navbar-search {
            width: 25rem;
        }
        
        .topbar .navbar-search input {
            font-size: 0.85rem;
            height: auto;
        }
        
        .topbar .dropdown-list {
            padding: 0;
            border: none;
            overflow: hidden;
            width: 20rem !important;
        }
        
        .topbar .dropdown-header {
            background-color: var(--primary-color);
            border: 1px solid var(--primary-color);
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            color: #fff;
        }
        
        .icon-circle {
            height: 2.5rem;
            width: 2.5rem;
            border-radius: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .img-profile {
            height: 2rem;
            width: 2rem;
        }
        
        .content-area {
            flex: 1 0 auto;
        }
        
        /* Footer */
        .sticky-footer {
            padding: 1rem 0;
            flex-shrink: 0;
        }
        
        /* Fix bootstrap badge positions */
        .badge-counter {
            position: absolute;
            transform: scale(0.7);
            transform-origin: top right;
            right: 0.25rem;
            top: 0.15rem;
        }
        
        /* Dropdown styling */
        .dropdown-menu {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
        }
        
        /* Card styling */
        .info-card {
            border-left: 0.25rem solid;
        }
        
        .info-card.primary { border-left-color: var(--primary-color); }
        .info-card.success { border-left-color: var(--success-color); }
        .info-card.info { border-left-color: var(--info-color); }
        .info-card.warning { border-left-color: var(--warning-color); }
        
        .info-card-title {
            text-transform: uppercase;
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .info-card-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
        }
        
        @media (max-width: 992px) {
            .sidebar {
                width: 6.5rem;
            }
            
            .sidebar .nav-link span {
                display: none;
            }
            
            .sidebar .nav-link i {
                margin-right: 0;
                font-size: 1.25rem;
            }
            
            .sidebar .sidebar-brand {
                justify-content: center;
                padding: 1.5rem 0;
            }
            
            .sidebar .sidebar-brand-icon {
                margin-right: 0;
            }
            
            .content-wrapper {
                margin-left: 6.5rem;
            }
            
            .topbar .navbar-search {
                width: 100%;
                margin-bottom: 0.5rem;
            }
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
                transition: margin-left 0.3s ease-in-out;
            }
            
            .content-wrapper.full-width {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar" id="mainSidebar">
            <div class="sidebar-brand">
                <i class="fas fa-tooth sidebar-brand-icon"></i>
                <div class="ms-2">Consultorio</div>
            </div>
            
            <hr class="sidebar-divider my-0">
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Panel</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('patients.index') }}">
                        <i class="fas fa-fw fa-users"></i>
                        <span>Pacientes</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('appointments.index') }}">
                        <i class="fas fa-fw fa-calendar-alt"></i>
                        <span>Citas</span>
                    </a>
                </li>
                
                {{-- <li class="nav-item">
                    <a class="nav-link" href="{{ route('treatments.index') }}">
                        <i class="fas fa-fw fa-procedures"></i>
                        <span>Tratamientos</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('payments.index') }}">
                        <i class="fas fa-fw fa-money-bill-wave"></i>
                        <span>Pagos</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reports.index') }}">
                        <i class="fas fa-fw fa-chart-bar"></i>
                        <span>Reportes</span>
                    </a>
                </li> --}}
            </ul>
            
            <hr class="sidebar-divider d-none d-md-block">
            
            <div class="text-center d-none d-md-inline">
                <button class="btn btn-light btn-sm rounded-circle border-0" id="sidebarToggle">
                    <i class="fas fa-angle-left"></i>
                </button>
            </div>
        </div>
        
        <!-- Main Content Wrapper -->
        <div class="content-wrapper" id="contentWrapper">
            <!-- Top Navigation -->
            <nav class="topbar navbar navbar-expand navbar-light bg-white mb-4">
                <div class="container-fluid">
                    <button id="sidebar-toggle" class="btn btn-link d-md-none rounded-circle me-3">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <!-- Search -->
                    <form class="d-none d-sm-inline-block me-auto ms-md-3 my-2 my-md-0 navbar-search" id="searchPatientForm">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Buscar paciente..."
                                aria-label="Search" aria-describedby="basic-addon2" id="searchPatientInput">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </form>
                    
                    <!-- Navbar Links -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Notifications Dropdown -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <span class="badge rounded-pill bg-danger badge-counter">3+</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Centro de Notificaciones
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="me-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-calendar-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">10 de Marzo, 2025</div>
                                        <span>5 citas programadas para hoy</span>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="me-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-user-plus text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">8 de Marzo, 2025</div>
                                        Se registró un nuevo paciente: Laura Vargas
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="me-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-money-bill-wave text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">7 de Marzo, 2025</div>
                                        Pago pendiente: Carlos Rodríguez - Bs. 500
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Ver todas las notificaciones</a>
                            </div>
                        </li>
                        
                        <!-- User Dropdown -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="me-2 d-none d-lg-inline text-gray-600 small">Dr. Juan Gómez</span>
                                <img class="img-profile rounded-circle" src="https://ui-avatars.com/api/?name=Juan+Gomez&background=4e73df&color=fff">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                                    Perfil
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i>
                                    Configuración
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                                    Cerrar sesión
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <!-- Content Area -->
            <div class="content-area container-fluid px-4">
                @yield('content')
            </div>
            
            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Consultorio 2025</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <!-- Patient Search Results Modal -->
    <div class="modal fade" id="searchResultsModal" tabindex="-1" aria-labelledby="searchResultsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchResultsModalLabel">Resultados de búsqueda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="list-group">
                        <a href="{{ route('patients.show', 1) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">Juan Pérez</h5>
                                <small>35 años</small>
                            </div>
                            <p class="mb-1">+591 70707070</p>
                            <small>Última visita: 15/06/2025</small>
                        </a>
                        <a href="{{ route('patients.show', 2) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">María González</h5>
                                <small>42 años</small>
                            </div>
                            <p class="mb-1">+591 71717171</p>
                            <small>Última visita: 22/05/2025</small>
                        </a>
                        <a href="{{ route('patients.show', 3) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">Carlos Rodríguez</h5>
                                <small>29 años</small>
                            </div>
                            <p class="mb-1">+591 72727272</p>
                            <small>Última visita: 03/07/2025</small>
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <a href="{{ route('patients.index') }}" class="btn btn-primary">Ver todos los pacientes</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- New Appointment Modal -->
    <div class="modal fade" id="newAppointmentModal" tabindex="-1" aria-labelledby="newAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newAppointmentModalLabel">Nueva Cita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="appointmentForm">
                        <div class="mb-3">
                            <label for="patient-select" class="form-label">Paciente</label>
                            <select id="patient-select" class="form-select">
                                <option selected>Seleccionar paciente...</option>
                                <option value="1">Juan Pérez</option>
                                <option value="2">María González</option>
                                <option value="3">Carlos Rodríguez</option>
                                <option value="4">Ana Martínez</option>
                                <option value="5">Roberto López</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="appointment-date" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="appointment-date" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label for="appointment-time" class="form-label">Hora</label>
                            <input type="time" class="form-control" id="appointment-time" value="09:00">
                        </div>
                        <div class="mb-3">
                            <label for="appointment-type" class="form-label">Tipo de cita</label>
                            <select id="appointment-type" class="form-select">
                                <option>Revisión regular</option>
                                <option>Limpieza dental</option>
                                <option>Tratamiento de conducto</option>
                                <option>Extracción</option>
                                <option>Empaste</option>
                                <option>Otro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="appointment-notes" class="form-label">Notas</label>
                            <textarea class="form-control" id="appointment-notes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveAppointmentBtn">Guardar cita</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">¿Listo para salir?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Seleccione "Cerrar sesión" si está listo para finalizar su sesión actual.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">Cerrar sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <!-- jQuery (opcional) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    
    <!-- Custom scripts -->
    <script>
        // Toggle sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('mainSidebar').classList.toggle('toggled');
            document.getElementById('contentWrapper').classList.toggle('toggled');
            
            // Rotate icon
            const icon = this.querySelector('i');
            if (icon.classList.contains('fa-angle-left')) {
                icon.classList.remove('fa-angle-left');
                icon.classList.add('fa-angle-right');
            } else {
                icon.classList.remove('fa-angle-right');
                icon.classList.add('fa-angle-left');
            }
        });
        
        // Mobile sidebar toggle
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            const sidebar = document.getElementById('mainSidebar');
            const contentWrapper = document.getElementById('contentWrapper');
            
            sidebar.classList.toggle('mobile-hidden');
            contentWrapper.classList.toggle('full-width');
        });
        
        // Patient search functionality
        document.getElementById('searchPatientForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const searchModal = new bootstrap.Modal(document.getElementById('searchResultsModal'));
            searchModal.show();
        });
        
        // New appointment button functionality
        document.querySelectorAll('.new-appointment-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const appointmentModal = new bootstrap.Modal(document.getElementById('newAppointmentModal'));
                appointmentModal.show();
            });
        });
        
        // Save appointment functionality
        document.getElementById('saveAppointmentBtn').addEventListener('click', function() {
            // In a real app, this would save the appointment
            // For demo, we'll just show a success message and close the modal
            const appointmentModal = bootstrap.Modal.getInstance(document.getElementById('newAppointmentModal'));
            appointmentModal.hide();
            
            // Show success alert
            const alertPlaceholder = document.createElement('div');
            alertPlaceholder.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-4" role="alert" style="z-index: 1050;">
                    <strong>¡Éxito!</strong> La cita ha sido programada correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            document.body.appendChild(alertPlaceholder);
            
            // Remove alert after 3 seconds
            setTimeout(() => {
                alertPlaceholder.remove();
            }, 3000);
        });
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
    
    <!-- Scripts específicos de la página -->
    @stack('scripts')
</body>
</html>