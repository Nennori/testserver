<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\EditUserRequest;
use App\Http\Resources\UserDetailResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    /**
     * @OA\Get(
     *     path="api/v1/user",
     *     operationId="getUser",
     *     summary="Get current user's personal informaiton",
     *     tags={"User"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\Response(
     *         response="200",
     *         description="User personal info",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/UserDetailResource"
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ControllerException"
     *         )
     *     ),
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function getUser(Request $request): JsonResponse
    {
        $response = new UserDetailResource(auth()->user());
        return response($response, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user",
     *     operationId="editUser",
     *     summary="Change user's personal information",
     *     tags={"User"},
     *     security={ {"bearerAuth": {} }},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *             @OA\Property(
     *                 property="name",
     *                 description="Name of the user",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="about",
     *                 description="Description of the user",
     *                 type="string"
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="User's personal info",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/UserDetailResource"
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ControllerException"
     *         )
     *     ),
     * )
     * @param EditUserRequest $request
     * @return JsonResponse
     */

    public function editUser(EditUserRequest $request): JsonResponse
    {
        $user = auth()->user();
        $user->fill($request->all());
        $response = new UserDetailResource($user);
        return response($response, 200);
    }
}
