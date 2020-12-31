<?php

namespace App\Repositories;

use App\Models\Video;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;

/**
 * Class VideoRepository.
 */
class VideoRepository extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return Video::class;
    }
}
