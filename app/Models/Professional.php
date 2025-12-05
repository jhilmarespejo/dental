<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Professional extends Model
{
    use HasFactory;
    
    protected $table = 'profesionales';
    
    protected $fillable = [
        'nombres', 
        'apellidos', 
        'especialidad', 
        'email', 
        'telefono', 
        'ci',
        'ci_exp',
        'estado',
    ];
    
    protected $dates = [
        'created_at',
        'updated_at',
    ];
    
    // Relaciones
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'profesional_id');
    }
    
    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'paciente_profesional', 'profesional_id', 'paciente_id')
                    ->withPivot('fecha_asignacion', 'notas')
                    ->withTimestamps();
    }
    
    public function treatments()
    {
        return $this->hasMany(TreatmentPerformed::class, 'profesional_id');
    }
    
    // Accessors
    public function getFullNameAttribute()
    {
        return $this->nombres . ' ' . $this->apellidos;
    }
}