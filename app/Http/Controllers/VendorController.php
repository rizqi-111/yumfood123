<?php

namespace App\Http\Controllers;

use App\Http\Resources\VendorResource;
use App\Vendor;
use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return VendorResource::collection(Vendor::paginate());
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
            'name' => 'required|max:128',
            'logo' => 'required',
            'tag' => 'required'
        ]);

        $vendor = new Vendor;

        $vendor->name=$request['name'];
        $vendor->logo=$request['logo'];
        
        $tags = explode(',',$request['tag']);
        
        $tag_ids = [];
        foreach($tags as $t_name){
            $tag = Tag::firstOrCreate(['name' => $t_name]);
            $tag_ids[] = $tag->id;
        }

        $success=$vendor->save();

        $vendor->tags()->sync($tag_ids);
    
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
