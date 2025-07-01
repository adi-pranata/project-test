<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'service_id', 'registration_number', 'status', 'notes', 
        'identity_data', 'submitted_at', 'processed_at', 'completed_at', 'processed_by'
    ];

    protected $casts = [
        'identity_data' => 'array',
        'submitted_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function documents()
    {
        return $this->hasMany(ComplaintDocument::class);
    }

    public function supportingDocuments()
    {
        return $this->hasMany(ComplaintDocument::class)->where('is_result_document', false);
    }

    public function resultDocuments()
    {
        return $this->hasMany(ComplaintDocument::class)->where('is_result_document', true);
    }

    public function statuses()
    {
        return $this->hasMany(ComplaintStatus::class)->orderBy('created_at', 'desc');
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'submitted' => 'badge-secondary',
            'pending_verification' => 'badge-warning',
            'approved' => 'badge-info',
            'needs_revision' => 'badge-danger',
            'in_progress' => 'badge-primary',
            'completed' => 'badge-success',
            'rejected' => 'badge-dark',
            default => 'badge-secondary'
        };
    }

    public function getStatusLabel()
    {
        return match($this->status) {
            'submitted' => 'Diajukan',
            'pending_verification' => 'Menunggu Verifikasi',
            'approved' => 'Disetujui',
            'needs_revision' => 'Perlu Revisi',
            'in_progress' => 'Sedang Diproses',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak',
            default => 'Tidak Diketahui'
        };
    }

    public static function generateRegistrationNumber()
    {
        $date = Carbon::now()->format('Ymd');
        $count = self::whereDate('created_at', Carbon::now())->count() + 1;
        return 'REG' . $date . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

}
