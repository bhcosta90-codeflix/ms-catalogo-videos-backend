<?php

namespace App\Http\Controllers;

use App\Abstracts\Controllers\Api\BasicCrudController;
use App\Http\Resources\CategoryResource;
use App\Repositories\CategoryRepository;

class CategoryController extends BasicCrudController
{
    private $rules = [
        'name' => ['required', 'min:2', 'max:191'],
        'description' => ['nullable', 'min:5'],
        'is_active' => ['nullable', 'boolean']
    ];

    protected function repository()
    {
        return CategoryRepository::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }

    protected function resource()
    {
        return CategoryResource::class;
    }

}
