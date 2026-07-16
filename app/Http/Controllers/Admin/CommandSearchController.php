<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\CommandSearch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommandSearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $query = (string) $request->query('q', '');

        return response()->json([
            'groups' => CommandSearch::search($query),
        ]);
    }
}
