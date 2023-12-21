<?php
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // check if table roles is empty
       	if(DB::table('roles')->get()->count() == 0){
            DB::table('roles')->insert([
                [
                    'name' => 'Super Admin',
                    'slug' => 'super-admin'
                ],
                [
                    'name' => 'Vendor',
                    'slug' => 'vendor'
                ],
                [
                    'name' => 'Contractor',
                    'slug' => 'contractor'
                ],
                [
                    'name' => 'Customer',
                    'slug' => 'customer'
                ]

            ]);
        } else { echo "Table is not empty, therefore NOT "; }
    }
}
