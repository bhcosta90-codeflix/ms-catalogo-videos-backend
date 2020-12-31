<?php

namespace Tests\Feature\Models\Video;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Video as Model;
use App\Models\Video;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\QueryException;


class VideoTest extends BaseVideoTestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        Model::factory()->create();
        $list = Model::all();
        $this->assertCount(1, $list);
        $this->assertEqualsCanonicalizing([
            'created_at',
            'deleted_at',
            'description',
            'duration',
            'id',
            'opened',
            'rating',
            'title',
            'updated_at',
            'year_launched',
            'file_thumb',
            'file_banner',
            'file_trailer',
            'file_video',
        ], array_keys($list->first()->getAttributes()));
    }

    public function testCreate()
    {
        $castMember = Model::create([
            'title' => 'teste1',
            'description' => 'teste1',
            'duration' => 100,
            'rating' => Video::RATING_LIST[0],
            'year_launched' => 2010
        ]);
        $castMember->refresh();

        $this->assertEquals('teste1', $castMember->title);
        $this->assertEquals('teste1', $castMember->description);
        $this->assertEquals(100, $castMember->duration);
        $this->assertFalse($castMember->opened);
        $this->assertEquals(Video::RATING_LIST[0], $castMember->rating);
        $this->assertEquals(2010, $castMember->year_launched);

        $castMember = Model::create([
            'title' => 'teste1',
            'description' => 'teste1',
            'duration' => 100,
            'rating' => Video::RATING_LIST[0],
            'year_launched' => 2010,
            'opened' => true
        ]);
        $this->assertTrue($castMember->opened);
    }

    public function testEdit()
    {
        /** @var Model $genre */
        $genre = Model::factory()->create();

        $data = [
            'title' => 'video edit',
            'opened' => false,
        ];

        $genre->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function testDelete()
    {
        /** @var Model $category */
        $category = Model::factory()->create();
        $category->delete();
        $this->assertNull(Model::find($category->id));

        $category->restore();
        $this->assertNotNull(Model::find($category->id));
    }

    public function testUuid()
    {
        /** @var Model $category */
        $category = Model::factory()->create();
        $this->assertTrue(Uuid::isValid($category->id));
    }

    public function testRollbackCreate()
    {
        $hasError = false;
        try {
            Video::create($this->data + [
                'categories' => [0, 1, 2]
            ]);
        } catch (QueryException $exception) {
            $this->assertEquals(0, Video::count());
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function testRollbackUpdate()
    {
        $video = Video::factory()->create();
        $oldTitle = $video->title;
        $hasError = false;

        try {
            $video->update($this->data + [
                'categories' => [0, 1, 2]
            ]);
        } catch (QueryException $exception) {
            $this->assertDatabaseHas('videos', [
                'title' => $oldTitle
            ]);
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function testCreateWithBasicFields()
    {

        $video = Video::create($this->data);
        $video->refresh();

        $this->assertEquals(36, strlen($video->id));
        $this->assertFalse($video->opened);
        $this->assertDatabaseHas(
            'videos',
            $this->data + ['opened' => false]
        );

        $video = Video::create($this->data + ['opened' => true]);
        $this->assertTrue($video->opened);
        $this->assertDatabaseHas('videos', ['opened' => true]);
    }

    public function testCreateWithRelations()
    {
        $category = Category::factory()->create();
        $genre = Genre::factory()->create();
        $video = Video::create($this->data + [
            'categories' => [$category->id],
            'genres' => [$genre->id],
        ]);

        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video->id, $genre->id);
    }

    public function testUpdateWithBasicFields()
    {
        $video = Video::factory()->create(
            ['opened' => false]
        );
        $video->update($this->data);
        $this->assertFalse($video->opened);
        $this->assertDatabaseHas(
            'videos',
            $this->data + ['opened' => false]
        );

        $video = Video::factory()->create(
            ['opened' => false]
        );
        $video->update($this->data + ['opened' => true]);
        $this->assertTrue($video->opened);
        $this->assertDatabaseHas('videos', $this->data + ['opened' => true]);
    }

    public function testUpdateWithRelations()
    {
        $category = Category::factory()->create();
        $genre = Genre::factory()->create();
        $video = Video::factory()->create();
        $video->update($this->data + [
            'categories' => [$category->id],
            'genres' => [$genre->id],
        ]);

        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video->id, $genre->id);
    }

    public function testHandleRelations()
    {
        $video = Video::factory()->create();
        $video->handleRelations([]);
        $this->assertCount(0, $video->categories);
        $this->assertCount(0, $video->genres);

        $category = Category::factory()->create();
        $video->handleRelations([
            'categories' => [$category->id]
        ]);
        $video->refresh();
        $this->assertCount(1, $video->categories);

        $genre = Genre::factory()->create();
        $video->handleRelations([
            'genres' => [$genre->id]
        ]);
        $video->refresh();
        $this->assertCount(1, $video->genres);

        $video->categories()->delete();
        $video->genres()->delete();

        $video->handleRelations([
            'categories' => [$category->id],
            'genres' => [$genre->id],
        ]);
        $video->refresh();
        $this->assertCount(1, $video->categories);
        $this->assertCount(1, $video->genres);
    }

    public function testSyncCategories()
    {
        $categoriesId = Category::factory(3)->create()->pluck('id')->toArray();
        $video = Video::factory()->create();
        $video->handleRelations([
            'categories' => [$categoriesId[0]]
        ]);
        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[0],
            'video_id' => $video->id
        ]);

        $video->handleRelations([
            'categories' => [$categoriesId[1], $categoriesId[2]]
        ]);
        $this->assertDatabaseMissing('category_video', [
            'category_id' => $categoriesId[0],
            'video_id' => $video->id
        ]);
        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[1],
            'video_id' => $video->id
        ]);
        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[2],
            'video_id' => $video->id
        ]);
    }

    public function testSyncGenres()
    {
        $genresId = Genre::factory(3)->create()->pluck('id')->toArray();
        $video = Video::factory()->create();
        $video->handleRelations([
            'genres' => [$genresId[0]]
        ]);
        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genresId[0],
            'video_id' => $video->id
        ]);

        $video->handleRelations([
            'genres' => [$genresId[1], $genresId[2]]
        ]);
        $this->assertDatabaseMissing('genre_video', [
            'genre_id' => $genresId[0],
            'video_id' => $video->id
        ]);
        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genresId[1],
            'video_id' => $video->id
        ]);
        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genresId[2],
            'video_id' => $video->id
        ]);
    }


    protected function assertHasCategory($videoId, $categoryId)
    {
        $this->assertDatabaseHas('category_video', [
            'video_id' => $videoId,
            'category_id' => $categoryId
        ]);
    }

    protected function assertHasGenre($videoId, $genreId)
    {
        $this->assertDatabaseHas('genre_video', [
            'video_id' => $videoId,
            'genre_id' => $genreId
        ]);
    }
}
