<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseBase extends Model
{
    protected $table = 'case_bases';

    protected $fillable = [
        'case_code',
        'damage_id',
        'note',
    ];

    public function damage()
    {
        return $this->belongsTo(Damage::class, 'damage_id');
    }

    public function symptoms()
    {
        return $this->hasMany(CaseSymptom::class, 'case_base_id');
    }
}