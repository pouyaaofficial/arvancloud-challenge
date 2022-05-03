<?php

namespace App\Http\Controllers\Api;

use App\Events\UserCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\GetUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserThumbnailResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * User Profile.
     *
     * Gets User full Profile.
     *
     * @authenticated
     * @group User
     *
     * @apiResource App\Http\Resources\UserResource
     * @apiResourceModel App\Models\User
     */
    public function show(GetUserRequest $request, User $user): JsonResponse
    {
        return $this->responseApi(new UserResource($user));
    }

    /**
     * Signup User.
     *
     * Creates user or return existing user.
     *
     * @group User
     *
     * @apiResource App\Http\Resources\UserThumbnailResource
     * @apiResourceModel App\Models\User
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        $user = User::firstWhere('phone_number', $request->phone_number);

        if (is_null($user)) {
            $user = User::create(['phone_number' => $request->phone_number]);
            UserCreated::dispatch($user);
        }

        return $this->created(new UserThumbnailResource($user));
    }
}
