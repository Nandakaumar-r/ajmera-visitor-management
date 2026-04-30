<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ManagersSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $managers = [
            ['manager_id' => 98, 'manager_name' => 'Subrahmanya B A', 'manager_email' => 'subrahmanya@fidelisgroup.in'],
            ['manager_id' => 21268, 'manager_name' => 'Chandrasekar G', 'manager_email' => 'chandrasekar.g@fidelisgroup.in'],
            ['manager_id' => 5582, 'manager_name' => 'Roshan Alva', 'manager_email' => 'roshan.alva@fidelisgroup.in'],
            ['manager_id' => 16722, 'manager_name' => 'Chetan Vinobha Shetty', 'manager_email' => 'chetan.s@fidelisgroup.in'],
            ['manager_id' => 18199, 'manager_name' => 'Kalpitha Y', 'manager_email' => 'kalpitha.y@fidelisgroup.in'],
            ['manager_id' => 5798, 'manager_name' => 'Nagashree K S', 'manager_email' => 'nagashree@fidelisgroup.in'],
            ['manager_id' => 1065, 'manager_name' => 'Prashanth B', 'manager_email' => 'prashanth.b@fidelisgroup.in'],
            ['manager_id' => 8769, 'manager_name' => 'Bindu Sunil', 'manager_email' => 'bindu.s@fidelisgroup.in'],
            ['manager_id' => 19442, 'manager_name' => 'Uday Kumar Reddy', 'manager_email' => 'udaykumar.reddy@fidelisgroup.in'],
            ['manager_id' => 708, 'manager_name' => 'Sunil Kumar S', 'manager_email' => 'sunil.g@fidelisgroup.in'],
            ['manager_id' => 12919, 'manager_name' => 'Suneel Kumar K C', 'manager_email' => 'suneel.kc@fidelisgroup.in'],
            ['manager_id' => 19444, 'manager_name' => 'Veena Saseendran G', 'manager_email' => 'veena.saseendran@fidelisgroup.in'],
            ['manager_id' => 5380, 'manager_name' => 'Rajesh Franklin', 'manager_email' => 'rajesh.franklin@fidelisgroup.in'],
            ['manager_id' => 15237, 'manager_name' => 'Rashmi K Kulkarni', 'manager_email' => 'rashmi.k@fidelisgroup.in'],
            ['manager_id' => 19388, 'manager_name' => 'Bharath K V', 'manager_email' => 'bkv@fidelisgroup.in'],
            ['manager_id' => 17063, 'manager_name' => 'Shridhara Sundararaj', 'manager_email' => 'sridhara.s@fidelisgroup.in'],
            ['manager_id' => 20515, 'manager_name' => 'Samrat D N', 'manager_email' => 'samrat.d@fidelisgroup.in'],
            ['manager_id' => 18698, 'manager_name' => 'Naik Chetan Laxman', 'manager_email' => 'chetan.n@fidelisgroup.in'],
            ['manager_id' => 19610, 'manager_name' => 'Sridhar R', 'manager_email' => 'sridhar.r@fidelisgroup.in'],
            ['manager_id' => 7730, 'manager_name' => 'Prashant V Kokane', 'manager_email' => 'prashant.kokane@fidelisgroup.in'],
            ['manager_id' => 12287, 'manager_name' => 'Kavitha C', 'manager_email' => 'kavitha.c@fidelisgroup.in'],
            ['manager_id' => 16309, 'manager_name' => 'Pradeep S R', 'manager_email' => 'pradeep.sr@fidelisgroup.in'],
            ['manager_id' => 456, 'manager_name' => 'Senthil J', 'manager_email' => 'senthil.j@fidelisgroup.in'],
            ['manager_id' => 7624, 'manager_name' => 'Malupula Ramanjaneyulu', 'manager_email' => 'ramanjaneyulu.m@fidelisgroup.in'],
        ];

        foreach ($managers as &$manager) {
            $manager['created_at'] = $now;
            $manager['updated_at'] = $now;
        }

        DB::table('managers')->insert($managers);
    }
}
