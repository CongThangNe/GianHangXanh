<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;

class CartController extends Controller
{
    protected $cartService;
    public function __construct(CartService $cartService){
        $this->cartService=$cartService;
    }

    public function index(){
        $cart=$this->cartService->getCart();
        return view('cart.index', compact('cart'));
    }

    public function add(Request $request){
        $data=$request->validate([
            'variant_id'=>'required|exists:product_variants,id',
            'quantity'=>'required|integer|min:1'
        ]);
        try{
            $cart=$this->cartService->addToCart($data['variant_id'],$data['quantity']);
        }catch(\Exception $e){
            return $this->errorResponse($request,$e->getMessage());
        }
        if($request->wantsJson()){
            return response()->json(['status'=>true,'cart'=>$cart]);
        }
        return redirect()->route('cart.index')->with('success','Đã thêm sản phẩm vào giỏ hàng.');
    }

    public function update(Request $request){
        $data=$request->validate([
            'item_id'=>'required|exists:cart_items,id',
            'quantity'=>'required|integer|min:1'
        ]);
        try{
            $cart=$this->cartService->updateItem($data['item_id'],$data['quantity']);
        }catch(\Exception $e){
            return $this->errorResponse($request,$e->getMessage());
        }
        if($request->wantsJson()){
            return response()->json(['status'=>true,'cart'=>$cart]);
        }
        return redirect()->route('cart.index')->with('success','Đã cập nhật giỏ hàng.');
    }

    public function remove(Request $request){
        $data=$request->validate([
            'item_id'=>'required|exists:cart_items,id'
        ]);
        try{
            $cart=$this->cartService->removeItem($data['item_id']);
        }catch(\Exception $e){
            return $this->errorResponse($request,$e->getMessage());
        }
        if($request->wantsJson()){
            return response()->json(['status'=>true,'cart'=>$cart]);
        }
        return redirect()->route('cart.index')->with('success','Đã xoá sản phẩm khỏi giỏ hàng.');
    }

    protected function errorResponse(Request $request, string $message){
        if($request->wantsJson()){
            return response()->json(['status'=>false,'message'=>$message],400);
        }
        return redirect()->back()->withErrors(['cart'=>$message]);
    }
}
