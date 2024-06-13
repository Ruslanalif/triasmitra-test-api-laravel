<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class MsLogin extends Model
{
    use HasApiTokens, HasFactory;
    // use Notifiable;
    protected $table = 'mslogin';  // Nama tabel yang sudah ada

    protected $primaryKey = 'ID';
    public $incrementing = true; // or false if the ID is not auto-increment
    protected $keyType = 'int'; // or 'string' if the ID is not an integer
    
    protected $fillable = [
        'UserName', 'Password',
    ];

    protected $hidden = [
        'Password', 'TokenKey',
    ];

    // public function getAuthIdentifierName()
    // {
    //     return 'UserName';
    // }
}
