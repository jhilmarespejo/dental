@extends('layouts.app')

@section('title', 'Pacientes - Consultorio')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pacientes</h1>
        <div>
            <a href="#" class="btn btn-primary" id="add-patient-btn">
                <i class="fas fa-user-plus"></i> Nuevo Paciente
            </a>
        </div>
    </div>

    <!-- Search Box -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-body">
                    <form id="search-patient-form">
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <label for="search-patient-input" class="col-form-label">Buscar:</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="search-patient-input" class="form-control" 
                                    placeholder="Nombre, apellido, CI o teléfono">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Results -->
    <div class="row mb-4" id="search-results" style="display: none;">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Resultados de la búsqueda</h6>
                </div>
                <div class="card-body">
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
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-file-csv fa-sm fa-fw me-2 text-gray-400"></i> Exportar a CSV
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-print fa-sm fa-fw me-2 text-gray-400"></i> Imprimir lista
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="patients-table" width="100%" cellspacing="0">
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
                            <td>{{ $patient['id'] }}</td>
                            <td>{{ $patient['name'] }}</td>
                            <td>{{ $patient['lastname'] }}</td>
                            <td>{{ $patient['age'] }}</td>
                            <td>{{ $patient['phone'] }}</td>
                            <td>{{ $patient['last_visit'] }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group" aria-label="Patient actions">
                                    <a href="{{ route('patients.show', $patient['id']) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-warning edit-patient-btn" data-patient-id="{{ $patient['id'] }}" data-bs-toggle="tooltip" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-success add-treatment-btn" data-patient-id="{{ $patient['id'] }}" data-bs-toggle="tooltip" title="Nuevo tratamiento">
                                        <i class="fas fa-plus-circle"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Siguiente</a>
                        </li>
                    </ul>
                </nav>
            </div>
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
                                    <div class="h5 mb-0 fw-bold">{{ count($patients) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-success text-white shadow">
                                <div class="card-body">
                                    <div class="text-xs fw-bold text-uppercase mb-1">Nuevos este mes</div>
                                    <div class="h5 mb-0 fw-bold">12</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-info text-white shadow">
                                <div class="card-body">
                                    <div class="text-xs fw-bold text-uppercase mb-1">Citas activas</div>
                                    <div class="h5 mb-0 fw-bold">24</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-warning text-white shadow">
                                <div class="card-body">
                                    <div class="text-xs fw-bold text-uppercase mb-1">Tratamientos pendientes</div>
                                    <div class="h5 mb-0 fw-bold">36</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Patient Modal -->
    <div class="modal fade" id="newPatientModal" tabindex="-1" aria-labelledby="newPatientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newPatientModalLabel">Nuevo Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="patientForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="patient-name" class="form-label">Nombres</label>
                                <input type="text" class="form-control" id="patient-name" placeholder="Nombres">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="patient-lastname" class="form-label">Apellidos</label>
                                <input type="text" class="form-control" id="patient-lastname" placeholder="Apellidos">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="patient-dob" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="patient-dob">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="patient-gender" class="form-label">Género</label>
                                <select class="form-select" id="patient-gender">
                                    <option value="">Seleccionar</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="patient-phone" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="patient-phone" placeholder="Teléfono">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="patient-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="patient-email" placeholder="Email">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="patient-ci" class="form-label">CI</label>
                                <input type="text" class="form-control" id="patient-ci" placeholder="CI">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="patient-ci-exp" class="form-label">Exp.</label>
                                <select class="form-select" id="patient-ci-exp">
                                    <option value="">Seleccionar</option>
                                    <option value="LP">LP</option>
                                    <option value="SC">SC</option>
                                    <option value="CB">CB</option>
                                    <option value="OR">OR</option>
                                    <option value="PT">PT</option>
                                    <option value="TJ">TJ</option>
                                    <option value="BE">BE</option>
                                    <option value="PD">PD</option>
                                    <option value="CH">CH</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="patient-address" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="patient-address" placeholder="Dirección">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="patient-allergies" class="form-label">Alergias</label>
                                <textarea class="form-control" id="patient-allergies" rows="2" placeholder="Alergias"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="patient-conditions" class="form-label">Condiciones Médicas</label>
                                <textarea class="form-control" id="patient-conditions" rows="2" placeholder="Condiciones Médicas"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="savePatientBtn">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Patient Modal -->
    <div class="modal fade" id="editPatientModal" tabindex="-1" aria-labelledby="editPatientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPatientModalLabel">Editar Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editPatientForm">
                        <input type="hidden" id="edit-patient-id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit-patient-name" class="form-label">Nombres</label>
                                <input type="text" class="form-control" id="edit-patient-name" placeholder="Nombres">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit-patient-lastname" class="form-label">Apellidos</label>
                                <input type="text" class="form-control" id="edit-patient-lastname" placeholder="Apellidos">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit-patient-dob" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="edit-patient-dob">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit-patient-gender" class="form-label">Género</label>
                                <select class="form-select" id="edit-patient-gender">
                                    <option value="">Seleccionar</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit-patient-phone" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="edit-patient-phone" placeholder="Teléfono">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit-patient-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="edit-patient-email" placeholder="Email">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="edit-patient-ci" class="form-label">CI</label>
                                <input type="text" class="form-control" id="edit-patient-ci" placeholder="CI">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="edit-patient-ci-exp" class="form-label">Exp.</label>
                                <select class="form-select" id="edit-patient-ci-exp">
                                    <option value="">Seleccionar</option>
                                    <option value="LP">LP</option>
                                    <option value="SC">SC</option>
                                    <option value="CB">CB</option>
                                    <option value="OR">OR</option>
                                    <option value="PT">PT</option>
                                    <option value="TJ">TJ</option>
                                    <option value="BE">BE</option>
                                    <option value="PD">PD</option>
                                    <option value="CH">CH</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit-patient-address" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="edit-patient-address" placeholder="Dirección">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit-patient-allergies" class="form-label">Alergias</label>
                                <textarea class="form-control" id="edit-patient-allergies" rows="2" placeholder="Alergias"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit-patient-conditions" class="form-label">Condiciones Médicas</label>
                                <textarea class="form-control" id="edit-patient-conditions" rows="2" placeholder="Condiciones Médicas"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="updatePatientBtn">Actualizar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- New Treatment Modal -->
    <div class="modal fade" id="newTreatmentModal" tabindex="-1" aria-labelledby="newTreatmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newTreatmentModalLabel">Nuevo Tratamiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="treatmentForm">
                        <input type="hidden" id="treatment-patient-id">
                        <div class="mb-3">
                            <label for="treatment-date" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="treatment-date" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label for="treatment-diagnosis" class="form-label">Diagnóstico</label>
                            <select class="form-select" id="treatment-diagnosis">
                                <option>Caries</option>
                                <option>Gingivitis</option>
                                <option>Periodontitis</option>
                                <option>Fractura dental</option>
                                <option>Otro (especificar)</option>
                            </select>
                        </div>
                        <div class="mb-3" id="diagnosis-other-container" style="display: none;">
                            <label for="treatment-diagnosis-other" class="form-label">Especificar diagnóstico</label>
                            <input type="text" class="form-control" id="treatment-diagnosis-other">
                        </div>
                        <div class="mb-3">
                            <label for="treatment-procedure" class="form-label">Tratamiento</label>
                            <select class="form-select" id="treatment-procedure">
                                <option>Empaste</option>
                                <option>Endodoncia</option>
                                <option>Extracción</option>
                                <option>Limpieza</option>
                                <option>Otro (especificar)</option>
                            </select>
                        </div>
                        <div class="mb-3" id="procedure-other-container" style="display: none;">
                            <label for="treatment-procedure-other" class="form-label">Especificar tratamiento</label>
                            <input type="text" class="form-control" id="treatment-procedure-other">
                        </div>
                        <div class="mb-3">
                            <label for="treatment-tooth" class="form-label">Pieza dental</label>
                            <input type="text" class="form-control" id="treatment-tooth" placeholder="Número/código de pieza">
                        </div>
                        <div class="mb-3">
                            <label for="treatment-cost" class="form-label">Costo (Bs.)</label>
                            <input type="number" class="form-control" id="treatment-cost" placeholder="0.00">
                        </div>
                        <div class="mb-3">
                            <label for="treatment-notes" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="treatment-notes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveTreatmentBtn">Guardar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar el gráfico de pacientes por edad
        const ageChartEl = document.getElementById('patients-by-age-chart');
        const ageChart = new Chart(ageChartEl, {
            type: 'pie',
            data: {
                labels: ['0-18', '19-35', '36-50', '51-65', '65+'],
                datasets: [{
                    data: [15, 25, 30, 20, 10],
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
        
        // Buscar pacientes
        document.getElementById('search-patient-form').addEventListener('submit', function(e) {
            e.preventDefault();
            document.getElementById('search-results').style.display = 'block';
        });
        
        // Nuevo paciente
        document.getElementById('add-patient-btn').addEventListener('click', function(e) {
            e.preventDefault();
            const patientModal = new bootstrap.Modal(document.getElementById('newPatientModal'));
            patientModal.show();
        });
        
        // Guardar nuevo paciente
        document.getElementById('savePatientBtn').addEventListener('click', function() {
            const patientModal = bootstrap.Modal.getInstance(document.getElementById('newPatientModal'));
            patientModal.hide();
            
            // Mostrar alerta de éxito
            const alertPlaceholder = document.createElement('div');
            alertPlaceholder.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-4" role="alert" style="z-index: 1050;">
                    <strong>¡Éxito!</strong> Paciente registrado correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            document.body.appendChild(alertPlaceholder);
            
            // Eliminar alerta después de 3 segundos
            setTimeout(() => {
                alertPlaceholder.remove();
            }, 3000);
        });
        
        // Editar paciente
        document.querySelectorAll('.edit-patient-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const patientId = this.getAttribute('data-patient-id');
                document.getElementById('edit-patient-id').value = patientId;
                
                // En una aplicación real cargaríamos los datos del paciente
                // Para el demo, prellenamos con datos de ejemplo
                document.getElementById('edit-patient-name').value = "Juan";
                document.getElementById('edit-patient-lastname').value = "Pérez";
                document.getElementById('edit-patient-dob').value = "1988-05-15";
                document.getElementById('edit-patient-gender').value = "M";
                document.getElementById('edit-patient-phone').value = "+591 70707070";
                document.getElementById('edit-patient-email').value = "juan.perez@example.com";
                document.getElementById('edit-patient-ci').value = "1234567";
                document.getElementById('edit-patient-ci-exp').value = "LP";
                document.getElementById('edit-patient-address').value = "Av. 6 de Agosto #123, La Paz";
                document.getElementById('edit-patient-allergies').value = "Penicilina";
                document.getElementById('edit-patient-conditions').value = "Hipertensión";
                
                const editPatientModal = new bootstrap.Modal(document.getElementById('editPatientModal'));
                editPatientModal.show();
            });
        });
        
        // Actualizar paciente
        document.getElementById('updatePatientBtn').addEventListener('click', function() {
            const editPatientModal = bootstrap.Modal.getInstance(document.getElementById('editPatientModal'));
            editPatientModal.hide();
            
            // Mostrar alerta de éxito
            const alertPlaceholder = document.createElement('div');
                            alertPlaceholder.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-4" role="alert" style="z-index: 1050;">
                    <strong>¡Éxito!</strong> Datos del paciente actualizados correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            document.body.appendChild(alertPlaceholder);
            
            // Eliminar alerta después de 3 segundos
            setTimeout(() => {
                alertPlaceholder.remove();
            }, 3000);
        });
        
        // Nuevo tratamiento
        document.querySelectorAll('.add-treatment-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const patientId = this.getAttribute('data-patient-id');
                document.getElementById('treatment-patient-id').value = patientId;
                
                const treatmentModal = new bootstrap.Modal(document.getElementById('newTreatmentModal'));
                treatmentModal.show();
            });
        });
        
        // Funcionamiento para los campos "otro"
        document.getElementById('treatment-diagnosis').addEventListener('change', function() {
            const otherContainer = document.getElementById('diagnosis-other-container');
            if (this.value === 'Otro (especificar)') {
                otherContainer.style.display = 'block';
            } else {
                otherContainer.style.display = 'none';
            }
        });
        
        document.getElementById('treatment-procedure').addEventListener('change', function() {
            const otherContainer = document.getElementById('procedure-other-container');
            if (this.value === 'Otro (especificar)') {
                otherContainer.style.display = 'block';
            } else {
                otherContainer.style.display = 'none';
            }
        });
        
        // Guardar nuevo tratamiento
        document.getElementById('saveTreatmentBtn').addEventListener('click', function() {
            const treatmentModal = bootstrap.Modal.getInstance(document.getElementById('newTreatmentModal'));
            treatmentModal.hide();
            
            // Mostrar alerta de éxito
            const alertPlaceholder = document.createElement('div');
            alertPlaceholder.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-4" role="alert" style="z-index: 1050;">
                    <strong>¡Éxito!</strong> Tratamiento registrado correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            document.body.appendChild(alertPlaceholder);
            
            // Eliminar alerta después de 3 segundos
            setTimeout(() => {
                alertPlaceholder.remove();
            }, 3000);
        });
    });
</script>
@endpush