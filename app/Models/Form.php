<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model {
    use HasFactory;


    // Fields that are mass assignable
    protected $fillable = ['title', 'slug', 'ai_prompt', 'description', 'scripts', 'form_json', 'theme_json', 'status'];


    public static function getBySlug(string $slug): Form {
        return self::where('slug', $slug)->first();
    }

    public static function Diagnostic(): Form {
        return self::where('slug', 'diagnostic')->first();
    }

    // Relationships

    public function formEntries() {
        return $this->hasMany(FormEntry::class);
    }
}
