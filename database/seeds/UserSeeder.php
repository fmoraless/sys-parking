<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'nombre' => 'Admin',
            'telefono' => '+56 9-82166481',
            'movil' => '+56 2-22166481',
            'tipo' => 'admin',
            'direccion' => 'Avda. el alba 5007 - Santiago',
            'email' => 'admin@demo.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        factory(App\User::class, 50)->create();
    }
}
