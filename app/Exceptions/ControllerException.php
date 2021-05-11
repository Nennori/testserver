<?php

namespace App\Exceptions;

use Exception;

/**
 * @OA\Schema(
 *     title="ControllerException",
 *     description="ControllerException model",
 *     @OA\Xml(
 *         name="ControllerException"
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
 */
class ControllerException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
            'status' => 'error',
        ], $this->getCode());
    }

}
