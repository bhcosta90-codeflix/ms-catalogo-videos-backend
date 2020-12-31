<?php

namespace Tests\Feature\Models;

use App\Models\Category as Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * Class CategoryTest
 * @package Tests\Feature\Models
 */
class CategoryTest extends TestCase
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
            'description',
            'created_at',
            'updated_at',
            'deleted_at',
            'is_active',
        ], array_keys($list->first()->getAttributes()));
    }

    public function testCreate()
    {
        /** @var Model $category */
        $category = Model::create([
            'name' => 'teste1'
        ]);
        $category->refresh();

        $this->assertEquals('teste1', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $category = Model::create([
            'name' => 'teste1',
            'description' => null,
        ]);
        $category->refresh();
        $this->assertNull($category->description);

        $category = Model::create([
            'name' => 'teste1',
            'description' => 'teste description',
        ]);
        $category->refresh();
        $this->assertEquals('teste description', $category->description);

        $category = Model::create([
            'name' => 'teste1',
            'is_active' => false,
        ]);
        $category->refresh();
        $this->assertFalse($category->is_active);

        $category = Model::create([
            'name' => 'teste1',
            'is_active' => true,
        ]);
        $category->refresh();
        $this->assertTrue($category->is_active);
    }

    public function testEdit()
    {
        /** @var Model $category */
        $category = Model::factory()->create([
            'name' => 'category create',
            'description' => 'description create',
            'is_active' => false
        ]);

        $data = [
            'name' => 'category edit',
            'description' => 'description edit',
            'is_active' => true
        ];

        $category->update($data);
        $this->assertEquals('category edit', $category->name);
        $this->assertEquals('description edit', $category->description);
        $this->assertTrue($category->is_active);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
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
