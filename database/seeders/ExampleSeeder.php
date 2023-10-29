<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Example;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ExampleSeeder extends Seeder
{
    /**
     * データベースに対するデータ設定の実行
     *
     * @return void
     */
    public function run()
    {
        // DB::table('examples')->insert([
        //     'id' => 10
        // ]);
        Example::factory()->count(3)->create();
    }
}
