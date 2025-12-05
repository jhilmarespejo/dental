<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
   
    use HasFactory;
    
    protected $table = 'pacientes';
    
    protected $fillable = [
        'nombres', 
        'apellidos', 
        'fecha_nacimiento', 
        'edad',
        'genero', 
        'celular', 
        'email', 
        'ci', 
        'ci_exp', 
        'direccion',
        'alergias', 
        'condiciones_medicas', 
        'fecha_ultima_visita'
    ];
    
    protected $dates = [
        'fecha_nacimiento',
        'fecha_ultima_visita',
        'created_at',
        'updated_at'
    ];
    
   
    // Relaciones
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'paciente_id');
    }
    
    public function professionals()
    {
        return $this->belongsToMany(Professional::class, 'paciente_profesional', 'paciente_id', 'profesional_id')
                    ->withPivot('fecha_asignacion', 'notas')
                    ->withTimestamps();
    }
    
    public function treatments()
    {
        return $this->hasMany(TreatmentPerformed::class, 'paciente_id');
    }
    
    // Accessors
    public function getFullNameAttribute()
    {
        return $this->nombres . ' ' . $this->apellidos;
    }
    
    // Mutators
    public function setFechaNacimientoAttribute($value)
    {
        $this->attributes['fecha_nacimiento'] = $value;
        // La edad se actualizará automáticamente por el trigger de la base de datos
    }
}