<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Face extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'photo_path', 'embedding',
        'model_version', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'embedding' => 'array',
    ];

    protected $hidden = ['embedding'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo_path && \Storage::disk('public')->exists($this->photo_path)) {
            return asset('storage/' . $this->photo_path);
        }
        return '';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
