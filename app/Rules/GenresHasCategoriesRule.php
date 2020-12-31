<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Collection;

class GenresHasCategoriesRule implements Rule
{
    private $categories;

    private $genre;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $categories)
    {
        $this->categories = array_unique($categories);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if (!is_array($value)) {
            $value = [];
        }

        $this->genre = array_unique($value);
        
        if (!count($this->genre) || !count($this->categories)) {
            return false;
        }

        $categoriesFound = [];

        foreach ($this->genre as $genre) {
            $rows = $this->getRows($genre);
            if (!$rows->count()) {
                return false;
            }

            array_push($categoriesFound, ...$rows->pluck('category_id')->toArray());
        }

        $categoriesFound = array_unique($categoriesFound);

        if (count($categoriesFound) !== count($this->categories)) {
            return false;
        }

        return true;

    }

    /**
     * @param string|int $genre
     * 
     * @return Collection
     */
    protected function getRows($genre): Collection
    {
        return \DB::table('category_genre')
            ->where('genre_id', $genre)
            ->whereIn('category_id', $this->categories)
            ->get();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.genres_has_categories');

    }
}
