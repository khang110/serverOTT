<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\GroupDetail;
use Illuminate\Support\Facades\Auth;



class GroupController extends Controller
{
    public function store(Request $request)
    {
        $group = new Group();
        $group->name_group = $request->name;
        $group->id_creator = Auth::id();
        $group->description = $request->description;
        $group->save();

         $members=$request->members;
         $members=trim($members,"[");
         $members=trim($members,"]");

         $array=explode(",",$members); 
         
       foreach ($array as $value) {
           $group_detail= new GroupDetail();
           $group_detail->id_user = (int)$value;
           $group_detail->id_group = $group->id;
           $group_detail->id_creator = Auth::id();
           $group_detail->save();
        }
        return true;
    }
}
