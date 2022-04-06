<?php

namespace App\Services\Application;

use App\Repositories\Rule\RuleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class RuleService
{
    /**
     * @var RuleRepositoryInterface
     */
    private $repository;

    public function __construct(RuleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @return Collection|LengthAwarePaginator
     */
    public function findAll(Request $request)
    {
        return $this->repository->all($request);
    }
}
