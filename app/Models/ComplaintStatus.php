<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id', 'status', 'notes', 'processed_by'
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
