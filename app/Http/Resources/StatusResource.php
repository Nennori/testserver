<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="StatusResource",
 *     description="StatusResource model",
 *     @OA\Xml(
 *         name="StatusResource"
 *     ),
 *     @OA\Property(
 *         property="id",
 *         type="string",
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *     ),
 * )
 *
 */
class StatusResource extends JsonResource
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
        ];
    }
}
