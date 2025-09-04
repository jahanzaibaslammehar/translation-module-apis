<?php

namespace App\Core\Services;

use App\Core\Contracts\Services\AbstractServiceInterface;
use App\Services\FileUpload;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\ResponseCode;
use Illuminate\Http\Request;

class AbstractService implements AbstractServiceInterface
{
    protected $repository;

    protected $response;

    public $request;

    protected $responseMessage;


    public function __construct(Request $request)
    {
        $this->request  = $request;
    }

    public function create(array $request): Model
    {
        return  $this->repository->create($request);
    }

    public function update(array $data,  array $with = []): Model
    {
        $this->repository->update($data['id'], $data);

        return  $this->repository->getById($data['id'], $with);
    }

    public function getById(int | string $id, array $with = []): Model
    {
        return $this->repository->getById($id, $with);
    }

    public function destroy(Model $model)
    {
        $this->repository->destroy($model);

        $this->response->setResponse(ResponseCode::SUCCESS, ResponseCode::REGULAR, $this->response->getDeleteResponseMessage());

        return $this->response;

    }

    public function list(array $with = [], $limit = null)
    {
        if ($limit) {
            $this->repository->setLimit($limit);
        }

        return  $this->repository->getList($with);

    }

    public function findMany(array $conditions, $with = [], $limit = null)
    {
        if ($limit) {
            $this->repository->setLimit($limit);
        }
        return $this->repository->findMany($conditions, $with , $limit);

    }

    public function findOne(array $conditions, $with = [])
    {
        return $this->repository->findOne($conditions, $with);
    }


    public function setPagination(bool $paginate): void
    {
        $this->repository->setPagination($paginate);
    }

    public function setOrder(string $order): void
    {
        $this->repository->setOrder($order);
    }

    public function setLimit(int $limit): void
    {
        $this->repository->setLimit($limit);
    }
}
