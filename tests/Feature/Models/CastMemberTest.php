<?php

namespace Tests\Feature\Models;

use App\Models\CastMember as Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Ramsey\Uuid\Uuid;

class CastMemberTest extends TestCase
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
            'type',
        ], array_keys($list->first()->getAttributes()));
    }

    public function testCreate()
    {
        $castMember = Model::create([
            'name' => 'teste1',
            'type' => Model::TYPE_ACTOR
        ]);
        $castMember->refresh();

        $this->assertEquals('teste1', $castMember->name);
        $this->assertEquals(Model::TYPE_ACTOR, $castMember->type);
    }

    public function testEdit()
    {
        /** @var Model $castMember */
        $castMember = Model::factory()->create([
            'name' => 'cast member create',
            'type' => Model::TYPE_DIRECTOR
        ]);

        $data = [
            'name' => 'cast member edit',
            'type' => Model::TYPE_DIRECTOR
        ];

        $castMember->update($data);
        $this->assertEquals('cast member edit', $castMember->name);
        $this->assertEquals(Model::TYPE_DIRECTOR, $castMember->type);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $castMember->{$key});
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
