<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Shop;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
class ShopsController extends Controller
{
    public function home()
    {
      return view('index');
    }

//View the Shops List Sorted by Distance
    public function index()
    {
      $items = Shop::where('liked',0)->orderBy('distance','asc')->paginate(20);
              $response = [
                'pagination' => [
                  'total' => $items->total(),
                  'per_page' => $items->perPage(),
                  'current_page' => $items->currentPage(),
                  'last_page' => $items->lastPage(),
                  'from' => $items->firstItem(),
                  'to' => $items->lastItem()
                ],
                'data' => $items
              ];
        return response()->json($response);
    }
    }
