<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *     title="MarkCollection",
 *     description="MarkCollection model",
 *     @OA\Xml(
 *         name="MarkCollection"
 *     ),
 *     @OA\Property(
 *         property="data",
 *         title="data",
 *         description="Response data",
 *         type="array",
 *         @OA\Items(
 *             ref="#/components/schemas/MarkResource"
 *         )
 *     ),
 *     @OA\Property(
 *         property="message",
 *         title="message",
 *         description="Response message",
 *         type="string",
 *     ),
 *     @OA\Property(
 *         property="status",
 *         title="status",
 *         description="Response status",
 *         type="string",
 *     ),
 * )
 *
 */

class MarkCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data'=>$this->collection,
            'message'=>'',
            'status'=>'ok',
        ];
    }
}
