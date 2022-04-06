<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\JsonResponse;

class CountryController extends Controller
{
    public function index(): JsonResponse {
        try {
            $countries = Country::all();

            return response()->json(['error' => false, 'data' => $countries]);
        } catch (\Throwable $exc) {
            return response()->json(['error' => true, 'message' => $exc]);
        }
    }
}
