<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="TaskResource",
 *     description="TaskResource model",
 *     @OA\Xml(
 *         name="TaskResource"
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
 *         property="description",
 *         type="string",
 *     ),
 *     @OA\Property(
 *         property="status",
 *         ref="#/components/schemas/StatusResource"
 *     ),
 *     @OA\Property(
 *         property="expired_at",
 *         type="string",
 *     ),
 *     @OA\Property(
 *         property="author",
 *         ref="#/components/schemas/UserResource"
 *     ),
 *     @OA\Property(
 *         property="executor",
 *         ref="#/components/schemas/UserResource"
 *     ),
 *     @OA\Property(
 *         property="marks",
 *         type="array",
 *         @OA\Items(
 *             ref="#/components/schemas/MarkResource"
 *         )
 *     ),
 * )
 *
 */
class TaskResource extends JsonResource
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
            'description'=>$this->description,
            'status'=>StatusResource::collection($this->status),
            'expired_at'=>$this->expired_at,
            'author'=>UserResource::collection($this->users->withPivot('is_author', true)->first()),
            'executor'=>UserResource::collection($this->users->wihPivot('is_author', false)->first()),
            'marks'=>MarkResource::collection($this->marks),
        ];
    }
}
