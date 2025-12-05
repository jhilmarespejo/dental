<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;
    
    protected $table = 'citas';

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];
    
    protected $fillable = [
        'paciente_id', 
        'profesional_id', 
        'fecha_hora', 
        'duracion',
        'estado', 
        'motivo', 
        'notas'
    ];
    
    protected $dates = [
        'fecha_hora',
        'created_at'
    ];
    
    // Relaciones
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'paciente_id');
    }
    
    public function professional()
    {
        return $this->belongsTo(Professional::class, 'profesional_id');
    }
    
    public function treatments()
    {
        return $this->hasMany(TreatmentPerformed::class, 'cita_id');
    }
}
