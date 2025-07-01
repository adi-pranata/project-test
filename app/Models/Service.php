<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'description', 'required_documents', 'is_active'
    ];

    protected $casts = [
        'required_documents' => 'array',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
}
