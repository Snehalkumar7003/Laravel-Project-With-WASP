<?php

namespace App\Models\auth;


use Illuminate\Database\Eloquent\Model;
class TblLoginAttemptModel extends Model{
    protected $table = 'tbl_login_attempts';
    protected $primaryKey = 'tbl_login_attempts_id';
    public $timestamps = false;
    protected $fillable = [
        'username',
        'ip_address',
        'is_success'
    ];
}
