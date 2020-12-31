<?php

namespace App\Repositories;

use App\Models\Genre;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;

/**
 * Class GenreRepository.
 */
class GenreRepository extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return Genre::class;
    }

    public function create(array $data)
    {
        $obj = parent::create($data);
        $this->syncCategories($obj, $data['categories']);
        return $obj;
    }

    public function updateById($id, array $data, array $options = [])
    {
        /**
         * @var $obj Genre
         */
        $obj = parent::updateById($id, $data, $options);
        $this->syncCategories($obj, $data['categories']);
        return $obj;
    }

    private function syncCategories(Genre $obj, array $categories)
    {
        $obj->categories()->sync($categories);
    }
}
