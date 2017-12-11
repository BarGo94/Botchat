<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::match(['get', 'post'], '/botman', 'BotManController@handle');
Route::get('categories', 'PrestaController@Category');
Route::get('souscateg/{id}', 'PrestaController@SousCat')->where('id','[0-9]+');
Route::get('products/{id}', 'PrestaController@MyProductsByIdCat')->where('id','[0-9]+');
Route::get('products/{name}', 'PrestaController@MyProductByName');
Route::get('reduction', 'PrestaController@reduction');
Route::get('getCatId/{name}', 'PrestaController@getCatId');
