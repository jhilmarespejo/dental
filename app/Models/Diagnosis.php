<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Diagnosis extends Model
{
    use HasFactory;
    
    protected $table = 'diagnosticos';
    
    protected $fillable = [
        'nombre',
        'descripcion'
    ];
    
    // Relaciones
    public function treatments()
    {
        return $this->hasMany(TreatmentPerformed::class, 'diagnostico_id');
    }
}
