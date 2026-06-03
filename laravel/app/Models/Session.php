<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Session extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sessions';

    protected $fillable = [
        'title', 'code', 'class_id', 'created_by',
        'date', 'start_time', 'end_time', 'status',
        'notes', 'late_threshold_minutes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function presentCount(): int
    {
        return $this->attendances()->whereIn('status', ['present', 'late'])->count();
    }

    public function attendanceRate(): float
    {
        $total = $this->classRoom ? $this->classRoom->activeStudentsCount() : 0;
        if ($total === 0) return 0.0;
        return round(($this->presentCount() / $total) * 100, 1);
    }

    public function getFormattedStartTimeAttribute(): string
    {
        return Carbon::parse($this->start_time)->format('H:i');
    }

    public function getFormattedEndTimeAttribute(): string
    {
        return Carbon::parse($this->end_time)->format('H:i');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeForClass($query, int $classId)
    {
        return $query->where('class_id', $classId);
    }
}
