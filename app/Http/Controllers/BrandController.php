<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Carbon;
use Image;

class BrandController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    
    public function index(){
        $brand = Brand::latest()->paginate(5);
        return view('bac.brand.index', compact('brand'));
    }


    public function store(Request $request){

        $this->validate($request, [
            'brand_name' => ['required', 'unique:brands', 'min:4'],
            'brand_image'=>'required|mimes:jpg,jpeg,png',
        ]);
     


         $brand_image = $request->file('brand_image');
        // $name_gen = hexdec(uniqid());
        // $image_ext = strtolower($brand_image->getClientOriginalExtension());
        // $image_name = $name_gen.'.'.$image_ext;
        // $up_location = 'image/brand/';
        // $last_img = $up_location.$image_name ;
        // $brand_image->move($up_location,$image_name);
            
         $name_gen = hexdec(uniqid()).'.'.$brand_image->getClientOriginalExtension();
         Image::make($brand_image)->resize(300,200)->save('image/brand/'.$name_gen);
         $last_image = 'image/brand/'.$name_gen;   

        Brand::insert([
            'brand_name' => $request->brand_name,
            'brand_image'=> $last_image,
            'created_at' =>Carbon::now()
        ]);
        return redirect()->back()->with('message', 'barnd successfully');
    }


    public function edit($id) {
        $brands = Brand::find($id);
        // dd($brands);
        return view('bac.brand.edit', compact('brands'));
    }

    public function update(Request $request , $id) {
        $this->validate($request, [
            'brand_name' => ['required', 'min:4'],
            
        ]);

        $old_image = $request->old_image;

        $brand_image = $request->file('brand_image');
        if($brand_image){
            // $name_gen = hexdec(uniqid());
            // $image_ext = strtolower($brand_image->getClientOriginalExtension());
            // $image_name = $name_gen.'.'.$image_ext;
            // $up_location = 'image/brand/';
            // $last_img = $up_location.$image_name ;
            // $brand_image->move($up_location,$image_name);

            $name_gen = hexdec(uniqid()).'.'.$brand_image->getClientOriginalExtension();
            Image::make($brand_image)->resize(300,200)->save('image/brand/'.$name_gen);
            $last_image = 'image/brand/'.$name_gen;

            unlink($old_image);
    
            Brand::find($id)->update([
                'brand_name' => $request->brand_name,
                'brand_image'=> $last_image,
                'created_at' =>Carbon::now()
            ]);
            return redirect()->back()->with('message', 'barnd updated successfully');
        } else{
            Brand::find($id)->update([
                'brand_name' => $request->brand_name,
                'created_at' =>Carbon::now()
            ]);
            return redirect()->back()->with('message', 'barnd updated successfully');
        }
     
    }

    public function delete($id){

        $image = Brand::find($id);
        $old_image = $image->brand_image;
        unlink($old_image);

        Brand::find($id)->delete(); 
        return redirect()->back()->with('message', 'barnd delete successfully');
    }
}
