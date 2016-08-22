<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::group(['middleware'=> ['web']], function(){
Route::auth();

    Route::get('/', function () {
    return view('welcome');
});
    Route::get('/',array('uses' => 'HomeController@index', 'as' => 'home'));

    //Route::get('/home', 'HomeController@index');
});

Route::get('datatable','DatatableController@index');

Route::post('datatable/show1','DatatableController@show1');
Route::post('datatable/csv_Export','DatatableController@csv_Export');
Route::post('datatable/pdf_Export','DatatableController@pdf_Export');
Route::get('delete/{id}','DatatableController@delete');
Route::get('edit/{id}','DatatableController@edit');
Route::post('datatable/update','DatatableController@update');