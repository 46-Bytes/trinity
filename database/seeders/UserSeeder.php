<?php

namespace Database\Seeders;

use App\Models\Org;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder {

    public function run(): void {
        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'advisor']);
        Role::create(['name' => 'client']);

        // Define users with multiple roles in an array
        $users = [
            [
                'first_name' => 'James',
                'last_name' => 'Ussery',
                'email' => 'james@mach.us',
                'password' => 'gskWi47oFfi6Y826meHVG!',
                'roles' => ['admin', 'client'],
                'org' => [
                    'name' => 'Machus',
                    'slug' => 'machus',
                    'status' => 'active',
                    'description' => 'Remote Project Management and Web Application Development Agency',
                    'website' => 'https://mach.us',
                    'address_line1' => '106 N. Denton Tap Rd',
                    'address_line2' => 'Ste 210-114',
                    'city' => 'Coppell',
                    'state' => 'TX',
                    'postal_code' => '75019',
                    'country' => 'US',
                    'date_joined' => now()
                ]
            ],
            [
                'first_name' => 'Hari',
                'last_name' => 'Vijayan',
                'email' => 'phpsmashcode@gmail.com',
                'password' => 'gskWi47oFfi6Y826meHVG!',
                'roles' => ['admin', 'client'],
                'org' => [
                    'name' => 'PHP Smash Code',
                    'slug' => 'php-smash-code',
                    'status' => 'active',
                    'description' => '',
                    'website' => '',
                    'address_line1' => '',
                    'address_line2' => '',
                    'city' => '',
                    'state' => '',
                    'postal_code' => '',
                    'country' => '',
                    'date_joined' => now()
                ]
            ],
            [
                'first_name' => 'Peter',
                'last_name' => 'Spinda',
                'email' => 'peter@benchmarkbusinessadvisory.com.au',
                'password' => 'gskWi47oFfi6Y826meHVG!',
                'roles' => ['admin', 'client'],
                'org' => [
                    'name' => 'Benchmark Business Advisory',
                    'slug' => 'benchmark-business-advisory',
                    'status' => 'active',
                    'description' => '',
                    'website' => 'benchmarkbusinessadvisory.com.au',
                    'address_line1' => '',
                    'address_line2' => '',
                    'city' => 'Brisbane',
                    'state' => 'Queensland',
                    'postal_code' => '',
                    'country' => 'AU',
                    'date_joined' => now()
                ]
            ]
//            [
//                'first_name' => 'Chris',
//                'last_name' => 'Smith',
//                'email' => 'Chris@MyMalekso.com.au',
//                'password' => 'gskWi47oFfi6Y826meHVG!',
//                'roles' => ['admin', 'client'],
//                'org' => [
//                    'name' => '',
//                    'slug' => '',
//                    'status' => '',
//                    'description' => '',
//                    'website' => '',
//                    'address_line1' => '',
//                    'address_line2' => '',
//                    'city' => '',
//                    'state' => '',
//                    'postal_code' => '',
//                    'country' => '',
//                    'date_joined' => null
//                ]
//            ],
//            [
//                'first_name' => 'Farrah',
//                'last_name' => 'Macchiwalla',
//                'email' => 'farrah@mach.us',
//                'password' => 'gskWi47oFfi6Y826meHVG!',
//                'roles' => ['admin', 'client'],
//                'org' => [
//                    'name' => '',
//                    'slug' => '',
//                    'status' => '',
//                    'description' => '',
//                    'website' => '',
//                    'address_line1' => '',
//                    'address_line2' => '',
//                    'city' => '',
//                    'state' => '',
//                    'postal_code' => '',
//                    'country' => '',
//                    'date_joined' => null
//                ]
//            ],
//            [
//                'first_name' => 'Scott',
//                'last_name' => 'Bodley',
//                'email' => 'scott@dpiconsulting.llc',
//                'password' => 'gskWi47oFfi6Y826meHVG!',
//                'roles' => ['client'],
//                'org' => [
//                    'name' => '',
//                    'slug' => '',
//                    'status' => '',
//                    'description' => '',
//                    'website' => '',
//                    'address_line1' => '',
//                    'address_line2' => '',
//                    'city' => '',
//                    'state' => '',
//                    'postal_code' => '',
//                    'country' => '',
//                    'date_joined' => null
//                ]
//            ],
//            [
//                'first_name' => 'Aidan',
//                'last_name' => 'Ussery',
//                'email' => 'aidan@mach.us',
//                'password' => 'DragonsSpitFire323.',
//                'roles' => ['admin', 'client'],
//                'org' => [
//                    'name' => '',
//                    'slug' => '',
//                    'status' => '',
//                    'description' => '',
//                    'website' => '',
//                    'address_line1' => '',
//                    'address_line2' => '',
//                    'city' => '',
//                    'state' => '',
//                    'postal_code' => '',
//                    'country' => '',
//                    'date_joined' => null
//                ]
//            ],
        ];

        // Loop through each user and create them
        foreach ($users as $userData) {
            $user = User::create([
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'email_verified_at' => now(),
            ]);

            // Assign multiple roles to the user
            foreach ($userData['roles'] as $role) {
                $user->assignRole($role);
            }

            if (isset($userData['org'])) {
                $user->org()->create($userData['org']);
                $user->save();
            }
        }
    }

}
