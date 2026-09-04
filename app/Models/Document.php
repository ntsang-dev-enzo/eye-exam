<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';

    protected $fillable = [
        'category_id',
        'class_id',
        'subject_id',
        'teacher_id',
        'title',
        'description',
        'original_filename',
        'file_path',
        'file_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get human readable formatted file size (e.g. 2.4 MB, 850 KB).
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 0) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Get uppercase extension (e.g. PDF, DOCX, ZIP).
     */
    public function getFileExtensionUpperAttribute(): string
    {
        return strtoupper($this->file_type ?? pathinfo($this->original_filename, PATHINFO_EXTENSION));
    }
}
