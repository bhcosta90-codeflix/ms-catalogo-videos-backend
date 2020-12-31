<?php

namespace Database\Seeders;

use App\Models\CastMember;
use Illuminate\Database\Seeder;

class CastMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $total = 50;

        if (CastMember::count() == 0) {
            CastMember::factory()->create(['id' => "0247da24-a4cb-4ab8-bf5c-6e5322416f9c"]);
            CastMember::factory()->create(['id' => "07e01510-8d5a-4aaa-b09f-03e57ee96c08"]);
            CastMember::factory()->create(['id' => "09a2a2c4-b071-4af9-b678-0b60e71f90a0"]);
            CastMember::factory()->create(['id' => "09fde757-d1a0-4c64-9773-90f082195eb2"]);
            CastMember::factory()->create(['id' => "0bc0301e-0826-442d-9a63-faec38a47ec6"]);

            $total = $total - 5;
        }

        CastMember::factory($total)->create();
    }
}
