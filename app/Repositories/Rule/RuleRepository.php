<?php

namespace App\Repositories\Rule;

use App\Models\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RuleRepository implements RuleRepositoryInterface
{

    /**
     * @var Rule
     */
    private $rule;

    public function __construct(Rule $rule)
    {
        $this->rule = $rule;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function all(Request $request)
    {
        $query = $this->rule->where([
            'active' => 1,
            'parent_compound' => $request->compoundId
        ]);

        if ($request->has('name')) {
            $query = $query
                        ->where('name', 'LIKE', "%". $request->name ."%")
                        ->orderBy('name');
        }

        return $query->paginate(20);
    }

    public function find(int $id): Model
    {
        // TODO: Implement find() method.
    }

    public function create(array $params): Model
    {
        // TODO: Implement create() method.
    }

    public function update(array $params, int $id): Model
    {
        // TODO: Implement update() method.
    }

    public function delete(int $id): void
    {
        // TODO: Implement delete() method.
    }
}
