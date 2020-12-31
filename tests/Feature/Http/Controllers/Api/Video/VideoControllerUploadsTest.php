<?php


namespace Tests\Feature\Http\Controllers\Api\Video;


use App\Models\Video;
use Arr;
use App\Tests\Traits\Api\TestValidations;
use App\Tests\Traits\TestUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\TestResponse;
use Storage;

class VideoControllerUploadsTest extends BaseVideoControllerTestCase
{
    use TestValidations, TestUploads;

    public function testInvalidationThumbField()
    {
        $this->assertInvalidationFile(
            'file_banner',
            'jpg',
            Video::BANNER_FILE_MAX_SIZE,
            'image'
        );
    }

    public function testInvalidationBannerField()
    {
        $this->assertInvalidationFile(
            'file_banner',
            'jpg',
            Video::BANNER_FILE_MAX_SIZE,
            'image'
        );
    }

    public function testInvalidationTrailerField()
    {
        $this->assertInvalidationFile(
            'file_trailer',
            'mp4',
            Video::TRAILLER_FILE_MAX_SIZE,
            'mimetypes', ['values' => 'video/mp4']
        );
    }

    public function testInvalidationVideoField()
    {
        $this->assertInvalidationFile(
            'file_video',
            'mp4',
            Video::VIDEO_FILE_MAX_SIZE,
            'mimetypes', ['values' => 'video/mp4']
        );
    }

    public function testStoreWithFiles()
    {
        Storage::fake();
        $files = $this->getFiles();

        $response = $this->json(
            'POST', $this->routeStore(), $this->sendData + $files
        );
        $response->assertStatus(201);
        $this->assertFilesOnPersist($response, $files);
        $video = Video::find($response->json('data.id'));
        $this->assertIfFilesUrlExists($video, $response);
    }

    public function testUpdateWithFiles()
    {
        \Storage::fake();
        $files = $this->getFiles();

        $response = $this->json(
            'PUT', $this->routeUpdate(), $this->sendData + $files
        );
        $response->assertStatus(200);
        $this->assertFilesOnPersist($response, $files);
        $video = Video::find($response->json('data.id'));
        $this->assertIfFilesUrlExists($video, $response);

        $newFiles = [
            'file_thumb' => UploadedFile::fake()->create("file_thumb.jpg"),
            'file_video' => UploadedFile::fake()->create("file_video.mp4")
        ];

        $response = $this->json(
            'PUT', $this->routeUpdate(), $this->sendData + $newFiles
        );
        $response->assertStatus(200);
        $this->assertFilesOnPersist(
            $response,
            Arr::except($files, ['file_thumb', 'file_video']) + $newFiles
        );

        $id = $response->json('data.id');
        $video = Video::find($id);
        \Storage::assertMissing($video->relativeFilePath($files['file_thumb']->hashName()));
        \Storage::assertMissing($video->relativeFilePath($files['file_video']->hashName()));
    }


    protected function assertFilesOnPersist(TestResponse $response, $files)
    {
        $id = $response->json('id') ?? $response->json('data.id');
        $video = Video::find($id);
        $this->assertFilesExistsInStorage($video, $files);
    }

    protected function getFiles()
    {
        return [
            'file_thumb' => UploadedFile::fake()->create("file_thumb.jpg"),
            'file_banner' => UploadedFile::fake()->create("file_banner.jpg"),
            'file_trailer' => UploadedFile::fake()->create("file_trailer.mp4"),
            'file_video' => UploadedFile::fake()->create("file_video.mp4")
        ];
    }

    protected function model()
    {
        return Video::class;
    }

    protected function routeStore()
    {
        return route('api.videos.store');
    }

    protected function routeUpdate()
    {
        return route('api.videos.update', ['video' => $this->video->id]);
    }

}
