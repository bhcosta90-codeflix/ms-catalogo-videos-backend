<?php

declare(strict_types=1);

namespace App\Tests\Traits\Api;

use App\Models\User;
use Illuminate\Testing\TestResponse;

/**
 * Test for crud in api
 */
trait TestSaves
{
    protected abstract function model();

    protected abstract function routeStore();

    protected abstract function routeUpdate();

    /**
     * @param array $sendData
     * @param array $testDatabase
     * @param array|null $testJsonData
     *
     * @return TestResponse
     */
    public function assertStore(array $sendData, array $testDatabase, array $testJsonData = null): TestResponse
    {
        $testResponse = $testJsonData ?? $testDatabase;

        /** @var TestResponse */
        $response = $this->json('POST', $this->routeStore(), $sendData);
        if ($response->status() !== 201) {
            throw new \Exception("Response status must be 201, giver {$response->status()}: {$response->content()}");
        }
        $this->assertInDatabase($response, $testDatabase);
        $this->assertJsonResponseContent($response, $testResponse);

        return $response;
    }

    /**
     * @param array $sendData
     * @param array $testDatabase
     * @param array|null $testJsonData
     *
     * @return TestResponse
     */
    public function assertUpdate(array $sendData, array $testDatabase, array $testJsonData = null): TestResponse
    {
        $testResponse = $testJsonData ?? $testDatabase;

        /** @var TestResponse */
        $response = $this->json('PUT', $this->routeUpdate(), $sendData);
        if ($response->status() !== 200) {
            throw new \Exception("Response status must be 200, giver {$response->status()}: {$response->content()}");
        }

        $this->assertInDatabase($response, $testDatabase);
        $this->assertJsonResponseContent($response, $testResponse);

        return $response;
    }

    /**
     * @param TestResponse $response
     * @param array $testDatabase
     *
     * @return void
     */
    private function assertInDatabase(TestResponse $response, array $testDatabase): void
    {
        $id = $response->json('id') ?? $response->json('data.id');
        $model = $this->model();
        $this->assertDatabaseHas((new $model)->getTable(), $testDatabase + ["id" => $id]);
    }

    /**
     * @param TestResponse $response
     * @param array $testJsonData
     *
     * @return void
     */
    private function assertJsonResponseContent(TestResponse $response, array $testJsonData): void
    {
        $id = $response->json('id') ?? $response->json('data.id');
        $response->assertJsonFragment($testJsonData + ['id' => $id]);
    }
}
