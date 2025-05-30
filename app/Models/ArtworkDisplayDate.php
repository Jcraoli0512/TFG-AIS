<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtworkDisplayDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'artwork_id',
        'user_id',
        'display_date',
        'is_approved'
    ];

    protected $casts = [
        'display_date' => 'date',
        'is_approved' => 'boolean'
    ];

    public function artwork()
    {
        return $this->belongsTo(Artwork::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 