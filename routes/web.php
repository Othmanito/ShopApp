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
