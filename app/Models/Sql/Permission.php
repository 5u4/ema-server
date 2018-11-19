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
 */
class Permission extends Model
{
    public const PERMISSIONS = [
        'read-users',
        'update-users',
        'remove-users',
    ];

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
