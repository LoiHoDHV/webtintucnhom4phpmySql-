<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Model\UserLevel;;
use App\Model\Page;
use App\Model\Social;
use App\Model\Newsletter;
use App\Model\Contact;
use App\Model\NewsCategory;
use App\Model\News;
use DB; // using DB
use File;
use Image;

use App\Model\System;

class BackController extends Controller
{
    public function _construct(){
        @session_start();
    }


    public function home(){
        return view('back.home.home');
    }


    // staff Management
    public function staff_profile(){
        echo view('back.staff.profile');
    }


    public function staff_profile_post(Request $request){
       
        if($request->fullname == '' || $request->email ==  '' || $request->phone == ''){

            // Thông báo cảnh báo
            return redirect('admin/staff/profile')->with(['flash_level' => 'danger', 'flash_message' => 'Vui lòng điền vào chỗ có dấu *']);
        }


        $User = User::find($request->id);
        
        
        $User->fullname = $request->fullname;;
        $User->email = $request->email;
        $User->phone = $request->phone;
        $User->address = $request->address;
        
        if(isset($request->password) && $request->password !=''){
            $User->password = bcrypt($request->password); // mã hóa md5
        }

        
        
        
        
        $Flag = $User->save();

        if($Flag == true){
            
            return redirect('admin/staff/profile')->with(['flash_level' => 'success', 'flash_message' => 'Cập nhật tài khoản thành công']);
        }else{
            return redirect('admin/staff/profile')->with(['flash_level' => 'danger' , 'flash_message' => 'Lỗi không chỉnh sửa được tài khoản']);
        }

        
    }


    public function staff_list(){

        $User = DB::table('users as a')
        ->join('users_level as b', 'a.level' , '=' , 'b.id')
        ->selectRaw('a.id,a.fullname, a.address , a.email ,a.phone , b.name')->get();
        return view('back.staff.list',compact('User'));
    }

    public function staff_add(){
        
        $UserLevel = UserLevel::where('status',1)->get();
        
        

        return view('back.staff.add',compact('UserLevel'));
    }
    
    public function staff_add_post(Request $request){
        if($request->fullname == '' || $request->email ==  '' || $request->phone == '' || $request->username == '' || $request->password == '' ){

            // Thông báo cảnh báo
            return redirect('admin/staff/add')->with(['flash_level' => 'danger', 'flash_message' => 'Vui lòng điền vào chỗ có dấu *']);
        }


        $User = new User;
        
        $User->level = $request->level;
        $User->status = 1;
        $User->fullname = $request->fullname;
        $User->email = $request->email;
        $User->phone = $request->phone;
        $User->address = $request->address;
        $User->username = $request->username;
        $User->password = $request->password;
        $User->password = bcrypt($request->password);
        
        $Flag = $User->save();

        if($Flag == true){
            return redirect('admin/staff/list')->with(['flash_level' => 'sucess', 'flash_message' => 'Thêm Thành công']);
        }else{
            return redirect('admin/staff/list')->with(['flash_level' => 'danger', 'flash_message' => 'Lỗi thêm dữ liệu']);
        }

    }

    public function staff_edit(Request $request, $id){
        $User = User::find($id);
        
        
        $UserLevel = UserLevel::where('status',1)->get();
        return view('back.staff.edit',compact('User','UserLevel'));
    }

    public function staff_edit_post(Request $request, $id){
        if($request->fullname == '' || $request->email ==  '' || $request->phone == ''){

            // Thông báo cảnh báo
            return redirect('admin/staff/add')->with(['flash_level' => 'danger', 'flash_message' => 'Vui lòng điền vào chỗ có dấu *']);
        }


        $User = User::find($id);
        
        $User->level = $request->level;
        $User->status = $request->status;
        $User->fullname = $request->fullname;
        $User->email = $request->email;
        $User->phone = $request->phone;
        $User->address = $request->address;

        if(isset($request->password) && $request->password !=''){
            $User->password = bcrypt($request->password); // mã hóa md5
        }
        
        $Flag = $User->save();

        if($Flag == true){
            return redirect('admin/staff/edit/'.$id)->with(['flash_level' => 'sucess', 'flash_message' => 'Sửa Thành công']);
        }else{
            return redirect('admin/staff/edit/'.$id)->with(['flash_level' => 'danger', 'flash_message' => 'Lỗi sửa dữ liệu']);
        }


       
        
    }

    public function staff_delete(Request $request,$id){


        $User = User::find($id);
        
        $Flag  = $User->delete();
        if($Flag == true){
            return redirect('admin/staff/list')->with(['flash_level' => 'sucess', 'flash_message' => 'Xóa nhân viên thành công']);
        }else{
            return redirect('admin/staff/list')->with(['flash_level' => 'danger', 'flash_message' => 'Xóa nhân viên không thành công']);
        }
        
    }

    // System management ====---------------------------------
    
    public function system(){

        $logo = System::where('Status',1)->where('Code','logo')->first();
        $favicon = System::where('Status',1)->where('Code','favicon')->first();
        $name = System::where('Status',1)->where('Code','name')->first();

        $email = System::where('Status',1)->where('Code','email')->first();
        $phone = System::where('Status',1)->where('Code','phone')->first();
        $address = System::where('Status',1)->where('Code','address')->first();
        
        $copyright = System::where('Status',1)->where('Code','copyright')->first();

        



        return view('back.system.system', compact(
        'logo','favicon','name','email','phone','address','copyright'));
    }


    public function system_post(Request $request){
        if($request->name == '' || $request->email ==  '' || $request->phone == ''){

            // Thông báo cảnh báo
            return redirect('admin/system')->with(['flash_level' => 'danger', 'flash_message' => 'Vui lòng điền vào chỗ có dấu *']);
        }

        //update ten cong ty
        System::where('Status',1)
        ->where('Code','name')
        ->update(['Description' => $request->name]);

        //update email
        System::where('Status',1)
        ->where('Code','email')
        ->update(['Description' => $request->email]);

        //update sdt
        System::where('Status',1)
        ->where('Code','phone')
        ->update(['Description' => $request->phone]);
        
        // update dia chi
        System::where('Status',1)
        ->where('Code','address')
        ->update(['Description' => $request->address]);

        // update copy riht
        System::where('Status',1)
        ->where('Code','copyright')
        ->update(['Description' => $request->copyright]);

        //ogo
        if(!empty($request->file('logo'))){ 
            

            // lay duong dan logo
            $logo = System::where('Status',1)->where('Code','logo')->first();
            
            $path = 'image/logo.'. $logo->Description;
            
            if(File::exists($path)){
                File::delete($path);
            }

            //upload images
            
            $name = $request->file('logo')->getClientOriginalName();
            
            $request->file('logo')->move('image/logo/', $name);

            $logo->Description = $name;
            
            $logo->save();
        }


        ///favicon
        if(!empty($request->file('favicon'))){ 
            

            // lay duong dan logo
            $favicon = System::where('Status',1)->where('Code','favicon')->first();
            
            $path = 'image/favicon.'. $favicon->Description;
            
            if(File::exists($path)){
                File::delete($path);
            }

            //upload images
            
            $name = $request->file('favicon')->getClientOriginalName();
            
            $request->file('favicon')->move('image/favicon/', $name);

            $favicon->Description = $name;
            
            $favicon->save();
        }


        


        return redirect('admin/system')->with(['flash_level' => 'sucess', 'flash_message' => 'Chỉnh sửa ok']);




    }
    // System management ====---------------------------------


    //page management--------------------------------
    
    public function page_list(){
        $Page = Page::get();
        return view('back.page.list',compact('Page'));
    }

    public function page_edit(Request $request, $id){
        
        $Page = Page::find($id);
        return view('back.page.edit',compact('Page'));
    }

    public function page_edit_post(Request $request, $id){


        if($request->Name == '' || $request->Font ==  ''){

            // Thông báo cảnh báo
            return redirect('admin/page/edit/'.$id)->with(['flash_level' => 'danger', 'flash_message' => 'Vui lòng điền vào chỗ có dấu *']);
        }


        $Page = Page::find($id);
        
        $Page->Status = $request->Status;
        $Page->Name = $request->Name;
        $Page->Font = $request->Font;
        $Page->Sort = $request->Sort;

        
        $Flag = $Page->save();
        


        if($Flag == true){
            return redirect('admin/page/edit/'.$id)->with(['flash_level' => 'sucess', 'flash_message' => 'Sửa Thành công']);
        }else{
            return redirect('admin/page/edit/'.$id)->with(['flash_level' => 'danger', 'flash_message' => 'Lỗi sửa dữ liệu']);
        }
    }
    
    //page management--------------------------------


    //social management -----------------
    public function social_list(){
        
        $Social = Social::get();
        return view('back.social.list',compact('Social'));
    }

    public function social_edit(Request $request, $id){
        $Social = Social::find($id);
        return view('back.social.edit',compact('Social'));
    }
    
    public function social_edit_post(Request $request,$id){
        if($request->Name == '' || $request->Font ==  ''){

            // Thông báo cảnh báo
            return redirect('admin/social/edit/'.$id)->with(['flash_level' => 'danger', 'flash_message' => 'Vui lòng điền vào chỗ có dấu *']);

        }
        $Social = Social::find($id);
        
        $Social->Status = $request->Status;
        $Social->Name = $request->Name;
        $Social->Font = $request->Font;
        $Social->Sort = $request->Sort;

        
        $Flag = $Social->save();
        


        if($Flag == true){
            return redirect('admin/social/edit/'.$id)->with(['flash_level' => 'sucess', 'flash_message' => 'Sửa Thành công']);
        }else{
            return redirect('admin/social/edit/'.$id)->with(['flash_level' => 'danger', 'flash_message' => 'Lỗi sửa dữ liệu']);
        }
    }

    //social management -----------------


    //newsletter management -----------------

    public function newsletter_list(){
        $NewsLetter = Newsletter::get();
        return view('back.newsletter.list',compact('NewsLetter'));
    }

    public function newsletter_edit(Request $request, $id){
        
        $NewsLetter = Newsletter::find($id);
        return view('back.newsLetter.edit',compact('NewsLetter'));
    }

    public function newsletter_edit_post(Request $request, $id){


        if($request->Email == ''){

            // Thông báo cảnh báo
            return redirect('admin/newsletter/edit/'.$id)->with(['flash_level' => 'danger', 'flash_message' => 'Vui lòng điền vào chỗ có dấu *']);
        }


        $NewsLetter = NewsLetter::find($id);
        
        $NewsLetter->IsViews = $request->Status;
        $NewsLetter->Email = $request->Email;
        
        
        $Flag = $NewsLetter->save();
        


        if($Flag == true){
            return redirect('admin/newsletter/edit/'.$id)->with(['flash_level' => 'sucess', 'flash_message' => 'Sửa Thành công']);
        }else{
            return redirect('admin/newsletter/edit/'.$id)->with(['flash_level' => 'danger', 'flash_message' => 'Lỗi sửa dữ liệu']);
        }
    }


    public function newsletter_delete(Request $request,$id){
        $NewsLetter = NewsLetter::find($id);
        
        $Flag  = $NewsLetter->delete();

        if($Flag == true){
            return redirect('admin/newsletter/list')->with(['flash_level' => 'sucess', 'flash_message' => 'Xóa email thành công']);
        }else{
            return redirect('admin/newsletter/list')->with(['flash_level' => 'danger', 'flash_message' => 'Xóa email không thành công']);
        }
    }
    //newsletter management -----------------

    //Quản lý liên hệ ---- Contact management


    public function contact_list(){
        $Contact = Contact::get();
        return view('back.contact.list',compact('Contact'));
    }

    public function contact_edit(Request $request, $id){
        
        $Contact = Contact::find($id);
        return view('back.contact.edit',compact('Contact'));
    }

    public function contact_edit_post(Request $request, $id){


        if($request->Email == '' || $request->Name == '' || $request->Phone = '' || $request->Message == ''){

            // Thông báo cảnh báo
            return redirect('admin/contact/edit/'.$id)->with(['flash_level' => 'danger', 'flash_message' => 'Vui lòng điền vào chỗ có dấu *']);
        }


        $Contact = Contact::find($id);
        

        $Contact->IsViews = $request->IsViews;
        
        $Contact->Name = $request->Name;
        $Contact->Email = $request->Email; 
        $Contact->Phone = $request->Phone;
        
        $Contact->Message = $request->Message;
        
        
        
        $Flag = $Contact->save();
        


        if($Flag == true){
            return redirect('admin/contact/edit/'.$id)->with(['flash_level' => 'sucess', 'flash_message' => 'Sửa Thành công']);
        }else{
            return redirect('admin/contact/edit/'.$id)->with(['flash_level' => 'danger', 'flash_message' => 'Lỗi sửa dữ liệu']);
        }
    }


    public function contact_delete(Request $request,$id){
        $Contact = Contact::find($id);
        
        $Flag  = $Contact->delete();

        if($Flag == true){
            return redirect('admin/contact/list')->with(['flash_level' => 'sucess', 'flash_message' => 'Xóa liên hệ thành công']);
        }else{
            return redirect('admin/contact/list')->with(['flash_level' => 'danger', 'flash_message' => 'Xóa liên hệ không thành công']);
        }
    }



    //Quản lý liên hệ ---- Contact management

    //Quản lý danh mục -- News_Category management

    public function news_cat_list(){
        
        $NewsCategory = NewsCategory::where('Status',1)->get();

        return view('back.news.cat_list',compact('NewsCategory'));
    }

    public function news_cat_getedit(Request $request, $RowID){

        $NewsCategory = NewsCategory::find($RowID);

        return view('back.news.cat_edit',compact('NewsCategory'));

    }

    public function news_cat_edit(Request $request,$RowID){
        if( $request->Name == ''){

            // Thông báo cảnh báo
            return redirect('admin/news_cat/edit/'.$RowID)->with(['flash_level' => 'danger', 'flash_message' => 'Vui lòng điền vào chỗ có dấu *']);
        }

        $NewsCategory = NewsCategory::find($RowID);

        $NewsCategory->Name = $request->Name;
        $NewsCategory->Status = $request->Status;
        

        $Flag = $NewsCategory->save();

        if($Flag == true){
            return redirect('admin/news_cat/edit/'.$RowID)->with(['flash_level' => 'sucess', 'flash_message' => 'Sửa Thành công']);
        }else{
            return redirect('admin/news_cat/edit/'.$RowID)->with(['flash_level' => 'danger', 'flash_message' => 'Lỗi sửa dữ liệu']);
        }

        
        
        
    }
    //Quản lý danh mục -- News_Category management

    // Quản lý tin tức -- -News Management --------------------------------
    public function news_list(){

        $News = DB::table('news as a')
        ->join('news_cat as b', 'a.RowIDCat', '=' , 'b.RowID')
        ->selectRaw('a.*, b.Name as CategoryName')
        ->orderBy('a.RowId','DESC')
        ->get();

        return view('back.news.list',compact('News'));

    }


    public function news_getadd(){

        $NewsCategory = NewsCategory::get();

        return view('back.news.add',compact('NewsCategory'));

    }

    public function news_add(Request $request){
        if($request->Name == '' || $request->Description == ''){

            // Thông báo cảnh báo
            return redirect('admin/news/add')->with(['flash_level' => 'danger', 'flash_message' => 'Vui lòng điền vào chỗ có dấu *']);

        }


        $News = new News;
        
        $News->Status = $request->Status;
        $News->RowIDCat = $request->RowIDCat;
        $News->Name = $request->Name;
        $News->MetaTitle = $request->MetaTitle;
        $News->MetaDescription = $request->MetaDescription;
        $News->MetaKeyword = $request->MetaKeyword;
        $News->SmallDescription = $request->SmallDescription;
        $News->Description  = $request->Description;

            // 
        // if($request->hasFile('Images')){

        //     $file = $request->file('Images');
            
        //     //$random_digit = rand(00000000, 999999999);

        //     $name = $file->getClientOriginalName();
            
        //     $duoi = strtolower($file->getClientOriginalExtension());
            

        //     if($duoi != 'png' && $duoi != 'jpg' && $duoi != 'jpeg' && $duoi != 'svg'){
        //         return back()->with(['flash_level' => 'danger', 'flash_message' => 'Đuôi file không hỗ trợ.']);
            
        //     }

        //     $file->move('/public/tmp/',$name);
            
        //     $img = Image::make('/public/tmp/'.$name);

        //     // kiểm tra, nếu không tồn tại thì tọa folder

        //     $filePath = '/public/tmp/'.date('Ymd');
        //      // 0777 phân quyền đọc ghi được, larvel cung cấp
        //     if(!file_exists($filePath)){
        //         mkdir('/public/tmp/'.date('Ymd').'/',0777,true);
        //     }
            
        //     $img->fit(400,300);
            
        //     $img->save('/public/tmp/'.date('Ymd').'/'.$name);
            
        //     // delete images upload images
        //     if(file_exists('/public/tmp/'.$name)){
        //         unlink('/public/tmp/'.$name);
        //     }

        //     // $News->Images = date('Ymd').'/'.$name;

        //     $News->Images = "true";
        // }


        /// kiểm tra không thể upload ảnh lên, địt con mẹ ảo ma canada
        $News->Images = $request->Images;



        
        $Flag = $News->save();

        if($Flag == true){
            return redirect('admin/news/list')->with(['flash_level' => 'sucess', 'flash_message' => 'Thêm Thành công']);
        }else{
            return redirect('admin/news/list')->with(['flash_level' => 'danger', 'flash_message' => 'Lỗi thêm dữ liệu']);
        }


        

    }

    public function news_delete(Request $request , $RowID){


        $News = News::find($RowID);
        
        $Flag  = $News->delete();

        if($Flag == true){
            return redirect('admin/news/list')->with(['flash_level' => 'sucess', 'flash_message' => 'Xóa tin tức thành công']);
        }else{
            return redirect('admin/news/list')->with(['flash_level' => 'danger', 'flash_message' => 'Xóa tin tức không thành công']);
        }

    }


    public function news_getedit(Request $request, $RowID){

        $NewsCategory = NewsCategory::get();
        
        $News = News::find($RowID);

        return view('back.news.edit',compact('News','NewsCategory'));

    }

    public function news_edit(Request $request, $RowID){


        if($request->Name == '' || $request->Description == ''){

            // Thông báo cảnh báo
            return redirect('admin/news/edit/'.$RowID)->with(['flash_level' => 'danger', 'flash_message' => 'Vui lòng điền vào chỗ có dấu *']);

        }


        $News = News::find($RowID);
        
        $News->Status = $request->Status;
        $News->RowIDCat = $request->RowIDCat;
        $News->Name = $request->Name;
        $News->MetaTitle = $request->MetaTitle;
        $News->MetaDescription = $request->MetaDescription;
        $News->MetaKeyword = $request->MetaKeyword;
        $News->SmallDescription = $request->SmallDescription;
        $News->Description  = $request->Description;
        $News->Images = $request->Image;

        
        $Flag = $News->save();

        if($Flag == true){
            return redirect('admin/news/edit/'.$RowID)->with(['flash_level' => 'sucess', 'flash_message' => 'Chỉnh sửa tin tức thành công']);
        }else{
            return redirect('admin/news/edit/'.$RowId)->with(['flash_level' => 'danger', 'flash_message' => 'Lỗi sửa tin tức']);
        }

    }

    

    



    // Quản lý tin tức -- -News Management --------------------------------
    
}

