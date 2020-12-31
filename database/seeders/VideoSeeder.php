<?php

namespace Database\Seeders;

use App\Models\{CastMember, Genre, Video};
use File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Storage;

class VideoSeeder extends Seeder
{
    private $allGenres, $allCastMembers;

    private $relations = [
        'categories' => [],
        'genres' => [],
    ];

    private function relationVideo()
    {
        $subCategories = $this->allGenres->random(rand(2, 4))->load('categories');
        $categoriesId = [];
        foreach($subCategories as $categories){
            array_push($categoriesId, ...$categories->categories->pluck('id')->toArray());
        }
        $categoriesId = array_unique($categoriesId);

        $this->relations['categories'] = $categoriesId;
        $this->relations['genres'] = $subCategories->pluck('id')->toArray();
        $this->relations['cast_members'] = $this->allCastMembers->random(rand(1, 3))->pluck('id')->toArray();
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dir = Storage::getDriver()->getAdapter()->getPathPrefix();
        File::deleteDirectory($dir, true);

        $this->allCastMembers = CastMember::all();
        $this->allGenres = Genre::all();

        Model::reguard();

        $self = $this;

        $id = [
            '03a6952d-7d32-46f7-bb37-c240b62579bg',
            'ff8492f7-d6ba-4691-8869-7e0193b4f35e',
            'febcde90-b05f-4675-8b59-238ccc0c6e28',
            'fd51efd7-71fe-497c-bdec-fb392f14f163',
            'f86c41fa-f02b-4caf-8c9a-f37ec71ebce1',
        ];

        Video::factory(count($id))->make()->each(function(Video $video, $i) use($id, $self){
            $self->relationVideo();
            Video::create($video->toArray() + [
                'file_video' => $this->getVideoFile(),
                'file_trailer' => $this->getVideoFile(),
                'file_banner' => $this->getImageFile(),
                'file_thumb' => $this->getImageFile(),
            ] + $this->relations);
        });

        Model::unguard();
    }


    public function getImageFile()
    {
        return new UploadedFile(
            storage_path('faker/640x360.png'),
            'Laravel Framework.png'
        );
    }

    public function getVideoFile()
    {
        return new UploadedFile(
            storage_path('faker/videoplayback.mp4'),
            '01-Como vai funcionar os uploads.mp4'
        );
    }

}
