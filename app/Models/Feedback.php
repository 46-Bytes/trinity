<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model {
    use HasFactory;

    // Relationships
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function formEntry() {
        return $this->belongsTo(FormEntry::class);
    }
}
