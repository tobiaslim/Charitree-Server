<?php

namespace App\Http\Controllers;

use App\Models\Item;

class ItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //

    public function getItems(){
        $items = Item::all();
        if(is_null($items)){
            return response()->json(['status'=>'0', 'errors'=>["No items found."]]);    
        }
        return response()->json(['status'=>'1', 'items'=>$items]);
    }
}
