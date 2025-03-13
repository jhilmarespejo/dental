<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Muestra la página del dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Datos demo para el dashboard
        $stats = [
            'total_patients' => 347,
            'appointments_today' => 12,
            'active_treatments' => 68,
            'revenue_month' => 42500,
            'pending_payments' => 15800,
        ];
        
        $recent_patients = [
            [
                'id' => 1,
                'name' => 'Juan Pérez',
                'date' => '2025-03-10',
                'treatment' => 'Limpieza dental',
                'status' => 'Completado'
            ],
            [
                'id' => 2,
                'name' => 'María González',
                'date' => '2025-07-08',
                'treatment' => 'Endodoncia',
                'status' => 'En proceso'
            ],
            [
                'id' => 3,
                'name' => 'Carlos Rodríguez',
                'date' => '2025-07-05',
                'treatment' => 'Extracción',
                'status' => 'Completado'
            ],
            [
                'id' => 4,
                'name' => 'Ana Martínez',
                'date' => '2025-07-03',
                'treatment' => 'Ortodoncia',
                'status' => 'En proceso'
            ],
            [
                'id' => 5,
                'name' => 'Roberto López',
                'date' => '2025-07-01',
                'treatment' => 'Implante dental',
                'status' => 'Programado'
            ],
        ];
        
        $today_appointments = [
            [
                'time' => '09:00',
                'patient' => 'Sofía García',
                'reason' => 'Revisión ortodoncia'
            ],
            [
                'time' => '10:30',
                'patient' => 'Diego Flores',
                'reason' => 'Extracción muela del juicio'
            ],
            [
                'time' => '12:00',
                'patient' => 'Patricia Rojas',
                'reason' => 'Limpieza dental'
            ],
            [
                'time' => '15:00',
                'patient' => 'Miguel Torres',
                'reason' => 'Endodoncia pieza 27'
            ],
            [
                'time' => '16:30',
                'patient' => 'Laura Vargas',
                'reason' => 'Evaluación inicial'
            ],
            [
                'time' => '18:00',
                'patient' => 'Fernando Castro',
                'reason' => 'Control post-operatorio'
            ],
        ];
        
        return view('dashboard', compact('stats', 'recent_patients', 'today_appointments'));
    }
}