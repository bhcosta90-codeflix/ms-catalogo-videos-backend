<?php


namespace Tests\Feature\Http\Controllers\Api\Video;

use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

abstract class BaseVideoControllerTestCase extends TestCase
{
    use DatabaseMigrations;


    protected $video;
    protected $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->video = Video::factory()->create([
            'opened' => false,
            'file_thumb' => 'thumb.jpg',
            'file_banner' => 'banner.jpg',
            'file_video' => 'video.mp4',
            'file_trailer' => 'trailer.mp4',
        ]);
        $castMember = CastMember::factory()->create();
        $category = Category::factory()->create();
        $genre = Genre::factory()->create();
        $genre->categories()->sync($category->id);
        $this->sendData = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90,
            'categories' => [$category->id],
            'genres' => [$genre->id],
            'cast_members' => [$castMember->id],
        ];
    }

    protected function assertIfFilesUrlExists(Video $video, TestResponse $response)
    {
        $fileFields = $video->getFieldFiles();
        $data = $response->json('data');
        $data = array_key_exists(0, $data) ? $data[0] : $data;
        foreach ($fileFields as $field) {
            $file = $video->{$field};
            $this->assertEquals(
                \Storage::url($video->relativeFilePath($file)),
                $data['url_'.$field]
            );
        }
    }
}
