<?php

namespace Tests\Unit\Models;

use App\Models\Video as Model;
use PHPUnit\Framework\TestCase;

class VideoUnitTest extends TestCase
{
    private $model;

    protected function setUp(): void
    {
        $this->model = new Model();
    }

    public function testFillable()
    {
        $fillModel = $this->model->getFillable();
        $fillCompare = [
            'title',
            'description',
            'year_launched',
            'opened',
            'rating',
            'duration',
            'file_thumb',
            'file_banner',
            'file_trailer',
            'file_video',
        ];

        $this->assertEqualsCanonicalizing($fillCompare, $fillModel);
    }

    public function testIfUseTraits()
    {
        $traitClass = array_keys(class_uses($this->model));
        $traitCompare = [
            \EloquentFilter\Filterable::class,
            \Illuminate\Database\Eloquent\Factories\HasFactory::class,
            \Illuminate\Database\Eloquent\SoftDeletes::class,
            \App\Traits\Models\UuidGenerate::class,
            \App\Traits\Models\UploadFile::class
        ];

        $this->assertEqualsCanonicalizing($traitCompare, $traitClass);

    }

    public function testIncrement()
    {
        $this->assertFalse($this->model->getIncrementing());
    }

    public function testKeyType()
    {
        $this->assertEquals('string', $this->model->getKeyType());
    }

    public function testClassIsModel()
    {
        $this->assertEquals(\Illuminate\Database\Eloquent\Model::class, get_parent_class($this->model));
    }

    public function testCasts()
    {
        $castModel = $this->model->getCasts();
        $castCompare = [
            "deleted_at" => "datetime",
            "year_launched" => "integer",
            "opened" => "boolean",
            "duration" => "integer",
        ];
        $this->assertEqualsCanonicalizing($castModel, $castCompare);
    }
}
