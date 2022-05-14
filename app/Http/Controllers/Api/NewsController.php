<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(){
        $data = News::latest()->get();

        return response()->json([
            'data' => $data,
        ]);
    }
}
