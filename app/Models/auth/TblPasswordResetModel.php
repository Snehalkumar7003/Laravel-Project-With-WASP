<?php

namespace App\Models\auth;

use Illuminate\Database\Eloquent\Model;

class TblPasswordResetModel extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */
    protected $table = 'tbl_password_resets';

    /*
    |--------------------------------------------------------------------------
    | Primary Key
    |--------------------------------------------------------------------------
    */
    protected $primaryKey = 'tbl_password_resets_id';

    /*
    |--------------------------------------------------------------------------
    | Disable Laravel Timestamps
    |--------------------------------------------------------------------------
    */
    public $timestamps = false;
    
    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Fields
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'mst_users_id',
        'email',
        'token',
        'expires_at',
        'used_at',
        'ip_address',
        'user_agent',
        'create_date'

    ];

    /*
    |--------------------------------------------------------------------------
    | Date Casting
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'create_date' => 'datetime'
    ];

}