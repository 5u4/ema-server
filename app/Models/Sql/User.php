<?php

namespace App\Models\Sql;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\Models\Sql\User
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $last_login
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Sql\Permission[] $permissions
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Sql\User onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\User whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\User whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Sql\User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Sql\User withoutTrashed()
 * @mixin \Eloquent
 * @property string|null $avatar_path
 * @property string|null $avatar_name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\User whereAvatarName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\User whereAvatarPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\User query()
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'last_login', 'avatar_name', 'avatar_path', 'deleted_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions', 'user_id', 'permission_id');
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return !$this->deleted_at;
    }

    /**
     *
     */
    public function disable()
    {
        $this->update([
            'deleted_at' => time(),
        ]);
    }

    /**
     *
     */
    public function enable()
    {
        $this->update([
            'deleted_at' => null,
        ]);
    }

    /**
     * @return bool
     */
    public function canViewUsers(): bool
    {
        return $this->permissions->contains('name', Permission::READ_USER_PERMISSION);
    }

    /**
     * @return bool
     */
    public function canDisableUsers(): bool
    {
        return $this->permissions->contains('name', Permission::DISABLE_USER_PERMISSION);
    }

    /**
     * @return bool
     */
    public function canUpdateUserPermissions(): bool
    {
        return $this->permissions->contains('name', Permission::UPDATE_PERMISSION_PERMISSION);
    }

    /**
     * @return bool
     */
    public function canDeleteComments(): bool
    {
        return $this->permissions->contains('name', Permission::DELETE_COMMENT_PERMISSION);
    }
}
