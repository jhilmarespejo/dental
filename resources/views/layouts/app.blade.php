<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Consultorio - Sistema de Gestión para Consultorios Dentales')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4e73df;
            --primary-dark: #224abe;
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
            background-color: #f8f9fc;
        }
        
        /* Sidebar styles */
        .sidebar {
            width: 14rem;
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 10%, var(--primary-dark) 100%);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.2s;
            color: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
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
        .sidebar .nav-link:focus,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-item.active .nav-link {
            font-weight: 700;
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
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
            width: calc(100% - 14rem);
        }
        
        .content-wrapper.toggled {
            margin-left: 6.5rem;
            width: calc(100% - 6.5rem);
        }
        
        /* Topbar styles */
        .topbar {
            height: 4.375rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            z-index: 1;
            background-color: #fff;
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
            padding: 1.5rem;
        }
        
        /* Footer */
        .sticky-footer {
            padding: 1rem 0;
            flex-shrink: 0;
            background-color: #fff;
            border-top: 1px solid #e3e6f0;
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
        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 0.75rem 1.25rem;
        }
        
        .info-card {
            border-left: 0.25rem solid;
        }
        
        .info-card.primary { border-left-color: var(--primary-color); }
        .info-card.success { border-left-color: var(--success-color); }
        .info-card.info { border-left-color: var(--info-color); }
        .info-card.warning { border-left-color: var(--warning-color); }
        .info-card.danger { border-left-color: var(--danger-color); }
        
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
        
        /* DataTables custom styling */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.25rem 0.5rem;
            margin: 0 0.2rem;
            border-radius: 0.2rem;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-color);
            color: white !important;
            border: 1px solid var(--primary-color);
        }
        
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            padding: 0.3rem 0.75rem;
        }
        
        .dataTables_wrapper .dataTables_info {
            padding-top: 0.5rem;
        }
        
        /* Button styling */
        .btn-circle {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 100%;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        
        .btn-circle.btn-sm {
            width: 1.8rem;
            height: 1.8rem;
            font-size: 0.75rem;
        }
        
        /* Select2 Custom Styling */
        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid #e3e6f0;
            min-height: calc(1.5em + 0.75rem + 2px);
            padding: 0.375rem 0.75rem;
        }
        
        /* Notification alerts */
        .top-alert {
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 9999;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        /* Custom calendar style for flatpickr */
        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day.selected.inRange,
        .flatpickr-day.startRange.inRange,
        .flatpickr-day.endRange.inRange,
        .flatpickr-day.selected:focus,
        .flatpickr-day.startRange:focus,
        .flatpickr-day.endRange:focus,
        .flatpickr-day.selected:hover,
        .flatpickr-day.startRange:hover,
        .flatpickr-day.endRange:hover,
        .flatpickr-day.selected.prevMonthDay,
        .flatpickr-day.startRange.prevMonthDay,
        .flatpickr-day.endRange.prevMonthDay,
        .flatpickr-day.selected.nextMonthDay,
        .flatpickr-day.startRange.nextMonthDay,
        .flatpickr-day.endRange.nextMonthDay {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        /* Responsive adjustments */
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
                width: calc(100% - 6.5rem);
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
                width: calc(100% - 6.5rem);
                transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
            }
            
            .content-wrapper.full-width {
                margin-left: 0;
                width: 100%;
            }
        }
    table tr td a {
        text-decoration: none;   /*elimina la línea*/
        /* color: inherit;          opcional: usa el mismo color del texto */
    }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Flash messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show top-alert" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show top-alert" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar" id="mainSidebar">
            <div class="sidebar-brand">
                <i class="fas fa-tooth fa-2x sidebar-brand-icon"></i>
                <div class="ms-2">Consultorio</div>
            </div>
            
            <hr class="sidebar-divider my-0">
            
            <ul class="nav flex-column">
                <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Panel</span>
                    </a>
                </li>
                
                <li class="nav-item {{ request()->routeIs('patients.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}" href="{{ route('patients.index') }}">
                        <i class="fas fa-fw fa-users"></i>
                        <span>Pacientes</span>
                    </a>
                </li>
                
                <li class="nav-item {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}" href="{{ route('appointments.index') }}">
                        <i class="fas fa-fw fa-calendar-alt"></i>
                        <span>Citas</span>
                    </a>
                </li>
                
                <li class="nav-item {{ request()->routeIs('treatments.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('treatments.*') ? 'active' : '' }}" href="{{ route('treatments.index') }}">
                        <i class="fas fa-fw fa-procedures"></i>
                        <span>Tratamientos</span>
                    </a>
                </li>
                
                <li class="nav-item {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}" href="{{ route('payments.index') }}">
                        <i class="fas fa-fw fa-money-bill-wave"></i>
                        <span>Pagos</span>
                    </a>
                </li>
                
                <li class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                        <i class="fas fa-fw fa-chart-bar"></i>
                        <span>Reportes</span>
                    </a>
                </li>
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
                            <input type="text" class="form-control bg-light border small" placeholder="Buscar paciente..."
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
                                <span class="badge rounded-pill bg-danger badge-counter" id="notification-count">0</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                                aria-labelledby="alertsDropdown" id="notifications-container">
                                <h6 class="dropdown-header">
                                    Centro de Notificaciones
                                </h6>
                                <div id="notifications-list">
                                    <!-- Las notificaciones se cargarán aquí dinámicamente -->
                                    <div class="dropdown-item text-center small text-gray-500" id="no-notifications">
                                        No hay notificaciones nuevas
                                    </div>
                                </div>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Ver todas las notificaciones</a>
                            </div>
                        </li>
                        
                        <!-- Quick Actions Dropdown -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="actionsDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-plus-circle fa-fw"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                                aria-labelledby="actionsDropdown">
                                <h6 class="dropdown-header">
                                    Acciones Rápidas
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('patients.create') }}">
                                    <div class="me-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-user-plus text-white"></i>
                                        </div>
                                    </div>
                                    <div>Nuevo Paciente</div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('appointments.create') }}">
                                    <div class="me-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-calendar-plus text-white"></i>
                                        </div>
                                    </div>
                                    <div>Nueva Cita</div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('treatments.create') }}">
                                    <div class="me-3">
                                        <div class="icon-circle bg-info">
                                            <i class="fas fa-clipboard-list text-white"></i>
                                        </div>
                                    </div>
                                    <div>Nuevo Tratamiento</div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('payments.pending') }}">
                                    <div class="me-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-money-bill-wave text-white"></i>
                                        </div>
                                    </div>
                                    <div>Pagos Pendientes</div>
                                </a>
                            </div>
                        </li>
                        
                        <!-- User Dropdown -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="me-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                                <img class="img-profile rounded-circle" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4e73df&color=fff">
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
            <div class="content-area">
                @yield('content')
            </div>
            
            <!-- Footer -->
            <footer class="sticky-footer">
                <div class="container">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Consultorio {{ date('Y') }}</span>
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
                    <div class="list-group" id="search-results-container">
                        <!-- Los resultados de búsqueda se cargarán aquí -->
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
    <!-- New Appointment Modal -->
<div class="modal fade" id="newAppointmentModal" tabindex="-1" aria-labelledby="newAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newAppointmentModalLabel">Nueva Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('appointments.store') }}" method="POST" id="quickAppointmentForm">
                @csrf
                <div class="modal-body">
                    <!-- Mensajes de error -->
                    <div class="alert alert-danger d-none" id="formErrors">
                        <ul class="mb-0" id="errorList"></ul>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="paciente_id" class="form-label required">Paciente</label>
                            <select id="paciente_id" name="paciente_id" class="form-select @error('paciente_id') is-invalid @enderror" required>
                                <option value="">Seleccionar paciente...</option>
                                @foreach($patients ?? [] as $patient)
                                    <option value="{{ $patient['id'] }}" 
                                            {{ old('paciente_id') == $patient['id'] ? 'selected' : '' }}>
                                        {{ $patient['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('paciente_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="mt-2">
                                <a href="{{ route('patients.create') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="fas fa-user-plus"></i> Nuevo Paciente
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="profesional_id" class="form-label required">Profesional</label>
                            <select id="profesional_id" name="profesional_id" class="form-select @error('profesional_id') is-invalid @enderror" required>
                                <option value="">Seleccionar profesional...</option>
                                @foreach($professionals ?? [] as $professional)
                                    <option value="{{ $professional['id'] }}" 
                                            {{ old('profesional_id') == $professional['id'] ? 'selected' : '' }}>
                                        {{ $professional['name'] }} 
                                        @if($professional['specialty'])
                                            - {{ $professional['specialty'] }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('profesional_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha" class="form-label required">Fecha</label>
                            <input type="date" 
                                   class="form-control @error('fecha') is-invalid @enderror" 
                                   id="fecha" 
                                   name="fecha" 
                                   value="{{ old('fecha', date('Y-m-d')) }}" 
                                   required 
                                   min="{{ date('Y-m-d') }}">
                            @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="hora" class="form-label required">Hora</label>
                            <input type="time" 
                                   class="form-control @error('hora') is-invalid @enderror" 
                                   id="hora" 
                                   name="hora" 
                                   value="{{ old('hora', date('H:00', strtotime('+1 hour'))) }}" 
                                   required 
                                   step="300">
                            @error('hora')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="duracion" class="form-label required">Duración</label>
                            <select id="duracion" name="duracion" class="form-select @error('duracion') is-invalid @enderror" required>
                                <option value="15" {{ old('duracion', '30') == '15' ? 'selected' : '' }}>15 minutos</option>
                                <option value="30" {{ old('duracion', '30') == '30' ? 'selected' : '' }}>30 minutos</option>
                                <option value="45" {{ old('duracion') == '45' ? 'selected' : '' }}>45 minutos</option>
                                <option value="60" {{ old('duracion') == '60' ? 'selected' : '' }}>1 hora</option>
                                <option value="90" {{ old('duracion') == '90' ? 'selected' : '' }}>1 hora 30 minutos</option>
                                <option value="120" {{ old('duracion') == '120' ? 'selected' : '' }}>2 horas</option>
                            </select>
                            @error('duracion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="motivo" class="form-label required">Motivo</label>
                            <input type="text" 
                                   class="form-control @error('motivo') is-invalid @enderror" 
                                   id="motivo" 
                                   name="motivo" 
                                   value="{{ old('motivo') }}" 
                                   required 
                                   maxlength="250"
                                   placeholder="Ej: Revisión, Limpieza, Tratamiento...">
                            @error('motivo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notas" class="form-label">Notas Adicionales</label>
                        <textarea class="form-control @error('notas') is-invalid @enderror" 
                                  id="notas" 
                                  name="notas" 
                                  rows="3"
                                  placeholder="Observaciones, instrucciones especiales...">{{ old('notas') }}</textarea>
                        @error('notas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Vista previa de la cita -->
                    <div class="card mt-3 d-none" id="appointmentPreview">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Vista Previa de la Cita</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Fecha:</strong> <span id="previewDate"></span></p>
                                    <p><strong>Hora:</strong> <span id="previewTime"></span></p>
                                    <p><strong>Duración:</strong> <span id="previewDuration"></span> minutos</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Motivo:</strong> <span id="previewReason"></span></p>
                                    <p><strong>Notas:</strong> <span id="previewNotes" class="text-muted">Ninguna</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-calendar-plus"></i> Guardar Cita
                    </button>
                </div>
            </form>
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
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    
    <!-- Bootstrap 5 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom scripts -->
    <script>
        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Inicializar Select2
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5'
            });
            
            // Inicializar Flatpickr para campos de fecha
            flatpickr("input[type=date]", {
                locale: "es",
                altInput: true,
                altFormat: "d/m/Y",
                dateFormat: "Y-m-d"
            });
            
            // Inicializar Flatpickr para campos de hora
            flatpickr("input[type=time]", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true
            });
            
            // Inicializar DataTables
            if ($('.datatable').length > 0) {
                $('.datatable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                    },
                    responsive: true
                });
            }
        });

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
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
        
        // Patient search functionality
        document.getElementById('searchPatientForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const searchTerm = document.getElementById('searchPatientInput').value.trim();
            
            if (searchTerm.length < 2) {
                return;
            }
            
            // Realizar búsqueda AJAX
            fetch(`/patients/search/query?q=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(data => {
                    const resultsContainer = document.getElementById('search-results-container');
                    resultsContainer.innerHTML = '';
                    
                    if (data.length === 0) {
                        resultsContainer.innerHTML = '<div class="text-center py-3">No se encontraron resultados</div>';
                    } else {
                        data.forEach(patient => {
                            const item = document.createElement('a');
                            item.href = `/patients/${patient.id}`;
                            item.className = 'list-group-item list-group-item-action';
                            
                            item.innerHTML = `
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">${patient.name}</h5>
                                    <small>${patient.age} años</small>
                                </div>
                                <p class="mb-1">${patient.phone || 'No registrado'}</p>
                                <small>Última visita: ${patient.last_visit}</small>
                            `;
                            
                            resultsContainer.appendChild(item);
                        });
                    }
                    
                    const searchModal = new bootstrap.Modal(document.getElementById('searchResultsModal'));
                    searchModal.show();
                })
                .catch(error => {
                    console.error('Error al buscar pacientes:', error);
                    alert('Error al buscar pacientes. Por favor, inténtelo de nuevo.');
                });
        });
        
        // Cargar datos para el modal de nueva cita rápida
        $(document).on('shown.bs.modal', '#newAppointmentModal', function () {
            // Cargar pacientes
            fetch('/patients?format=json')
                .then(response => response.json())
                .then(data => {
                    const selectElement = document.getElementById('paciente_id');
                    
                    // Limpiar opciones existentes excepto la primera
                    while(selectElement.options.length > 1) {
                        selectElement.remove(1);
                    }
                    
                    // Agregar nuevas opciones
                    data.forEach(patient => {
                        const option = new Option(patient.nombre_completo, patient.id);
                        selectElement.add(option);
                    });
                    
                    // Refrescar Select2
                    $(selectElement).trigger('change');
                });
                
            // Cargar profesionales
            fetch('/professionals?format=json')
                .then(response => response.json())
                .then(data => {
                    const selectElement = document.getElementById('profesional_id');
                    
                    // Limpiar opciones existentes excepto la primera
                    while(selectElement.options.length > 1) {
                        selectElement.remove(1);
                    }
                    
                    // Agregar nuevas opciones
                    data.forEach(professional => {
                        const option = new Option(professional.nombre_completo, professional.id);
                        selectElement.add(option);
                    });
                    
                    // Refrescar Select2
                    $(selectElement).trigger('change');
                });
                
            // Establecer fecha actual
            document.getElementById('fecha').value = new Date().toISOString().slice(0, 10);
            
            // Establecer hora actual (redondeada a la próxima media hora)
            const now = new Date();
            const minutes = now.getMinutes();
            const roundedMinutes = minutes < 30 ? 30 : 0;
            const hours = minutes < 30 ? now.getHours() : now.getHours() + 1;
            const roundedHours = hours.toString().padStart(2, '0');
            const timeValue = `${roundedHours}:${roundedMinutes === 0 ? '00' : roundedMinutes}`;
            document.getElementById('hora').value = timeValue;
        });
        
        // Cargar notificaciones
        function loadNotifications() {
            fetch('/notifications')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('notifications-list');
                    const noNotifications = document.getElementById('no-notifications');
                    const countBadge = document.getElementById('notification-count');
                    
                    // Limpiar contenedor excepto el elemento "no-notifications"
                    while (container.firstChild && container.firstChild !== noNotifications) {
                        container.removeChild(container.firstChild);
                    }
                    
                    if (data.length === 0) {
                        noNotifications.style.display = 'block';
                        countBadge.textContent = '0';
                    } else {
                        noNotifications.style.display = 'none';
                        countBadge.textContent = data.length;
                        
                        // Agregar notificaciones al contenedor
                        data.forEach(notification => {
                            const item = document.createElement('a');
                            item.href = notification.url || '#';
                            item.className = 'dropdown-item d-flex align-items-center';
                            
                            let iconClass = 'fa-bell';
                            let bgColor = 'bg-primary';
                            
                            if (notification.type === 'appointment') {
                                iconClass = 'fa-calendar-alt';
                                bgColor = 'bg-info';
                            } else if (notification.type === 'payment') {
                                iconClass = 'fa-money-bill-wave';
                                bgColor = 'bg-success';
                            } else if (notification.type === 'alert') {
                                iconClass = 'fa-exclamation-circle';
                                bgColor = 'bg-warning';
                            }
                            
                            item.innerHTML = `
                                <div class="me-3">
                                    <div class="icon-circle ${bgColor}">
                                        <i class="fas ${iconClass} text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500">${notification.time}</div>
                                    <span>${notification.message}</span>
                                </div>
                            `;
                            
                            container.insertBefore(item, noNotifications);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error al cargar notificaciones:', error);
                });
        }
        
        // Cargar notificaciones al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            loadNotifications();
            
            // Actualizar notificaciones cada 5 minutos
            setInterval(loadNotifications, 5 * 60 * 1000);
        });
    </script>
    
    <!-- Scripts específicos de la página -->
    @stack('scripts')
</body>
</html>