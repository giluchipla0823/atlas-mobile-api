<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface BaseRepositoryInterface
{
    public function all(Request $request);

    public function find(int $id): Model;

    public function create(array $params): Model;

    public function update(array $params, int $id): Model;

    public function delete(int $id): void;
}
