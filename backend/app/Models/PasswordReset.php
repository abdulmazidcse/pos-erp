<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    // use HasFactory; 

    protected $primaryKey = 'email';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'password_resets';

    protected $fillable = ['email', 'otp', 'token', 'created_at'];

    public $timestamps = false; // If 'created_at' and 'updated_at' fields don't exist in your table
}