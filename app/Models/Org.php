<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Org extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',            // Add user_id here
        'name',
        'slug',
        'status',
        'description',
        'website',
        'date_joined',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country'
    ];
public function getLocationAttribute() {
    return $this->state . ', '. $this->country;
}
    // Define the relationship with the User model
    public function user() {
        return $this->belongsTo(User::class);
    }
}
