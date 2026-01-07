<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Evaluation Model
 *
 * Represents an evaluation entity (not scores).
 */
class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'user_id',
        'name',
        'logo',
        'description',
        'year',
    ];

    protected $casts = [
        'year' => 'string',
    ];

    /**
     * Get the user who created this evaluation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the organization that owns this evaluation.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the students that belong to this evaluation.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'evaluation_student')
            ->withPivot('position')
            ->withTimestamps();
    }

    /**
     * Get the ranks for this evaluation.
     */
    public function ranks(): HasMany
    {
        return $this->hasMany(Rank::class);
    }

    /**
     * Get the peer evaluator assignments for this evaluation.
     */
    public function peerEvaluators(): HasMany
    {
        return $this->hasMany(EvaluationPeerEvaluator::class);
    }

    /**
     * Scope a query to filter by organization.
     */
    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope a query to filter by year.
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }
}
