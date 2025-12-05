<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TreatmentImage extends Model
{
    use HasFactory;
    
    protected $table = 'tratamiento_imagenes';
    
    protected $fillable = [
        'tratamiento_id',
        'ruta_archivo',
        'nombre_archivo',
        'tipo_archivo',
        'descripcion',
        'tamano',
        'orden'
    ];
    
    protected $dates = [
        'created_at'
    ];
    
    // Relaciones
    public function treatmentPerformed()
    {
        return $this->belongsTo(TreatmentPerformed::class, 'tratamiento_id');
    }
}