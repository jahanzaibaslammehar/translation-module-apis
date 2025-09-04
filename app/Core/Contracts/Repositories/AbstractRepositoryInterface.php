<?php

namespace App\Core\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;


interface AbstractRepositoryInterface
{

    public function create(array $request): Model;
    public function update(int $id, array $request): bool;
    public function destroy(Model $model): bool;

    public function findOne(array $conditions, array $with = []): ?Model;
    public function findMany(array $conditions, array $with = []): LengthAwarePaginator;

    public function getById(int $id, array $with = []): Model;

}