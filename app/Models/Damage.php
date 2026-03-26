<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Damage extends Model
{
    protected $table = 'damages';

    protected $fillable = [
        'code',
        'name',
        'solution',
    ];

    public function cases()
    {
        return $this->hasMany(CaseBase::class, 'damage_id');
    }
}