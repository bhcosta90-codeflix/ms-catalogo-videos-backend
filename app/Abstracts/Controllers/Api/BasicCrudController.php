<?php

namespace App\Abstracts\Controllers\Api;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller as BaseController;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
use JasonGuru\LaravelMakeRepository\Repository\RepositoryContract;
use ReflectionClass;

abstract class BasicCrudController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $defaultPerPage = 15;

    protected abstract function repository();

    protected abstract function rulesStore();

    protected abstract function rulesUpdate();

    protected abstract function resource();

    public function index(Request $request)
    {
        /**
         * @var $repository BaseRepository
         */
        $repository = app($this->repository());
        $resource = $this->resource();
        $resourceCollection = $this->resource();
        if(method_exists($this, 'resourceCollection')) {
            $resourceCollection = $this->resourceCollection();
        }
        $perPage = (int) $request->get('per_page', $this->defaultPerPage);
        $hasFilter = method_exists($repository, 'model')
            && in_array(Filterable::class, class_uses($repository->model()));

        $query = $this->queryBuilder();

        if($hasFilter){
            $query = $query->filter($request->all());
        }

        $data = $request->has('all') || !$this->defaultPerPage
            ? $query->get()
            : $query->paginate($perPage);

        $refClass = new ReflectionClass($resourceCollection);

        return $refClass->isSubclassOf(ResourceCollection::class)
            ? new $resource($data)
            : $resourceCollection::collection($data);
    }

    public function store(Request $request)
    {
        $validatedData = $this->validate($request, $this->rulesStore());
        /**
         * @var $repository BaseRepository
         */
        $repository = app($this->repository());
        $resource = $this->resource();

        $keyName = $repository->makeModel()->getRouteKeyName();
        $obj = $repository->create($validatedData);
        $obj->refresh();

        return $this->show($obj->$keyName)
            ->response()
            ->setStatusCode(201);
    }

    public function show($id)
    {
        /**
         * @var $repository BaseRepository
         */
        $repository = app($this->repository());
        $keyName = $repository->makeModel()->getRouteKeyName();
        $obj = $this->queryBuilder()->where($keyName, $id)->firstOrFail();;

        $resource = $this->resource();

        return new $resource($obj);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $this->validate($request, $this->rulesUpdate());
        /**
         * @var $repository BaseRepository
         */
        $repository = app($this->repository());
        $repository->updateById($id, $validatedData);

        return $this->show($id);
    }

    public function destroy($id)
    {
        /**
         * @var $repository BaseRepository
         */
        $repository = app($this->repository());
        $repository->deleteById($id);

        return response()->noContent();
    }

    protected function queryBuilder(): Builder {
        /**
         * @var $repository BaseRepository
         */
        $repository = app($this->repository());
        return method_exists($repository, 'model') ?
            $repository->model()::query() :
            new Builder;
    }

}
