<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Dish;
use App\Vendor;
use App\Http\Resources\DishResource;

class DishController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index() //Time : 10 menit
    {
        return DishResource::collection(Dish::paginate());
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
            'name' => 'required',
            'vendor_id' => 'required'
        ]);

        $vendor = Vendor::find($request['vendor_id']);

        $dish = $vendor->dishes()->create([
            'name' => $request['name'],
        ]);
    
        if(!$dish)
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
        return new DishResource(Dish::findOrFail($id)); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) //Time : 10 Menit
    {
        //
        $request->validate([
            'name' => 'required'
        ]);

        $dish = Dish::find($id);
        
        $dish->name = $request['name'];

        $success = $dish->save();
    
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
    public function destroy($id)//Time : 10 Menit
    {
        //
        $dish=Dish::find($id);
        if(is_null($dish))
        {
            return "Tidak Ditemukan";
        }
    
        $success=$dish->delete();

        if(!$success)
        {
            return "Gagal Hapus";
        }
    
        return "Berhasil Hapus";
    }

    public function getByVendor($id){ //Time : 10 Menit
        $vendor = Vendor::find($id);
        return DishResource::collection(Dish::where('vendor_id',$id)->get());
        // return new DishResource(Dish::where('vendor_id',$id)->get()); 
    }
}
