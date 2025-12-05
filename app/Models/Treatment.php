<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Treatment extends Model
{
    use HasFactory;
    
    protected $table = 'tratamientos';
    protected $casts = [
        'fecha_hora' => 'datetime',
    ];
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'costo_sugerido'
    ];
    
    // Relaciones
    public function performedTreatments()
    {
        return $this->hasMany(TreatmentPerformed::class, 'tratamiento_id');
    }
}
