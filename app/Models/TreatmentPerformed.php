<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TreatmentPerformed extends Model
{
    use HasFactory;
    
    protected $table = 'tratamientos_realizados';
    
    protected $fillable = [
        'paciente_id',
        'profesional_id',
        'cita_id',
        'fecha',
        'diagnostico_id',
        'diagnostico_otro',
        'tratamiento_id',
        'tratamiento_otro',
        'pieza_dental',
        'costo',
        'observaciones'
    ];
    
    protected $dates = [
        'fecha',
        'created_at',
        'updated_at'
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
    
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'cita_id');
    }
    
    public function diagnosis()
    {
        return $this->belongsTo(Diagnosis::class, 'diagnostico_id');
    }
    
    public function treatment()
    {
        return $this->belongsTo(Treatment::class, 'tratamiento_id');
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class, 'tratamiento_id');
    }
    
    public function images()
    {
        return $this->hasMany(TreatmentImage::class, 'tratamiento_id');
    }
    
    // Accesor para obtener el saldo pendiente
    public function getPendingBalanceAttribute()
    {
        return $this->costo - $this->payments->sum('monto');
    }
}