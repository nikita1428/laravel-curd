<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\If_;

class ProductController extends Controller
{
    public function index(){

        $product = DB::table('products')->latest()->paginate(5);

        return view('product.index', compact('product'));
    }


   public function create(){
       return view('product.create');
   }

   public function Store(Request $request){
      
      $data = array();
      $data['product_name'] = $request->product_name;
      $data['product_code'] = $request->product_code;
      $data['details'] = $request->details;
      
      if(!$data){
         echo "please fill all the data";
      }


      $image = $request->file('logo');
       if ($image) {
          $image_name = date('dmy_H_s_i');
          $ext = strtolower($image->getClientOriginalExtension());
          $image_full_name = $image_name.'.'.$ext;
          $upload_path = 'public/media/';
          $image_url = $upload_path.$image_full_name;
          $success = $image->move($upload_path,$image_full_name);

          $data['logo'] = $image_url;
          $product = DB::table('products')->insert($data);
          return redirect()->route('product.index')
                      ->with('success','Product Created Successfully');


      }
   }


   public function Edit($id){
        $product = DB::table('products')->where('id',$id)->first();
         return view('product.edit',compact('product'));
   }

   public function Update(Request $request, $id){

    $oldlogo = $request->old_logo;

    $data = array();
    $data['product_name'] = $request->product_name;
    $data['product_code'] = $request->product_code;
    $data['details'] = $request->details;
    
    $image = $request->file('logo');
     if ($image) {
         unlink($oldlogo);
        $image_name = date('dmy_H_s_i');
        $ext = strtolower($image->getClientOriginalExtension());
        $image_full_name = $image_name.'.'.$ext;
        $upload_path = 'public/media/';
        $image_url = $upload_path.$image_full_name;
        $success = $image->move($upload_path,$image_full_name);

        $data['logo'] = $image_url;
        $product = DB::table('products')->where('id',$id)->update($data);
        return redirect()->route('product.index')
                    ->with('success','Product Updated Successfully');


    }

   }

     public function Delete($id){
       
        $data = DB::table('products')->where('id',$id)->first();
        $image = $data->logo;
        unlink($image);
        $product = DB::table('products')->where('id',$id)->delete();
        return redirect()->route('product.index')
        ->with('success','Product Deleted Successfully');
     }


     public function Show($id){
 
        $data = DB::table('products')->where('id',$id)->first();
        return view('product.show',compact('data'));
     }

}
