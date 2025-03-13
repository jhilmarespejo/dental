<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Muestra el listado de citas
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Datos demo para las citas
        $today = [
            [
                'id' => 1,
                'time' => '09:00',
                'patient' => 'Sofía García',
                'reason' => 'Revisión ortodoncia',
                'status' => 'Confirmada'
            ],
            [
                'id' => 2,
                'time' => '10:30',
                'patient' => 'Diego Flores',
                'reason' => 'Extracción muela del juicio',
                'status' => 'Confirmada'
            ],
            [
                'id' => 3,
                'time' => '12:00',
                'patient' => 'Patricia Rojas',
                'reason' => 'Limpieza dental',
                'status' => 'Pendiente'
            ],
            [
                'id' => 4,
                'time' => '15:00',
                'patient' => 'Miguel Torres',
                'reason' => 'Endodoncia pieza 27',
                'status' => 'Confirmada'
            ],
            [
                'id' => 5,
                'time' => '16:30',
                'patient' => 'Laura Vargas',
                'reason' => 'Evaluación inicial',
                'status' => 'Pendiente'
            ],
            [
                'id' => 6,
                'time' => '18:00',
                'patient' => 'Fernando Castro',
                'reason' => 'Control post-operatorio',
                'status' => 'Confirmada'
            ],
        ];

        $upcoming = [
            [
                'id' => 7,
                'date' => '2025-03-11',
                'time' => '09:30',
                'patient' => 'Carmen Mendoza',
                'reason' => 'Evaluación brackets',
                'status' => 'Confirmada'
            ],
            [
                'id' => 8,
                'date' => '2025-03-11',
                'time' => '11:00',
                'patient' => 'Javier Gutiérrez',
                'reason' => 'Revisión periódica',
                'status' => 'Pendiente'
            ],
            [
                'id' => 9,
                'date' => '2025-03-11',
                'time' => '14:30',
                'patient' => 'Marcela Suárez',
                'reason' => 'Blanqueamiento dental',
                'status' => 'Confirmada'
            ],
            [
                'id' => 10,
                'date' => '2025-03-12',
                'time' => '10:00',
                'patient' => 'Ricardo Molina',
                'reason' => 'Revisión prótesis',
                'status' => 'Pendiente'
            ],
            [
                'id' => 11,
                'date' => '2025-03-12',
                'time' => '12:30',
                'patient' => 'Verónica Paz',
                'reason' => 'Empaste pieza 14',
                'status' => 'Confirmada'
            ],
            [
                'id' => 12,
                'date' => '2025-03-12',
                'time' => '16:00',
                'patient' => 'Gustavo Ramírez',
                'reason' => 'Evaluación ortodoncia',
                'status' => 'Confirmada'
            ],
        ];

        return view('appointments.index', compact('today', 'upcoming'));
    }

    /**
     * Muestra el formulario para crear una nueva cita
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Lista demo de pacientes
        $patients = [
            ['id' => 1, 'name' => 'Juan Pérez'],
            ['id' => 2, 'name' => 'María González'],
            ['id' => 3, 'name' => 'Carlos Rodríguez'],
            ['id' => 4, 'name' => 'Ana Martínez'],
            ['id' => 5, 'name' => 'Roberto López'],
            ['id' => 6, 'name' => 'Sofía García'],
            ['id' => 7, 'name' => 'Diego Flores'],
            ['id' => 8, 'name' => 'Patricia Rojas'],
            ['id' => 9, 'name' => 'Miguel Torres'],
            ['id' => 10, 'name' => 'Laura Vargas'],
        ];

        return view('appointments.create', compact('patients'));
    }

    /**
     * Muestra los detalles de una cita específica
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Datos demo de la cita
        $appointment = [
            'id' => $id,
            'date' => '2025-03-10',
            'time' => '10:30',
            'patient_id' => 2,
            'patient_name' => 'Diego Flores',
            'patient_phone' => '+591 76767676',
            'patient_email' => 'diego.flores@example.com',
            'reason' => 'Extracción muela del juicio',
            'status' => 'Confirmada',
            'notes' => 'Paciente con ansiedad dental, se recomienda sedación.',
            'created_at' => '2025-02-25 14:30:00'
        ];

        // Historial de citas anteriores
        $previous_appointments = [
            [
                'date' => '2025-03-18',
                'reason' => 'Radiografía dental',
                'notes' => 'Se programó extracción de muela del juicio.'
            ],
            [
                'date' => '2025-03-02',
                'reason' => 'Limpieza dental',
                'notes' => 'Completada sin complicaciones.'
            ],
            [
                'date' => '2024-12-15',
                'reason' => 'Evaluación general',
                'notes' => 'Primera visita del paciente.'
            ]
        ];

        return view('appointments.show', compact('appointment', 'previous_appointments'));
    }
}