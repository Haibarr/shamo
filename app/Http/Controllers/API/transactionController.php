<?php

namespace App\Http\Controllers\API;

use App\Models\transaction;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\transactionItem;
use Illuminate\Support\Facades\Auth;

class transactionController extends Controller
{
    public function all(Request $request){
        $id = $request->input('id');
        $limit = $request->input('limit',6);
        $status = $request->input('status');

        if ($id) {
            $transaction = transaction::with(['items.product'])->find($id);
            if ($transaction) {
                return ResponseFormatter::success(
                    $transaction,'data transaksi berhasil'
                );
            }else{
                return ResponseFormatter::error(
                    null,'data transaksi tidak ada',404
                );
            }
        }
        $transaction = transaction::with(['items.product'])->where('users_id',Auth::user()->id);

        if ($status) {
            $transaction->where('status',$status);
        }
        return ResponseFormatter::success(
           $transaction->paginate($limit),'data list transaksi berhasil diambil'
        );
    }

    public function checkout(Request $request){
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'exists:products,id',   
            'total_price' => 'required',
            'shipping_price' => 'required',
            'status' => 'required|in:PENDING,SUCCESS,CANCELED,SHIPPING,SHIPED',
            ]
        );
        $transaction = transaction::create(
            [
                'users_id' => Auth::user()->id,
                'address' => $request->address,
                'total_price' => $request->total_price,
                'shipping_price' => $request->shipping_price,
                'status' => $request->status,
                
            ]
        );

        foreach ($request->items as $product) {
            transactionItem::create([
                'users_id' => Auth::user()->id,
                'products_id' => $product['id'],
                'transactions_id' => $transaction->id,
                'quantity' => $product['quantity'],
            ]);
        }
        return ResponseFormatter::success(
            $transaction->load('items.product'),'transaksi berhasil'
        ); 
    }
}
