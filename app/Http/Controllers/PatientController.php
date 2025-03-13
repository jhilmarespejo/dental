<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Muestra el listado de pacientes
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Datos demo para pacientes
        $patients = [
            [
                'id' => 1,
                'name' => 'Juan Pérez',
                'lastname' => 'González',
                'age' => 35,
                'phone' => '+591 70707070',
                'last_visit' => '2024-06-15',
                'email' => 'juan.perez@example.com'
            ],
            [
                'id' => 2,
                'name' => 'María',
                'lastname' => 'González',
                'age' => 42,
                'phone' => '+591 71717171',
                'last_visit' => '2024-05-22',
                'email' => 'maria.gonzalez@example.com'
            ],
            [
                'id' => 3,
                'name' => 'Carlos',
                'lastname' => 'Rodríguez',
                'age' => 29,
                'phone' => '+591 72727272',
                'last_visit' => '2024-07-03',
                'email' => 'carlos.rodriguez@example.com'
            ],
            [
                'id' => 4,
                'name' => 'Ana',
                'lastname' => 'Martínez',
                'age' => 50,
                'phone' => '+591 73737373',
                'last_visit' => '2024-06-10',
                'email' => 'ana.martinez@example.com'
            ],
            [
                'id' => 5,
                'name' => 'Roberto',
                'lastname' => 'López',
                'age' => 37,
                'phone' => '+591 74747474',
                'last_visit' => '2024-07-01',
                'email' => 'roberto.lopez@example.com'
            ],
            [
                'id' => 6,
                'name' => 'Sofía',
                'lastname' => 'García',
                'age' => 22,
                'phone' => '+591 75757575',
                'last_visit' => '2024-07-12',
                'email' => 'sofia.garcia@example.com'
            ],
            [
                'id' => 7,
                'name' => 'Diego',
                'lastname' => 'Flores',
                'age' => 45,
                'phone' => '+591 76767676',
                'last_visit' => '2024-05-18',
                'email' => 'diego.flores@example.com'
            ],
            [
                'id' => 8,
                'name' => 'Patricia',
                'lastname' => 'Rojas',
                'age' => 31,
                'phone' => '+591 77777777',
                'last_visit' => '2024-06-20',
                'email' => 'patricia.rojas@example.com'
            ],
            [
                'id' => 9,
                'name' => 'Miguel',
                'lastname' => 'Torres',
                'age' => 58,
                'phone' => '+591 78787878',
                'last_visit' => '2024-07-05',
                'email' => 'miguel.torres@example.com'
            ],
            [
                'id' => 10,
                'name' => 'Laura',
                'lastname' => 'Vargas',
                'age' => 27,
                'phone' => '+591 79797979',
                'last_visit' => '2024-06-25',
                'email' => 'laura.vargas@example.com'
            ]
        ];

        return view('patients.index', compact('patients'));
    }

    /**
     * Muestra el formulario para crear un nuevo paciente
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Muestra los detalles de un paciente específico
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Datos demo del paciente
        $patient = [
            'id' => $id,
            'name' => 'Juan',
            'lastname' => 'Pérez',
            'birthdate' => '1988-05-15',
            'age' => 35,
            'gender' => 'M',
            'phone' => '+591 70707070',
            'email' => 'juan.perez@example.com',
            'ci' => '1234567',
            'ci_exp' => 'LP',
            'address' => 'Av. 6 de Agosto #123, La Paz',
            'medical_conditions' => 'Hipertensión',
            'allergies' => 'Penicilina',
            'last_visit' => '2024-06-15',
            'created_at' => '2020-01-15'
        ];

        // Datos demo del historial de tratamientos
        $treatments = [
            [
                'id' => 1,
                'date' => '2024-06-15',
                'diagnosis' => 'Caries',
                'treatment' => 'Empaste',
                'tooth' => '36',
                'cost' => 250.00,
                'paid' => 250.00,
                'balance' => 0.00,
                'status' => 'Completado'
            ],
            [
                'id' => 2,
                'date' => '2024-05-10',
                'diagnosis' => 'Gingivitis',
                'treatment' => 'Limpieza profunda',
                'tooth' => 'General',
                'cost' => 350.00,
                'paid' => 350.00,
                'balance' => 0.00,
                'status' => 'Completado'
            ],
            [
                'id' => 3,
                'date' => '2024-03-22',
                'diagnosis' => 'Fractura dental',
                'treatment' => 'Reconstrucción',
                'tooth' => '11',
                'cost' => 500.00,
                'paid' => 500.00,
                'balance' => 0.00,
                'status' => 'Completado'
            ],
            [
                'id' => 4,
                'date' => '2022-11-05',
                'diagnosis' => 'Pulpitis',
                'treatment' => 'Endodoncia',
                'tooth' => '47',
                'cost' => 800.00,
                'paid' => 800.00,
                'balance' => 0.00,
                'status' => 'Completado'
            ],
            [
                'id' => 5,
                'date' => '2022-07-18',
                'diagnosis' => 'Periodontitis',
                'treatment' => 'Curetaje',
                'tooth' => 'General',
                'cost' => 650.00,
                'paid' => 650.00,
                'balance' => 0.00,
                'status' => 'Completado'
            ]
        ];

        // Datos demo de las próximas citas
        $upcoming_appointments = [
            [
                'id' => 1,
                'date' => '2024-08-15',
                'time' => '10:30',
                'reason' => 'Control de ortodoncia'
            ]
        ];

        return view('patients.show', compact('patient', 'treatments', 'upcoming_appointments'));
    }

    /**
     * Muestra el formulario para editar un paciente
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // Datos demo del paciente
        $patient = [
            'id' => $id,
            'name' => 'Juan',
            'lastname' => 'Pérez',
            'birthdate' => '1988-05-15',
            'gender' => 'M',
            'phone' => '+591 70707070',
            'email' => 'juan.perez@example.com',
            'ci' => '1234567',
            'ci_exp' => 'LP',
            'address' => 'Av. 6 de Agosto #123, La Paz',
            'medical_conditions' => 'Hipertensión',
            'allergies' => 'Penicilina'
        ];

        return view('patients.edit', compact('patient'));
    }
}