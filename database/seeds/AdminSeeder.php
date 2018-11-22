<?php

use App\Models\Sql\Permission;
use App\Models\Sql\User;
use App\Services\UserService;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    private const ADMIN_USERNAME = 'Admin';
    private const ADMIN_EMAIL    = 'admin@sfu.ca';
    private const ADMIN_PASSWORD = 'adminpassword';

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        /** @var User $admin */
        $admin = factory(User::class)->create([
            'username' => self::ADMIN_USERNAME,
            'email'    => self::ADMIN_EMAIL,
            'password' => bcrypt(self::ADMIN_PASSWORD),
        ]);

        foreach (Permission::all() as $permission) {
            $admin->permissions()->attach($permission->id);
        }

        $userService = app()->make(UserService::class);

        $userService->createUserInNeo($admin->id);
    }
}
