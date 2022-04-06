<?php

namespace App\Traits;

use Exception;
use App\Helpers\DatatablesHelper;
use App\Helpers\QueryParamsHelper;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

trait TransformResourceResponse
{
    /**
     * @param Collection $data
     * @return array
     * @throws Exception
     */
    protected function transformCollection(Collection $data): array
    {
        $instance = $data->first();

        $transformer = $instance->transformer;

        return $this->getTransformDataFromResource($data, $transformer);
    }


    /**
     * @param Model|null $instance
     * @return array|object
     */
    protected function transformInstance(?Model $instance)
    {
        if (!$instance) {
            return $instance;
        }

        $transformer = $instance->transformer;

        $instance->load(QueryParamsHelper::getIncludesParamFromRequest());

        return $this->getTransformDataFromResource($instance, $transformer);
    }

    /**
     * @param Collection|Model $data
     * @param string|null $transformer
     * @return array
     */
    private function getTransformDataFromResource($data, ?string $transformer): array
    {
        if(!class_exists($transformer)){
            return $data->toArray();
        }

        $resource = $this->getResourceInstance($data, $transformer);

        if(!$resource instanceof JsonResource){
            return $data->toArray();
        }

        return $resource->toArray(request());
    }

    /**
     * @param Collection|Model $data
     * @param string $transformer
     * @return JsonResource|null
     */
    private function getResourceInstance($data, string $transformer): ?JsonResource
    {
        $resource = null;

        if ($data instanceof Model) {
            $resource = new $transformer($data);
        } else if ($data instanceof Collection) {
            $resource = $transformer::collection($data);
        }

        return $resource;
    }

}
