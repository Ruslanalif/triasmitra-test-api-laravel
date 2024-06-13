<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsResident extends Model
{
    use HasFactory;

    protected $table = 'msresident';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'IDCardNumber', 'Name', 'BirthPlace', 'BirthDay', 'Gender',
        'Province', 'Regency', 'District', 'Village', 'Address',
        'Religion', 'MaritalStatus', 'Employment', 'Citizenship',
        'FileURL', 'FgActive'
    ];
}