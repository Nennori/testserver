<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *     title="TaskCollection",
 *     description="TaskCollection model",
 *     @OA\Xml(
 *         name="TaskCollection"
 *     ),
 *     @OA\Property(
 *         property="data",
 *         title="data",
 *         description="Response data",
 *         type="array",
 *         @OA\Items(
 *             ref="#/components/schemas/TaskResource"
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

class TaskCollection extends ResourceCollection
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
