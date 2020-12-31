<?php

namespace Tests\Feature\Models;

use App\Models\Genre as Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Ramsey\Uuid\Uuid;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        Model::factory()->create();
        $list = Model::all();
        $this->assertCount(1, $list);
        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'created_at',
            'updated_at',
            'deleted_at',
            'is_active',
        ], array_keys($list->first()->getAttributes()));
    }

    public function testCreate()
    {
        $genre = Model::create([
            'name' => 'teste1'
        ]);
        $genre->refresh();

        $this->assertEquals('teste1', $genre->name);
        $this->assertTrue($genre->is_active);

        $genre = Model::create([
            'name' => 'teste1',
            'is_active' => false,
        ]);
        $genre->refresh();
        $this->assertFalse($genre->is_active);

        $genre = Model::create([
            'name' => 'teste1',
            'is_active' => true,
        ]);
        $genre->refresh();
        $this->assertTrue($genre->is_active);
    }

    public function testEdit()
    {
        /** @var Model $genre */
        $genre = Model::factory()->create([
            'name' => 'genre create',
            'is_active' => false
        ]);

        $data = [
            'name' => 'genre edit',
            'is_active' => true
        ];

        $genre->update($data);
        $this->assertEquals('genre edit', $genre->name);
        $this->assertTrue($genre->is_active);

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
}
