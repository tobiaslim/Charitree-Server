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
        return response()->json(['status'=>'1', 'items'=>$items]);
    }
}
