<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *     title="BoardCollection",
 *     description="BoardCollection model",
 *     @OA\Xml(
 *         name="BoardCollection"
 *     ),
 *     @OA\Property(
 *         property="data",
 *         title="data",
 *         description="Response data",
 *         type="array",
 *         @OA\Items(
 *             ref="#/components/schemas/BoardResource"
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

class BoardCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
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
