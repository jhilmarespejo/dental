import './bootstrap';
import '../css/app.css';
import * as bootstrap from 'bootstrap';
import Swal from 'sweetalert2';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import Chart from 'chart.js/auto';

// Make libraries available globally
window.bootstrap = bootstrap;
window.Swal = Swal;
window.Chart = Chart;

// Initialize tooltips and popovers
document.addEventListener('DOMContentLoaded', () => {
    // Enable tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Enable popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Initialize calendar if element exists
    const calendarEl = document.getElementById('appointments-calendar');
    if (calendarEl) {
        const calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
            initialView: 'timeGridWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            slotMinTime: '08:00:00',
            slotMaxTime: '20:00:00',
            editable: true,
            selectable: true,
            selectMirror: true,
            dayMaxEvents: true,
            events: [], // In a real app, events would be loaded from the server
            select: function(info) {
                // Demo appointment creation
                Swal.fire({
                    title: 'New Appointment',
                    html: `
                        <div class="mb-3">
                            <label for="patient-select" class="form-label">Patient</label>
                            <select id="patient-select" class="form-select">
                                <option selected>Choose patient...</option>
                                <option value="1">Juan Pérez</option>
                                <option value="2">María González</option>
                                <option value="3">Carlos Rodríguez</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="appt-type" class="form-label">Appointment Type</label>
                            <select id="appt-type" class="form-select">
                                <option selected>Choose type...</option>
                                <option>Regular Checkup</option>
                                <option>Root Canal</option>
                                <option>Teeth Cleaning</option>
                                <option>Filling</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="appt-notes" class="form-label">Notes</label>
                            <textarea id="appt-notes" class="form-control"></textarea>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return new Promise((resolve) => {
                            setTimeout(() => {
                                resolve({
                                    patient: document.getElementById('patient-select').value,
                                    type: document.getElementById('appt-type').value,
                                    notes: document.getElementById('appt-notes').value
                                });
                            }, 500);
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        calendar.addEvent({
                            title: `${document.getElementById('patient-select').options[document.getElementById('patient-select').selectedIndex].text} - ${document.getElementById('appt-type').value}`,
                            start: info.startStr,
                            end: info.endStr,
                            allDay: info.allDay
                        });
                        Swal.fire('Success!', 'Appointment has been created.', 'success');
                    }
                });
                calendar.unselect();
            },
            eventClick: function(info) {
                // Show appointment details on click
                Swal.fire({
                    title: 'Appointment Details',
                    html: `
                        <div class="text-start">
                            <p><strong>Patient:</strong> ${info.event.title.split(' - ')[0]}</p>
                            <p><strong>Type:</strong> ${info.event.title.split(' - ')[1]}</p>
                            <p><strong>Date:</strong> ${new Date(info.event.start).toLocaleDateString()}</p>
                            <p><strong>Time:</strong> ${new Date(info.event.start).toLocaleTimeString()} - ${new Date(info.event.end).toLocaleTimeString()}</p>
                        </div>
                    `,
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Edit',
                    denyButtonText: 'Delete',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Edit functionality would be implemented here
                        Swal.fire('Edit mode', 'This would open the edit form in a real app', 'info');
                    } else if (result.isDenied) {
                        info.event.remove();
                        Swal.fire('Deleted!', 'The appointment has been removed.', 'success');
                    }
                });
            }
        });
        calendar.render();
    }

    // Initialize charts if elements exist
    initializeCharts();
    
    // Initialize demo functionality
    initializeDemoActions();
});

function initializeCharts() {
    // Patients by age chart
    const ageChartEl = document.getElementById('patients-by-age-chart');
    if (ageChartEl) {
        new Chart(ageChartEl, {
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
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Monthly revenue chart
    const revenueChartEl = document.getElementById('monthly-revenue-chart');
    if (revenueChartEl) {
        new Chart(revenueChartEl, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Revenue',
                    data: [3200, 2800, 4100, 3700, 4600, 5200],
                    backgroundColor: '#4e73df'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Treatments by type chart
    const treatmentsChartEl = document.getElementById('treatments-by-type-chart');
    if (treatmentsChartEl) {
        new Chart(treatmentsChartEl, {
            type: 'doughnut',
            data: {
                labels: ['Checkup', 'Cleaning', 'Filling', 'Root Canal', 'Extraction', 'Other'],
                datasets: [{
                    data: [30, 25, 20, 10, 8, 7],
                    backgroundColor: [
                        '#4e73df',
                        '#1cc88a',
                        '#36b9cc',
                        '#f6c23e',
                        '#e74a3b',
                        '#5a5c69'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
}

function initializeDemoActions() {
    // Add patient demo
    const addPatientBtn = document.getElementById('add-patient-btn');
    if (addPatientBtn) {
        addPatientBtn.addEventListener('click', function() {
            Swal.fire({
                title: 'New Patient',
                html: `
                    <div class="mb-3">
                        <label for="patient-name" class="form-label">First Name</label>
                        <input type="text" id="patient-name" class="form-control" placeholder="First Name">
                    </div>
                    <div class="mb-3">
                        <label for="patient-lastname" class="form-label">Last Name</label>
                        <input type="text" id="patient-lastname" class="form-control" placeholder="Last Name">
                    </div>
                    <div class="mb-3">
                        <label for="patient-dob" class="form-label">Date of Birth</label>
                        <input type="date" id="patient-dob" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="patient-phone" class="form-label">Phone Number</label>
                        <input type="tel" id="patient-phone" class="form-control" placeholder="Phone Number">
                    </div>
                    <div class="mb-3">
                        <label for="patient-email" class="form-label">Email</label>
                        <input type="email" id="patient-email" class="form-control" placeholder="Email">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Save',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return new Promise((resolve) => {
                        setTimeout(() => {
                            resolve({
                                name: document.getElementById('patient-name').value,
                                lastname: document.getElementById('patient-lastname').value,
                                dob: document.getElementById('patient-dob').value,
                                phone: document.getElementById('patient-phone').value,
                                email: document.getElementById('patient-email').value
                            });
                        }, 800);
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Success!', 'Patient has been added to the system.', 'success');
                    // In a real app, this would update the UI with the new patient
                }
            });
        });
    }

    // Patient search demo
    const searchPatientForm = document.getElementById('search-patient-form');
    if (searchPatientForm) {
        searchPatientForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchInput = document.getElementById('search-patient-input');
            if (searchInput.value.trim() !== '') {
                // Show loader
                const loader = document.createElement('div');
                loader.className = 'loader';
                loader.innerHTML = '<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>';
                document.body.appendChild(loader);
                
                // Simulate search delay
                setTimeout(() => {
                    document.body.removeChild(loader);
                    
                    // Hardcoded demo results
                    const resultsContainer = document.getElementById('search-results');
                    if (resultsContainer) {
                        resultsContainer.innerHTML = `
                            <div class="list-group">
                                <a href="#" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">Juan Pérez</h5>
                                        <small>35 years</small>
                                    </div>
                                    <p class="mb-1">+591 70707070</p>
                                    <small>Last visit: 2025-01-15</small>
                                </a>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">María González</h5>
                                        <small>42 years</small>
                                    </div>
                                    <p class="mb-1">+591 71717171</p>
                                    <small>Last visit: 2025-02-22</small>
                                </a
                                <a href="#" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">Carlos Rodríguez</h5>
                                        <small>29 years</small>
                                    </div>
                                    <p class="mb-1">+591 72727272</p>
                                    <small>Last visit: 2025-03-03</small>
                                </a>
                            </div>
                        `;
                    }
                }, 800);
            }
        });
    }
    
    // Add treatment demo
    const addTreatmentBtns = document.querySelectorAll('.add-treatment-btn');
    if (addTreatmentBtns.length > 0) {
        addTreatmentBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                Swal.fire({
                    title: 'New Treatment',
                    html: `
                        <div class="mb-3">
                            <label for="treatment-date" class="form-label">Date</label>
                            <input type="date" id="treatment-date" class="form-control" value="${new Date().toISOString().split('T')[0]}">
                        </div>
                        <div class="mb-3">
                            <label for="treatment-diagnosis" class="form-label">Diagnosis</label>
                            <select id="treatment-diagnosis" class="form-select">
                                <option>Cavity</option>
                                <option>Gingivitis</option>
                                <option>Periodontitis</option>
                                <option>Dental fracture</option>
                                <option>Other (specify)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="treatment-procedure" class="form-label">Treatment</label>
                            <select id="treatment-procedure" class="form-select">
                                <option>Filling</option>
                                <option>Root canal</option>
                                <option>Extraction</option>
                                <option>Cleaning</option>
                                <option>Other (specify)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="treatment-tooth" class="form-label">Tooth</label>
                            <input type="text" id="treatment-tooth" class="form-control" placeholder="Tooth number/code">
                        </div>
                        <div class="mb-3">
                            <label for="treatment-cost" class="form-label">Cost (Bs.)</label>
                            <input type="number" id="treatment-cost" class="form-control" placeholder="0.00">
                        </div>
                        <div class="mb-3">
                            <label for="treatment-notes" class="form-label">Notes</label>
                            <textarea id="treatment-notes" class="form-control"></textarea>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return new Promise((resolve) => {
                            setTimeout(() => {
                                resolve({
                                    date: document.getElementById('treatment-date').value,
                                    diagnosis: document.getElementById('treatment-diagnosis').value,
                                    treatment: document.getElementById('treatment-procedure').value,
                                    tooth: document.getElementById('treatment-tooth').value,
                                    cost: document.getElementById('treatment-cost').value,
                                    notes: document.getElementById('treatment-notes').value
                                });
                            }, 800);
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire('Success!', 'Treatment has been added to the patient record.', 'success');
                        // In a real app, this would update the UI with the new treatment
                    }
                });
            });
        });
    }
    
    // Toggle sidebar on mobile
    const sidebarToggleBtn = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    if (sidebarToggleBtn && sidebar) {
        sidebarToggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }
}