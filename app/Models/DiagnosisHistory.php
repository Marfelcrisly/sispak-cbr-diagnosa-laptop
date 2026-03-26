<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosisHistory extends Model
{
    protected $table = 'diagnosis_histories';

    protected $fillable = [
        'user_id',
        'selected_symptom_ids',
        'best_case_id',
        'best_similarity',
        'top_results',
        'threshold_used',
        'needs_review',
        'pending_case_id',
    ];

    protected $casts = [
        'best_similarity' => 'float',
        'threshold_used' => 'float',
        'needs_review' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bestCase()
    {
        return $this->belongsTo(CaseBase::class, 'best_case_id');
    }

    public function pendingCase()
    {
        return $this->belongsTo(PendingCase::class, 'pending_case_id');
    }
}