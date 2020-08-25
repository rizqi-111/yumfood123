<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Order;
use App\Detail;
use App\Dish;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index() //Time : 10 Menit
    {
        // 
        return OrderResource::collection(Order::paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) //Time : 10 Minutes
    {
        //
        $request->validate([
            'dish_id' => 'required',
            'quantity' => 'required|numeric|min:1',
        ]);

        $order = new Order;
        $order->status = 1;
        $order->request = $request['request'];
        $order->save();

        $dish = Dish::find($request['dish_id']);

        $detail_order = $dish->detail()->create([
            'dish_id' => $request['dish_id'],
            'quantity' => $request['quantity'],
        ]);

        $success = $order->details()->sync($detail_order);
    
        if(!$success)
        {
            return "gagal masuk";
        }
    
        return "berhasil masuk";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) //Time : 5 Minutes
    {
        //
        return new VendorResource(Vendor::findOrFail($id)); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) //Time : 20 Menit
    {
        //
        $request->validate([
            'name' => 'required|max:128',
            'logo' => 'required'
        ]);

        $tags = explode(',',$request['tag']);
        
        $tag_ids = [];
        foreach($tags as $t_name){
            $tag = Tag::firstOrCreate(['name' => $t_name]);
            $tag_ids[] = $tag->id;
        }

        $vendor=Vendor::find($id);
        $vendor->name=$request['name'];
        $vendor->logo=$request['logo'];
        $vendor->save();
        
        $success=$vendor->tags()->sync($tag_ids);
    
        if(!$success)
        {
            return "gagal ubah";
        }
    
        return "berhasil ubah";

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)//Time : 10 menit
    {
        //
        $vendor=Vendor::find($id);
        if(is_null($vendor))
        {
            return "Tidak Ditemukan";
        }
    
        $success=$vendor->delete();

        $success = $vendor->tags()->detach();

        if(!$success)
        {
            return "Berhasil Hapus Vendor, Tidak Ditemukan Taggable";
        }
    
        return "Berhasil Hapus";
    }
}
