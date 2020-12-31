<?php

namespace App\Http\Controllers;

use App\Abstracts\Controllers\Api\BasicCrudController;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Repositories\VideoRepository;
use App\Rules\GenresHasCategoriesRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class VideoController extends BasicCrudController
{
    private $rules = [];

    public function __construct()
    {
        $this->rules = [
            'title' => ['required', 'min:2', 'max:191'],
            'description' => ['required'],
            'year_launched' => ['required', 'date_format:Y'],
            'opened' => ['boolean'],
            'rating' => ['required', 'in:' . implode(',', Video::RATING_LIST)],
            'duration' => ['required', 'integer'],
            'categories' => ['array', 'required', 'exists:categories,id,deleted_at,NULL'],
            'genres' => ['array', 'required', 'exists:genres,id,deleted_at,NULL'],
            'cast_members' => ['array', 'required', 'exists:cast_members,id,deleted_at,NULL'],
            'file_video' => ['mimetypes:video/mp4', 'max:' . Video::VIDEO_FILE_MAX_SIZE],
            'file_trailer' => ['mimetypes:video/mp4', 'max:' . Video::TRAILLER_FILE_MAX_SIZE],
            'file_thumb' => ['image', 'max:' . Video::THUMB_FILE_MAX_SIZE],
            'file_banner' => ['image', 'max:' . Video::BANNER_FILE_MAX_SIZE],
        ];
    }

    public function store(Request $request)
    {
        $this->addRuleGenreHasCategories($request);
        return parent::store($request);
    }

    public function update(Request $request, $id)
    {
        $this->addRuleGenreHasCategories($request);
        return parent::update($request, $id);
    }


    protected function repository()
    {
        return VideoRepository::class;
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
        return VideoResource::class;
    }

    protected function addRuleGenreHasCategories(Request $request)
    {
        $categories = $request->get('categories');
        if(!is_array($categories)){
            $categories = [];
        }
        $this->rules['genres_id'][] = new GenresHasCategoriesRule($categories);
    }



    protected function queryBuilder(): Builder
    {
        $action = \Route::getCurrentRoute()->getAction()['uses'];
        return parent::queryBuilder()->with([
            str_contains($action, 'show')
            || str_contains($action, 'store')
            || str_contains($action, 'update')
                ? 'genres.categories'
                : 'genres', 'categories', 'castMembers'
        ]);
    }


}
