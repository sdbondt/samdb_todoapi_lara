<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'user_id', 'completed'];

    protected $casts = [
        'completed' => 'boolean'
    ];

    public function creator () {
        return $this->belongsTo(User::class, 'user_id');
    }
}
