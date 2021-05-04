<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="BoardResource",
 *     description="BoardResource model",
 *     @OA\Xml(
 *         name="BoardResource"
 *     ),
 *     @OA\Property(
 *         property="id",
 *         type="string",
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *     ),
 *     @OA\Property(
 *         property="users",
 *         type="array",
 *         description="Users that in the board",
 *         @OA\Items(
 *             ref="#/components/schemas/BoardResource"
 *         )
 *     ),
 * )
 *
 */
class BoardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'users'=>UserResource::collection($this->users),
            'marks'=>MarkResource::collection($this->marks)
        ];
    }
}
