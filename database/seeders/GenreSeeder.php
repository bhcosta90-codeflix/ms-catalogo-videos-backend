<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $total = 100;
        
        $categories = Category::all();

        if (Genre::count() == 0) {
            $genre = Genre::factory()->create(["id" => "01628454-5941-4961-ab1e-c86cdaf688a6"]);
            $genre->categories()->attach(["07355ec1-d989-464f-a6a8-170e2f7db5e6", "04f19435-0c88-4db8-b589-248657704fde"]);
            
            $genre = Genre::factory()->create(["id" => "03563339-f0c7-43a2-9e3b-fef8d67a5807"]);
            $genre->categories()->attach(["05a2fb8d-bb39-4ed5-a289-8bc498d8f2ad", "0a75762b-99ba-407e-9ba8-1f1b4024b2bd"]);
            
            $genre = Genre::factory()->create(["id" => "10ab7444-2123-4a5b-a23c-2bf642349b7f"]);
            $genre->categories()->attach(["15ddbabc-3b90-46a1-91ac-76630fcd5911", "04f19435-0c88-4db8-b589-248657704fde"]);
            
            $genre = Genre::factory()->create(["id" => "15608e28-e21b-4f0c-ae80-60042008ca59"]);
            $genre->categories()->attach(["0a75762b-99ba-407e-9ba8-1f1b4024b2bd", "07355ec1-d989-464f-a6a8-170e2f7db5e6"]);
            
            $genre = Genre::factory()->create(["id" => "26908d2d-0113-4b37-b2ea-2e79b244195f"]);
            $genre->categories()->attach(["05a2fb8d-bb39-4ed5-a289-8bc498d8f2ad", "15ddbabc-3b90-46a1-91ac-76630fcd5911"]);
            
            $total = $total - 5;
        }

        Genre::factory($total)->create()->each(function($obj) use($categories){
            $categoriesRandom = $categories->random(rand(2, 4));
            $obj->categories()->attach($categoriesRandom);
        });
    }
}
