<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exhibition extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'curatorial_note',
        'start_date',
        'end_date',
        'status',
        'is_public'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_public' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function artworks()
    {
        return $this->belongsToMany(Artwork::class);
    }
} 