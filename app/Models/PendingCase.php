<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingCase extends Model
{
    protected $table = 'pending_cases';

    protected $fillable = [
        'user_id',
        'selected_symptom_ids',
        'best_case_id',
        'best_similarity',
        'top_results',
        'status',
        'review_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'best_similarity' => 'float',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bestCase()
    {
        return $this->belongsTo(CaseBase::class, 'best_case_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}