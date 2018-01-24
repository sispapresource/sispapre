<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            'name' => 'Ver Link Log Usuarios',
            'slug' => 'ver.log_usuarios',
        ]);
        DB::table('permissions')->insert([
            'name' => 'Ver Información de Facturación y Cobro',
            'slug' => 'ver.facturacion_cobro',
        ]);
    }
}
