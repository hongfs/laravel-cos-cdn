<?php

use App\Models\Admin;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Admin::firstOrCreate([
            'username' => 'admin'
        ], [
            'password' => bcrypt('admin123')
        ]);
    }
}
