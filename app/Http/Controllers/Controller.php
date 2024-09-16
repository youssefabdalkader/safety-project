<?php


namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="My API Documentation",
 *         version="1.0.0",
 *         description="API documentation for my Laravel project"
 *     )
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
