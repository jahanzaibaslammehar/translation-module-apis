<?php

namespace App\Core\Repositories;

use App\Core\Contracts\Repositories\AbstractRepositoryInterface;
use App\Traits\RepositoryTrait;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Database\QueryException;

abstract class AbstractRepository implements AbstractRepositoryInterface
{

    use RepositoryTrait;

    protected $model;

    protected $limit = 10;

    protected $order = 'DESC';

    protected $pagination = true;

    public function create(array $request): Model
    {

        try {
            return $this->model->create($request);
        } catch (QueryException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function update(int | string $id, array $data): bool
    {
        try {
            return $this->model->find($id)->update($data);
        } catch (QueryException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getById(int | string $id, array $with = []): Model
    {
        try {
            $data = $this->model->with($with)->find($id);
        } catch (RelationNotFoundException $e) {
            throw new RelationNotFoundException($e->getMessage());
        }

        if (!$data) {
            throw new ModelNotFoundException('Record not found');
        }

        return $data;
    }

    public function findMany(array $conditions, array $with = [], $limit = 10): LengthAwarePaginator
    {
        try {
            $query =  $this->model->where($conditions)->with($with)->limit($limit);
            if ($this->pagination) {
                $data =    $query->paginate($limit);
            } else {
                $data = $query->get();
            }
        } catch (RelationNotFoundException $e) {
            throw new RelationNotFoundException($e->getMessage());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        if (!$data) {
            throw new ModelNotFoundException('Record not found');
        }
        return $data;
    }

    public function findOne(array $conditions, array $with = []): Model
    {
        try {
            $data =  $this->model->where($conditions)->with($with)->first();
        } catch (RelationNotFoundException $e) {
            throw new RelationNotFoundException($e->getMessage());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        if (!$data) {
            throw new ModelNotFoundException('Record not found');
        }
        return $data;
    }

    public function destroy(Model $model): bool
    {
        return $model->delete();
    }
}
