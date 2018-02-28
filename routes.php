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
// Versionado de la ruta
Route::group(array('prefix'=>'api/v1.0'),function()
{
// resource recibe nos parámetros(URI del recurso, Controlador que gestionará las peticiones)
Route::resource('userdbs','UserdbController',['except'=>['edit','create'] ]); // Todos los métodos menos Edit que mostraría un formulario de edición.

// Si queremos dar  la funcionalidad de ver todos los customers tendremos que crear una ruta específica.
// Pero de customers solamente necesitamos solamente los métodos index y show.
// Lo correcto sería hacerlo así:
Route::resource('customers','CustomerController',[ 'only'=>['index','show'] ]); // El resto se gestionan en UserdbCustomerController

// Como la clase principal es users y un customer no se puede crear si no le indicamos el user, 
// entonces necesitaremos crear lo que se conoce como  "Recurso Anidado" de users con customers.
// Definición del recurso anidado:
Route::resource('userdbs.customers','UserdbCustomerController',[ 'except'=>['show','edit','create'] ]);
});