<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id', 'student_id', 'checked_in_at',
        'status', 'similarity_score', 'captured_photo', 'ip_address',
    ];

    protected $casts = [
        'checked_in_at'   => 'datetime',
        'similarity_score' => 'float',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getSimilarityPercentAttribute(): string
    {
        return number_format($this->similarity_score * 100, 1) . '%';
    }

    public function getCapturedPhotoUrlAttribute(): string
    {
        if ($this->captured_photo && \Storage::disk('public')->exists($this->captured_photo)) {
            return asset('storage/' . $this->captured_photo);
        }
        return '';
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('checked_in_at', today());
    }
}
