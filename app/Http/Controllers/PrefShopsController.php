<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Shop;
use DB;
class PrefShopsController extends Controller
{
  //Afficher la liste des Liked Shops (Prefered shops)
  public function liked()
  {
    $liked = Shop::where('liked','1')->paginate(10);
            $response = [
              'pagination' => [
                'total' => $liked->total(),
                'per_page' => $liked->perPage(),
                'current_page' => $liked->currentPage(),
                'last_page' => $liked->lastPage(),
                'from' => $liked->firstItem(),
                'to' => $liked->lastItem()
              ],
              'data' => $liked
            ];

      return view('likedshops',compact('liked'));
  }
//Faire un like et l'ajouter au preferedList
  public function makeLike($id)
  {
    try{
      DB::table('shops')
                  ->where('id', $id)
                  ->update(['liked' => 1]);
      }catch (Exception $e) {
            return redirect()->back()->withInput()->withAlertDanger("Erreur .<br>Messagde d'erreur: <b>" . $e->getMessage() . "</b>");
        }
  return redirect()->route('Listshops')->withInput()->withAlertSuccess("The shop has been addesd to your prefered shops successfully");
  }
//Retirer le like de la liste des prefferedList
  public function RemoveLike($id)
  {
    try{
      DB::table('shops')
                  ->where('id', $id)
                  ->update(['liked' => 0]);
  }catch (Exception $e) {
            return redirect()->back()->withInput()->withAlertDanger("Erreur .<br>Messagde d'erreur: <b>" . $e->getMessage() . "</b>");
        }
          return redirect()->route('Likedshops')->withInput()->withAlertSuccess("The shop has been removed from the prefered shops successfully");
  }
}
