<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Symptom extends Model
{
    protected $table = 'symptoms';

    protected $fillable = [
        'code',
        'name',
        'category',
        'description',
    ];

    public $timestamps = false;

    public function caseSymptoms()
    {
        return $this->hasMany(CaseSymptom::class, 'symptom_id');
    }
}