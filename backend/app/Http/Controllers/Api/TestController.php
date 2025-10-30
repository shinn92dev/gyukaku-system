<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Test if API is working
     *
     * @return array{status: string}
     */
    public function __invoke(Request $request): array
    {
        return ['status' => 'working'];
    }
}
