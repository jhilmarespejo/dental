# Consultorio - Sistema de Gestión para Consultorios Dentales

![Consultorio Logo](https://i.ibb.co/2PBL4Nc/dentalcare-pro-logo.png)

## Descripción

Consultorio es un sistema completo de gestión para consultorios dentales que proporciona herramientas eficientes para la administración de pacientes, citas, tratamientos y finanzas. Esta aplicación web moderna está diseñada para optimizar los procesos administrativos y mejorar la experiencia tanto del personal como de los pacientes.

## Características Principales

- **Gestión de Pacientes**: Registro detallado, historial médico e información de contacto.
- **Agenda de Citas**: Calendario interactivo para programar y administrar citas.
- **Tratamientos**: Registro y seguimiento de diagnósticos y tratamientos.
- **Pagos**: Control de pagos, saldos pendientes y facturación.
- **Odontograma**: Representación visual del estado dental de los pacientes.
- **Reportes**: Estadísticas y análisis financiero para la toma de decisiones.
- **Diseño Responsivo**: Interfaz adaptable a dispositivos móviles y de escritorio.

## Tecnologías Utilizadas

- **Backend**: Laravel 11 (PHP)
- **Frontend**: Bootstrap 5, JavaScript, SASS
- **Bibliotecas**: Chart.js, FullCalendar, SweetAlert2, FontAwesome
- **Base de Datos**: MySQL

## Instalación (Modo Demo)

Siga estos pasos para instalar y ejecutar el sistema en modo demostración:

1. **Clonar el repositorio**:
   ```
   git clone https://github.com/tuusuario/dental-clinic-app.git
   cd dental-clinic-app
   ```

2. **Instalar dependencias de PHP**:
   ```
   composer install
   ```

3. **Instalar dependencias de Node.js**:
   ```
   npm install
   ```

4. **Configurar el archivo .env**:
   ```
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configurar la base de datos en el archivo .env**:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cdental2
   DB_USERNAME=root
   DB_PASSWORD=tu-contraseña
   ```

6. **Compilar los activos**:
   ```
   npm run dev
   ```

7. **Iniciar el servidor**:
   ```
   php artisan serve
   ```

8. **Acceder al sistema**:
   Abra su navegador y visite `http://localhost:8000`

## Modo Demo

Este sistema actualmente está configurado en modo demostración, lo que significa que:

- No se requiere autenticación real (puede usar el botón "Entrar en Modo Demo").
- No se realizan transacciones reales con la base de datos.
- Los datos mostrados son estáticos y solo para propósitos de demostración.
- Todas las funcionalidades de creación, edición y eliminación son simuladas.

## Personalización

Para convertir este demo en una aplicación completamente funcional, se necesitaría:

1. Implementar la autenticación real de usuarios con roles y permisos.
2. Configurar los modelos y migraciones para la base de datos.
3. Desarrollar los controladores para manejar operaciones CRUD reales.
4. Implementar validaciones de formularios y manejo de errores.
5. Configurar notificaciones por email para recordatorios de citas.

## Capturas de Pantalla

![Dashboard](https://i.ibb.co/YyB8rLt/dashboard-preview.jpg)

![Pacientes](https://i.ibb.co/mFkVmyK/patients-preview.jpg)

![Citas](https://i.ibb.co/5KV9k7J/appointments-preview.jpg)

## Contacto

Para más información o consultas sobre la implementación completa del sistema, por favor contactar a:

Email: tu.email@ejemplo.com
Teléfono: +123 456 7890

---

&copy; 2025 Consultorio. Todos los derechos reservados.