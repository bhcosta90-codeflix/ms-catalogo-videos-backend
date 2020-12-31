<?php

namespace App\Repositories;

use App\Models\CastMember;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;

/**
 * Class CastMemberRepository.
 */
class CastMemberRepository extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return CastMember::class;
    }
}
