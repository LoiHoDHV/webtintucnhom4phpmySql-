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

class FrontController extends Controller
{
    public function _construct(){
        @session_start();
    }


    public function home(){
        return view('front.home.home');
    }

    public function contact(){
        
        echo "This is contact page";
    }


  
    
}

