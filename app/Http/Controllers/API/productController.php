<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\product;
use Illuminate\Http\Request;

class productController extends Controller
{
    public function all(Request $request){
        // ambil data dari API
        $id = $request->input('id');
        $limit = $request->input('limit');
        $name = $request->input('name');
        $description = $request->input('description');
        $tags = $request->input('tags');
        $categories = $request->input('categories');

        $price_from = $request->input('price_from');
        $price_to = $request->input('price_to');

        //jika mengisi id maka ambil data dari model product berdasarkan id 
        if ($id) {
            $product = product::with(['category','galleries'])->find($id);
            if ($product) {
                return ResponseFormatter::success($product,'data produk berhasil diambil');
            }else{
                return ResponseFormatter::error(null,'data produk tidak berhasil diambil',404);
            }
        }
        $product = product::with(['category','galleries']);
        if ($name) {
            //melakukan pencarian berdasarakan name
            $product->where($name , 'like', '%' .$name. '%');
        }
        if ($description) {
            //melakukan pencarian berdasarakan des$description
            $product->where($description , 'like', '%' .$description. '%');
        }
        if ($tags) {
            //melakukan pencarian berdasarakan des$tags
            $product->where($tags , 'like', '%' .$tags. '%');
        }

        if ($price_from) {
            $product->where('price','>=',$price_from);
        }
        if ($price_to) {
            $product->where('price','<=',$price_to);
        }
        if ($categories) {
            $product->where('categories',$categories);
        }
        return ResponseFormatter::success(
            $product->paginate($limit),
            'data produk berhasil diambil',
        );
    }
}
