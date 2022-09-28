<?php

use Illuminate\Database\Seeder;

class OptionValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('option_values')->insert([
            [
            'position'       => '1',
            'name'           => 'A',
            'presentation'   => 'A',
            'option_type_id' => '1',
            ],
            [
            'position'       => '2',
            'name'           => 'AA',
            'presentation'   => 'AA',
            'option_type_id' => '1',
            ],
            [
            'position'       => '3',
            'name'           => 'AAA',
            'presentation'   => 'AAA',
            'option_type_id' => '1',
            ],
            [
            'position'       => '1',
            'name'           => 'S',
            'presentation'   => 'S',
            'option_type_id' => '2',
            ],
            [
            'position'       => '2',
            'name'           => 'M',
            'presentation'   => 'M',
            'option_type_id' => '2',
            ],
            [
            'position'       => '3',
            'name'           => 'G',
            'presentation'   => 'G',
            'option_type_id' => '2',

            ],
            [
            'position'       => '4',
            'name'           => 'XG',
            'presentation'   => 'XG',
            'option_type_id' => '2',
            ]
        ]);
    }
}
