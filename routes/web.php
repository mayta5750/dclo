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

Route::get('/', function () {
    return view('welcome');
});



Route::group(['middleware' => 'auth'], function() {


    Route::get('/borrar', 'TestController@borrar');

    Route::get('/words/lote', 'TestController@lote');
    Route::post('/words/lote', 'TestController@lote');
    Route::get('/words/eliminar/{id}'.'_'.'{nueva_carpeta}', 'TestController@destroy');
    Route::match(["get", "post"],'/import', 'TestController@import');
    Route::get('/import', 'TestController@import');
    
    Route::post('/words/vistalote', 'TestController@vistalote');
    Route::get('/words/vistalote{id}', 'TestController@vistalote_fecha');
    Route::get('/words/listarlote', 'TestController@listarlote');
    
    ///////////////////////////////////////////////////////////////////////////
    Route::get('password', 'UserController@password');
    Route::post('updatepassword', 'UserController@updatePassword');

Route::resource('words', 'WordsController');


});
Auth::routes();
