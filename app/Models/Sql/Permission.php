<?php

namespace App\Models\Sql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Sql\Permission
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Sql\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\Permission whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\Permission query()
 * @property string $description
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sql\Permission whereDescription($value)
 */
class Permission extends Model
{
    public const PERMISSIONS = [
        self::READ_USER_PERMISSION,
        self::DISABLE_USER_PERMISSION,
        self::UPDATE_PERMISSION_PERMISSION,
        self::DELETE_COMMENT_PERMISSION,
    ];

    public const READ_USER_PERMISSION = 'read-user';
    public const DISABLE_USER_PERMISSION = 'block-user';
    public const UPDATE_PERMISSION_PERMISSION = 'mod-user';
    public const DELETE_COMMENT_PERMISSION = 'delete-comment';

    protected $fillable = [
        'name',
    ];

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_permissions', 'permission_id', 'user_id');
    }
}
