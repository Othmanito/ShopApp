# ShopApp

 ## Description

it is a simple application that allows you to present a list of the different nearby shops. It also gives the user the opportunity to choose the stores he prefers and add them to the list of Prefered shops. On the other hand it allows him to hide a shop he does not like temporarily .

## Getting Started
These instructions will get you a copy of the project up and running on your local machine for development and testing purposes
### Prerequisites
Before implementing our application, you will need to install several tools such as:    
* Atom,  
* Github Desktop,  
* Wampserver: which includes the Apache and Mysql server and the latest version of PHP (7.0),  
* Composer
* Laravel Framework: Backend,  
* VueJS: Frontend  
### Installing  
First of all, you will create your Laravel project and install it using the Composer  
`composer create-project --prefer-dist laravel/laravel ShopApp`  
Then we move on to our working directory   
`cd C:\wamp64\www\ShopApp`  
After that , you create a connection to our database in the .env file   

    DB_CONNECTION=mysql  
    DB_HOST=127.0.0.1  
    DB_PORT=3306  
    DB_DATABASE=shoppingApp  
    DB_USERNAME=root  
    DB_PASSWORD=******  

Then we create a Migration to our Database    
`php artisan make:migration create_shops_table`    
Our migration file will look like this :  

    
    <?php
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;
    class CreateShopsTable extends Migration
    {
        public function up()
        {
            Schema::create('shops', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('ville');
                $table->string('adresse');
                $table->double('distance');
                $table->boolean('liked');
                $table->timestamps();
            });
        }
        public function down()
        {
            Schema::dropIfExists('shops');
        }
    }  
    



   
Next you run migration      
`php artisan migrate`    
Then after that , you create the Modal Class :  
`php artisan make:model Shop`  
Our modal class will look like this :  

    <?php  
    namespace App\Models;  
    use Illuminate\Database\Eloquent\Model;  
    class Shop extends Model  
    {  
            protected $table='shops';  
            protected $fillable = array('id', 'name', 'ville','adresse','distance','liked','created_at','updated_at');  
    }  
    
After that , you create your first Controller "ShopController"
`php artisan make:controller ShopController --resource`  
It will look like this :  

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

        public function index()
        {
          $items = Shop::where('liked',0)->orderBy('distance','asc')->paginate(10);
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
  
  Then you'll create your second Controller "PrefShopsController" :  
  
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
    
 After creating your controllers, you will create routes for them :  
 
     <?php
    //Welcome Page Route
      Route::get('/', function () {
          return view('welcome');
      });
      //Authentication routes
        Auth::routes();
        
     //Application Routes
        Route::group(['middleware' => ['web']], function() {
        Route::get('/Listshops', 'ShopsController@home')->name('Listshops');
        Route::get('/Likedshops', 'PrefShopsController@liked')->name('Likedshops');
        Route::post('/makeLike/{p_id}', 'PrefShopsController@makeLike')->name('makeLike');
        Route::post('/removeLike/{p_id}', 'PrefShopsController@RemoveLike')->name('removeLike');
        Route::resource('shops','ShopsController',['only' => ['index']]);
      });

 Then you will create the vueJS file for the FrontEnd of the application "Shop.js"  
 
     Vue.http.headers.common['X-CSRF-TOKEN'] = $("#token").attr("value");
     new Vue({
       el :'#shop-vue',
       data :{
         isInvisible:true,
         items: [],
         pagination: {
           total: 0,
           per_page: 2,
           from: 1,
           to: 0,
           current_page: 1
         },
         offset: 4,
         formErrors:{},
         formErrorsUpdate:{},
         newItem : {'name':'','ville':''},
         fillItem : {'name':'','ville':'','id':''}

       },
       computed: {
         isActived: function() {
           return this.pagination.current_page;
         },

         pagesNumber: function() {
           if (!this.pagination.to) {
             return [];
           }
           var from = this.pagination.current_page - this.offset;
           if (from < 1) {
             from = 1;
           }
           var to = from + (this.offset * 2);
           if (to >= this.pagination.last_page) {
             to = this.pagination.last_page;
           }
           var pagesArray = [];
           while (from <= to) {
             pagesArray.push(from);
             from++;
           }
           return pagesArray;
         }
       },
       ready: function() {
         this.getShops(this.pagination.current_page);
         //dislike a shop and keep it hideen
    var $tab = $('#tab');
    if(localStorage.getItem("#tab")) {
        $tab.html(localStorage.getItem("#tab"));
    }
    $("#tab").on('click','.btn-danger',function(){
          $(this).closest('tr').remove();
          localStorage.setItem("#tab", $tab.html());
          setTimeout(function(){localStorage.removeItem("#tab");}, 1000*60*120);
        });
       },
       methods: {
         getShops: function(page) {
           this.$http.get('/shops?page='+page).then((response) => {
             this.$set('items', response.data.data.data);
             this.$set('pagination', response.data.pagination);
           });
         },
         changePage: function(page) {
           this.pagination.current_page = page;
           this.getShops(page);
         }
       }
     });
     
Now you will create the view for the Application : the first view is the layout of the application "Application.js"  
    
    <!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Shopping App</title>
        <meta id="token" name="token" value="{{ csrf_token() }}">
        <!-- Bootstrap -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="css/app.css">
        <link rel="icon" type="image/png" sizes="32x32" href="img/logo-ico.png">
      </head>
      <body>
        <div class="container" id="shop-vue">
          <nav class="navbar navbar-default navbar-static-top">
              <div class="container">
                  <div class="navbar-header">

                      <!-- Collapsed Hamburger -->
                      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                          <span class="sr-only">Toggle Navigation</span>
                          <span class="icon-bar"></span>
                          <span class="icon-bar"></span>
                          <span class="icon-bar"></span>
                      </button>

                      <!-- Branding Image -->
                      <a class="navbar-brand" href="{{ url('/Listshops') }}">
                          {{ config('app.name', 'ShoppingApp') }}
                      </a>
                  </div>
                  <div class="collapse navbar-collapse" id="app-navbar-collapse">
                      <!-- Left Side Of Navbar -->
                      <ul class="nav navbar-nav">
                          &nbsp;
                      </ul>

                      <!-- Right Side Of Navbar -->
                      <ul class="nav navbar-nav navbar-right">
                          <!-- Authentication Links -->
                          @guest
                              <li></li>
                              <li></li>
                          @else
                              <li class="dropdown">
                                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                      {{ Auth::user()->name }} <span class="caret"></span>
                                  </a>

                                  <ul class="dropdown-menu">
                                      <li>
                                          <a href="{{ route('logout') }}"
                                              onclick="event.preventDefault();
                                                       document.getElementById('logout-form').submit();">
                                              Logout
                                          </a>

                                          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                              {{ csrf_field() }}
                                          </form>
                                      </li>
                                  </ul>
                              </li>
                          @endguest
                      </ul>
                  </div>
              </div>
          </nav>
          @yield('content')
        </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.26/vue.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue-resource/1.0.3/vue-resource.min.js"></script>
        <script type="text/javascript" src="/js/shop.js"></script>
      </body>
    </html>

The second view is "index.blade.php" which contains the list of shops sorted by distance  

    @extends('layouts.application')
    @section('content')
    <center>
      <div class="form-group row add">
        <div class="col-md-6">

          <a href="/Listshops"><h4>My Nearby Shops</h3></a>

        </div>
        <div class="col-md-6">
          <a href="/Likedshops"><h4>My Prefered Shops</h3></a>
        </div>
      </div>

      <div class="row">

    <h2>List of nearby Shops</h2>
        <div class="table-responsive">

          <table class="table table-bordered table-hover table-condensed">

           <tr class="active">
              <th><center> Shop Name</th>
              <th><center> Shop City</th>
              <th><center> Distance</th>
              <th><center> Actions</th>
            </tr>

            <tr v-for="item in items" >
              <td><center> @{{ item.name }}</td>
              <td><center> @{{ item.ville }}</td>
              <td><center>@{{ item.distance }} metres</td>

              <td>
                <div class="col-md-2"></div><div class="col-md-2"></div>

                <!-- Bouton like -->
                <div class="col-lg-2">

                 <form action="/makeLike/@{{ item.id }}" method="post">
                   <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button class=" btn btn-success" type="submit" >
                  <span class="glyphicon glyphicon-thumbs-up" ></span> Like
                </button>
              </form>
            </div>

             <!-- Bouton Dislike -->
            <div class="col-lg-2">
              <form>

                <button class="btn btn-danger"  >
                  <span class="glyphicon glyphicon-thumbs-down"></span> Dislike
                </button>
              </form>
            </div>
              </td>
            </tr>
            </div>
          </table>

      </div></div>
      <!-- Systeme de pagination  -->
      <nav>
        <ul class="pagination">
          <li v-if="pagination.current_page > 1">
            <a href="#" aria-label="Previous" @click.prevent="changePage(pagination.current_page - 1)">
              <span aria-hidden="true">«</span>
            </a>
          </li>
          <li v-for="page in pagesNumber" v-bind:class="[ page == isActived ? 'active' : '']">
            <a href="#" @click.prevent="changePage(page)">
              @{{ page }}
            </a>
          </li>
          <li v-if="pagination.current_page < pagination.last_page">
            <a href="#" aria-label="Next" @click.prevent="changePage(pagination.current_page + 1)">
              <span aria-hidden="true">»</span>
            </a>
          </li>
        </ul>
      </nav>
    @stop
    <!-- Fin du Systeme de pagination  -->
    
 The third view is "Likedshops.blade.php"  
 
     @extends('layouts.application')
    @section('content')
    <center>
      <div class="form-group row add">
        <div class="col-md-6">
          <a href="/Listshops"><h4>My Nearby Shops</h3></a>
        </div>
        <div class="col-md-6">
          <a href="/Likedshops"><h4>My Prefered Shops</h3></a>
        </div>
      </div>
      <h2>List of Prefered Shops</h2>
      <div class="row">

        <div class="table-responsive">
          <center>
          <table class="table table-bordered table-hover table-condensed">
            <tr class="active">
              <th>Shop Name</th>
              <th>Shop City</th>
              <th>Distance</th>
              <th>Actions</th>
            </tr>
            @foreach($liked as $item)
            <tr >
              <td>{{ $item->name }}</td>
              <td>{{ $item->ville }}</td>
              <td>{{ $item->distance }} metres</td></div>
                </div>
              </td>
              <td>
                <form action="{{ route('removeLike',[$item->id]) }}" method="post">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button class="edit-modal btn btn-danger" type="submit" >
                  <span class="glyphicon glyphicon-trash"></span> Remove
                </button>
              </form>
              </td>
            </tr>
            @endforeach
          </table>
        </div>
      </div>
      <nav>
        <ul class="pagination">
          <li v-if="pagination.current_page > 1">
            <a href="#" aria-label="Previous" @click.prevent="changePage(pagination.current_page - 1)">
              <span aria-hidden="true">«</span>
            </a>
          </li>
          <li v-for="page in pagesNumber" v-bind:class="[ page == isActived ? 'active' : '']">
            <a href="#" @click.prevent="changePage(page)">
              @{{ page }}
            </a>
          </li>
          <li v-if="pagination.current_page < pagination.last_page">
            <a href="#" aria-label="Next" @click.prevent="changePage(pagination.current_page + 1)">
              <span aria-hidden="true">»</span>
            </a>
          </li>
        </ul>
      </nav>
    @stop

### Authors

* Othmane Essarsri





 
     

  


    


    


