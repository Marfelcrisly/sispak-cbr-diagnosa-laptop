<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseSymptom extends Model
{
    protected $table = 'case_symptoms';

    protected $fillable = [
        'case_base_id',
        'symptom_id',
        'weight',
    ];

    public function case()
    {
        return $this->belongsTo(CaseBase::class, 'case_base_id');
    }

    public function symptom()
    {
        return $this->belongsTo(Symptom::class, 'symptom_id');
    }
}