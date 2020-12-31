<?php

namespace App\Http\Controllers;

use App\Abstracts\Controllers\Api\BasicCrudController;
use App\Http\Resources\GenreResource;
use App\Repositories\GenreRepository;
use Illuminate\Database\Eloquent\Builder;

class GenreController extends BasicCrudController
{
    private $rules = [
        'name' => ['required', 'max:191', 'min:2'],
        'is_active' => ['boolean'],
        'categories' => ['array', 'required', 'exists:categories,id,deleted_at,NULL'],
    ];

    protected function repository()
    {
        return GenreRepository::class;
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
        return GenreResource::class;
    }

    protected function queryBuilder(): Builder
    {
        return parent::queryBuilder()->with('categories');
    }


}
