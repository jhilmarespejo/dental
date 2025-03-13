<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Iniciar Sesión - Consultorio</title>
    
    <!-- Styles -->
    @vite(['resources/sass/app.scss'])
    
    <style>
        body {
            background: linear-gradient(135deg, #4e73df 0%, #36b9cc 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            max-width: 500px;
            width: 100%;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .login-header {
            background: linear-gradient(to right, #4e73df, #36b9cc);
            color: white;
            border-radius: 1rem 1rem 0 0;
            padding: 2rem 1.5rem;
            text-align: center;
        }
        
        .login-logo {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .login-body {
            padding: 2rem;
        }
        
        .form-floating {
            margin-bottom: 1.5rem;
        }
        
        .btn-login {
            padding: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card login-card">
                    <div class="login-header">
                        <div class="login-logo">
                            <i class="fas fa-tooth"></i>
                        </div>
                        <h1 class="h4 mb-0">Consultorio</h1>
                        <p class="mb-0">Sistema de Gestión para Consultorios Dentales</p>
                    </div>
                    
                    <div class="login-body">
                        <h2 class="text-center mb-4">Iniciar Sesión</h2>
                        
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('login.post') }}">
                            @csrf
                            
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="email" name="email" placeholder="nombre@ejemplo.com" required autofocus>
                                <label for="email">Correo electrónico</label>
                            </div>
                            
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                                <label for="password">Contraseña</label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">
                                    Recordarme
                                </label>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-login">
                                    <i class="fas fa-sign-in-alt mr-2"></i> Ingresar
                                </button>
                            </div>
                            
                            <div class="text-center mt-3">
                                <a href="#" class="small">¿Olvidaste tu contraseña?</a>
                            </div>
                        </form>
                        
                        <!-- Demo mode buttons -->
                        <div class="mt-4">
                            <div class="d-grid">
                                <a href="{{ route('dashboard') }}" class="btn btn-success btn-login">
                                    <i class="fas fa-user-md mr-2"></i> Entrar en Modo Demo
                                </a>
                            </div>
                            <div class="text-center mt-3">
                                <small class="text-muted">Esta es una versión de demostración</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3 text-white">
                    <small>&copy; 2025 Consultorio. Todos los derechos reservados.</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- FontAwesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>