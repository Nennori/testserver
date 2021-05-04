<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="MarkResource",
 *     description="MarkResource model",
 *     @OA\Xml(
 *         name="MarkResource"
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
 *         property="color",
 *         type="string",
 *     ),
 * )
 *
 */
class MarkResource extends JsonResource
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
            'color'=>$this->color,
    ];
    }
}
