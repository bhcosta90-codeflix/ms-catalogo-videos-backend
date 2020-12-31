<?php

namespace Tests\Unit\Models;

use App\Models\Genre as Model;
use PHPUnit\Framework\TestCase;

class GenreUnitTest extends TestCase
{
    private $model;

    protected function setUp(): void
    {
        $this->model = new Model();
    }

    public function testFillable()
    {
        $fillModel = $this->model->getFillable();
        $fillCompare = ['name', 'is_active'];

        $this->assertEqualsCanonicalizing($fillCompare, $fillModel);
    }

    public function testIfUseTraits()
    {
        $traitClass = array_keys(class_uses($this->model));
        $traitCompare = [
            \Illuminate\Database\Eloquent\Factories\HasFactory::class,
            \Illuminate\Database\Eloquent\SoftDeletes::class,
            \App\Traits\Models\UuidGenerate::class,
            \EloquentFilter\Filterable::class,
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
            "is_active" => "boolean",
            "deleted_at" => "datetime"
        ];
        $this->assertEqualsCanonicalizing($castModel, $castCompare);
    }
}
