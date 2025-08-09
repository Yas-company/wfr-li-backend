<?php

namespace App\Http\Controllers\api\v1;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="WFR LI Backend API",
 *     description="API documentation for WFR LI Backend",
 *
 *     @OA\Contact(
 *         email="admin@example.com",
 *         name="API Support"
 *     ),
 *
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Local Server1"
 * )
 * @OA\Server(
 *     url="http://wfr-li-backend.test/api/v1/",
 *     description="Local Server2"
 *)
 * @OA\Server(
 *     url="https://wfr.li/api/v1/",
 *     description="Live Server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class SwaggerController
{
    // This class is only used for Swagger documentation
    // No actual methods needed
}
