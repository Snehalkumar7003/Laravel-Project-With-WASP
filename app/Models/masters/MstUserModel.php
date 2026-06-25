<?php

namespace App\Models\masters;

use Illuminate\Database\Eloquent\Model;

/**
 * |--------------------------------------------------------------------------
 * | User Model
 * |--------------------------------------------------------------------------
 * |
 * | Table      : mst_users
 * | Primary Key: mst_users_id
 * |
 * |--------------------------------------------------------------------------
 */
class MstUserModel extends Model
{
    /**
     * |--------------------------------------------------------------------------
     * | Table Name
     * |--------------------------------------------------------------------------
     */
    protected $table = 'mst_users';

    /**
     * |--------------------------------------------------------------------------
     * | Primary Key
     * |--------------------------------------------------------------------------
     */
    protected $primaryKey = 'mst_users_id';

    /**
     * |--------------------------------------------------------------------------
     * | Timestamp Handling
     * |--------------------------------------------------------------------------
     */
    public $timestamps = false;

    /**
     * |--------------------------------------------------------------------------
     * | Mass Assignable Fields
     * |--------------------------------------------------------------------------
     */
    protected $fillable = [
        'username',
        'profile_photo',
        'mobile',
        'email',
        'password',
        'mst_roles_id',
        'refresh_token',
        'auth_token_expirty',
        'is_first_login',
        'is_active',
        'is_delete',
        'last_update',
        'last_login',
        'session_id',
        'device_fingerprint',
        'last_ip_address',
        'last_user_agent'
    ];

    /**
     * |--------------------------------------------------------------------------
     * | Type Casting
     * |--------------------------------------------------------------------------
     */
    protected $casts = [
        'is_first_login' => 'boolean',
        'is_active'      => 'boolean',
        'is_delete'      => 'boolean',
        'create_date'    => 'datetime',
        'last_update'    => 'datetime',
        'last_login'     => 'datetime'
    ];

    /**
     * |--------------------------------------------------------------------------
     * | Role Relationship
     * |--------------------------------------------------------------------------
     */
    public function role()
    {
        return $this->belongsTo(
            MstRoleModel::class,
            'mst_roles_id',
            'mst_roles_id'
        );
    }
}
