<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="UserDetailResource",
 *     description="UserDetailResource model",
 *     @OA\Xml(
 *         name="UserDetailResource"
 *     ),
 *     @OA\Property(
 *         property="data",
 *         title="data",
 *         description="Response data",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(
 *                 property="id",
 *                 type="string",
 *             ),
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *             ),
 *             @OA\Property(
 *                 property="email",
 *                 type="string",
 *             ),
 *             @OA\Property(
 *                 property="about",
 *                 type="string",
 *             ),
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
 *
 * )
 *
 */
class UserDetailResource extends JsonResource
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
            'data'=>[
                'id'=>$this->id,
                'name'=>$this->name,
                'email'=>$this->email,
                'about'=>$this->about,
            ],
            'message'=>'',
            'status'=>'ok',
        ];
    }
}
