<?php

namespace App\Http\Controllers;

use App\Abstracts\Controllers\Api\BasicCrudController;
use App\Http\Resources\CastMemberResource;
use App\Models\CastMember;
use App\Repositories\CastMemberRepository;

class CastMemberController extends BasicCrudController
{
    private $rules = [];

    public function __construct()
    {
        $this->rules = [
            'name' => ['required', 'min:2', 'max:191'],
            'type' => ['required', 'in:' . implode(',', array_keys(CastMember::TYPES_OPTIONS))],
        ];
    }

    protected function repository()
    {
        return CastMemberRepository::class;
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
        return CastMemberResource::class;
    }

}
