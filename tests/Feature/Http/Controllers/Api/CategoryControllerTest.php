<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Resources\CategoryResource;
use App\Models\Category as Model;
use App\Tests\Traits\Api\{TestResources, TestValidations, TestSaves};
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves, TestResources;

    private $category;
    private $serializedFields = [
        'id',
        'name',
        'description',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = Model::factory()->create();
    }


    public function testIndex()
    {
        $response = $this->get(route('api.categories.index'));

        $response
            ->assertStatus(200)
            ->assertJson([
                'meta' => ['per_page' => 15]
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->serializedFields
                ],
                'links' => [],
                'meta' => [],
            ]);

        $resource = CategoryResource::collection(collect([$this->category]));
        $this->assertResource($response, $resource);
    }

    public function testShow()
    {
        $response = $this->get(route('api.categories.show', ['category' => $this->category->id]));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->serializedFields
            ]);

        $id = $response->json('data.id');
        $resource = new CategoryResource(Model::find($id));
        $this->assertResource($response, $resource);
    }

    public function testInvalidationData()
    {
        $data = [
            'name' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'name' => str_repeat('a', 256),
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 191]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 191]);

        $data = [
            'is_active' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function testStore()
    {
        $data = [
            'name' => 'test'
        ];
        $response = $this->assertStore($data,
            $data + ['description' => null, 'is_active' => true, 'deleted_at' => null]);
        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);

        $data = [
            'name' => 'test',
            'description' => 'description',
            'is_active' => false
        ];
        $this->assertStore($data, $data + ['description' => 'description', 'is_active' => false]);

        $id = $response->json('data.id');
        $resource = new CategoryResource(Model::find($id));
        $this->assertResource($response, $resource);
    }

    public function testUpdate()
    {
        $data = [
            'name' => 'test',
            'description' => 'test test',
            'is_active' => true
        ];
        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);

        $id = $response->json('data.id');
        $resource = new CategoryResource(Model::find($id));
        $this->assertResource($response, $resource);

        $data = [
            'name' => 'test',
            'description' => '',
        ];
        $this->assertUpdate($data, array_merge($data, ['description' => null]));

        $data['description'] = 'test test';
        $this->assertUpdate($data, array_merge($data, ['description' => 'test test']));

        $data['description'] = null;
        $this->assertUpdate($data, array_merge($data, ['description' => null]));
    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('api.categories.destroy', ['category' => $this->category->id]));
        $response->assertStatus(204);
        $this->assertNull(Model::find($this->category->id));
        $this->assertNotNull(Model::withTrashed()->find($this->category->id));
    }

    protected function routeStore()
    {
        return route('api.categories.store');
    }

    protected function routeUpdate()
    {
        return route('api.categories.update', ['category' => $this->category->id]);
    }

    protected function model()
    {
        return Model::class;
    }

}
