<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model {
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'description', 'setting_name', 'setting_value'];

    public static function getPrompt(string $setting_name): string|null {
        return Setting::where('setting_name', $setting_name)->first()->setting_value;
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
