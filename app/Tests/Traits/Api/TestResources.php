<?php

declare(strict_types=1);

namespace App\Tests\Traits\Api;

use Exception;
use Illuminate\Testing\TestResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait TestResources
{
    /**
     * @param TestResponse $response
     * @param JsonResource $resource
     *
     * @return void
     */
    protected function assertResource(TestResponse $response, JsonResource $resource)
    {
        $response->assertJson($resource->response()->getData(true));
    }
}
