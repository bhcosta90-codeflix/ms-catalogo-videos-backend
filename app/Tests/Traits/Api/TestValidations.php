<?php

declare(strict_types=1);

namespace App\Tests\Traits\Api;

use Illuminate\Support\Facades\Lang;
use Illuminate\Testing\TestResponse;

/**
 * Test for validate at api
 */
trait TestValidations
{
    protected abstract function routeStore();

    protected abstract function routeUpdate();

    /**
     * @param array $data
     * @param string $rule
     * @param array $ruleParams
     *
     * @return void
     */
    protected function assertInvalidationInStoreAction(
        array $data,
        string $rule,
        array $ruleParams = []
    ): void
    {
        $response = $this->json('POST', $this->routeStore(), $data);
        $fields = array_keys($data);
        $this->assertInvalidationFields($response, $fields, $rule, $ruleParams);
    }

    /**
     * @param array $data
     * @param string $rule
     * @param array $ruleParams
     *
     * @return void
     */
    protected function assertInvalidationInUpdateAction(
        array $data,
        string $rule,
        array $ruleParams = []
    ): void
    {
        $response = $this->json('PUT', $this->routeUpdate(), $data);
        $fields = array_keys($data);
        $this->assertInvalidationFields($response, $fields, $rule, $ruleParams);
    }

    /**
     * @param TestResponse $response
     * @param array $fields
     * @param string $rule
     * @param array $ruleParams
     *
     * @return void
     */
    protected function assertInvalidationFields(
        TestResponse $response,
        array $fields,
        string $rule,
        array $ruleParams = []
    ): void
    {
        $response->assertStatus(422)
            ->assertJsonValidationErrors($fields);

        foreach ($fields as $field) {
            $fieldName = str_replace('_', ' ', $field);
            $response->assertJsonFragment([Lang::get("validation.{$rule}", ['attribute' => $fieldName] + $ruleParams)]);
        }
    }
}
