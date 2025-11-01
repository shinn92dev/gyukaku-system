<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(title: 'Gyukaku System RESTful API', version: '1.0.0')]
#[OA\Server(url: 'http://localhost:8000/api')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
abstract class Controller
{
    //
}
