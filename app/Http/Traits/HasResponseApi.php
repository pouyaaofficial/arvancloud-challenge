<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait HasResponseApi
{
    protected function ok(JsonResource $resource = null): JsonResponse
    {
        return $this->responseApi($resource, 200);
    }

    protected function created(JsonResource $resource = null): JsonResponse
    {
        return $this->responseApi($resource, 201);
    }

    protected function conflict(JsonResource $resource = null): JsonResponse
    {
        return $this->responseApi($resource, 409);
    }

    protected function responseApi(JsonResource $resource = null, int $statusCode = 200): JsonResponse
    {
        return ($resource ?? (new JsonResource([])))->response()->setStatusCode($statusCode);
    }
}
