<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Petshop API Documentation",
 *     description="This is the API documentation for Petshop.",
 *     @OA\Contact(
 *         name="Godwin (DeGod)",
 *         email="support@petshop-api.test"
 *     )
 * )
 *
 * @OA\SecurityScheme(
 *    securityScheme="bearerAuth",
 *    in="header",
 *    name="bearerAuth",
 *    type="http",
 *    scheme="bearer",
 *    bearerFormat="JWT",
 * )
 */
abstract class Controller
{
    //
}
