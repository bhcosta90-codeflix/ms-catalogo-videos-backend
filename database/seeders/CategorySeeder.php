<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $total = 100;
        
        if (Category::count() == 0) {
            Category::factory()->create(["id" => "07355ec1-d989-464f-a6a8-170e2f7db5e6"]);
            Category::factory()->create(["id" => "04f19435-0c88-4db8-b589-248657704fde"]);
            Category::factory()->create(["id" => "05a2fb8d-bb39-4ed5-a289-8bc498d8f2ad"]);
            Category::factory()->create(["id" => "0a75762b-99ba-407e-9ba8-1f1b4024b2bd"]);
            Category::factory()->create(["id" => "15ddbabc-3b90-46a1-91ac-76630fcd5911"]);
            $total = $total - 5;
        }

        Category::factory($total)->create();
    }
}
