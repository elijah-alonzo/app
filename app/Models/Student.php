<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_number',
        'name',
        'email',
        'password',
        'image',
        'description',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the evaluations this student participates in.
     */
    public function evaluations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Evaluation::class, 'evaluation_student')
            ->withPivot('position')
            ->withTimestamps();
    }

    /**
     * Get peer evaluator assignments where this student is the evaluatee (being evaluated).
     */
    public function peerEvaluationAssignments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EvaluationPeerEvaluator::class, 'evaluatee_student_id');
    }

    /**
     * Get peer evaluator assignments where this student is the evaluator (doing the evaluating).
     */
    public function peerEvaluatorAssignments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EvaluationPeerEvaluator::class, 'evaluator_student_id');
    }

}
