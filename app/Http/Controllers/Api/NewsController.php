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

    public function detail($id){
        $data = News::find($id);

        return response()->json([
            'data' => $data,
        ]);
    }

    public function create(Request $request){
        $data = News::updateOrCreate(['id' => $request->id],[
            'judul' => $request->judul,
            'tanggal' => $request->tanggal,
            'isi' => $request->isi,
            'penulis' => $request->penulis
        ]);

        return response()->json([
            'data' => $data,
        ]);
    }

    public function delete($id){
        $data = News::find($id);

        return response()->json([
            'data' => $data,
        ]);
    }

}
