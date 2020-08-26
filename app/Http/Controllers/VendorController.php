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
    public function index(Request $request)
    {
        if($request['tags'][0] === null) {
            return VendorResource::collection(Vendor::paginate());
        }
        else { //Time : 2 Jam
            $tag_s = [];
            foreach($request['tags'] as $t_name){
                $tag = Tag::where('name',$t_name)->first();
                if($tag){
                    $tag_s[] = $tag->id;
                }
            }

            $vendor = Vendor::whereHas('tags', function($query) use ($tag_s) {

                $query->whereIn('id', $tag_s);

            });

            return $vendor->get()->toJson();
        }
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
            return json_encode("gagal masuk");
        }
    
        return json_encode("berhasil masuk");
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
            return json_encode("gagal ubah");
        }
    
        return json_encode("berhasil ubah");

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
            return json_encode("Tidak Ditemukan");
        }
    
        $success=$vendor->delete();

        $success = $vendor->tags()->detach();

        if(!$success)
        {
            return json_encode("Berhasil Hapus Vendor, Tidak Ditemukan Taggable");
        }
    
        return json_encode("Berhasil Hapus");
    }
}
