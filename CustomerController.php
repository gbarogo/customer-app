<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

// Activamos uso de caché.
use Illuminate\Support\Facades\Cache;

// Necesitaremos el modelo Userdb para ciertas tareas.
use App\Userdb;

// Necesitamos la clase Response para crear la respuesta especial con la cabecera de localización en el método Store()
use Response;

class userdbController extends Controller {

    // Configuramos en el constructor del controlador la autenticación usando el Middleware auth.basic,
    // pero solamente para los métodos de crear, actualizar y borrar.
    public function __construct()
    {
        $this->middleware('auth.basic',['only'=>['store','update','destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
        public function index()
        {
            // Devuelve todos los userdb en JSON.
            // return Userdb::all();

            // Mejora en la respuesta.
            // Devolvemos explícitamente el código 200 http de datos encontrados.
            // Se puede poner como 404 cuando no se encuentra nada.
            //return response()->json(['datos'=>Userdb::all()],200);

            // Activamos la caché de los resultados.
            //  Cache::remember('tabla', $minutes, function()
            $userdb=Cache::remember('userdb',20/60, function()
            {
                // Caché válida durante 20 segundos.
                return Userdb::all();
            });

            // Con caché.
            return response()->json(['status'=>'ok','data'=>$userdb], 200);

            // Sin caché.
            //return response()->json(['status'=>'ok','data'=>userdb::all()], 200);
        }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */

    // Pasamos como parámetro al método store todas las variables recibidas de tipo Request
    // utilizando inyección de dependencias (nuevo en Laravel 5)
    // Para acceder a Request necesitamos asegurarnos que está cargado use Illuminate\Http\Request;
    // Información sobre Request en: http://laravel.com/docs/5.0/requests 
    // Ejemplo de uso de Request:  $request->input('name');
    public function store(Request $request)
    {

        // Primero comprobaremos si estamos recibiendo todos los campos.
        if (!$request->input('dni') || !$request->input('phone_number') || !$request->input('name')|| !$request->input('second_surname')|| !$request->input('surname'))
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para errores de validación.
            return response()->json(['errors'=>array(['code'=>422,'message'=>'Faltan datos necesarios para el proceso de alta.'])],422);
        }

        // Insertamos una fila en userdb con create pasándole todos los datos recibidos.
        // En $request->all() tendremos todos los campos del formulario recibidos.
        $nuevoUserdb=Userdb::create($request->all());

        // Más información sobre respuestas en http://jsonapi.org/format/
        // Devolvemos el código HTTP 201 Created – [Creada] Respuesta a un POST que resulta en una creación. Debería ser combinado con un encabezado Location, apuntando a la ubicación del nuevo recurso.
        $response = Response::make(json_encode(['status'=>'ok','data'=>$nuevoUserdb]), 201)->header('Location', 'http://www.dominio.local/userdb/'.$nuevoUserdb->dni)->header('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $dni
     * @return Response
     */
    public function show($dni)
    {
        //
        // return "Se muestra userdb con dni: $dni";
        // Buscamos un userdb por el dni.
        $userdb=Userdb::find($dni);

        // Si no existe ese userdb devolvemos un error.
        if (!$userdb)
        {
            // Si queremos mantener una tabla de códigos de error en nuestra aplicación  lo ideal sería enviar un mensaje de error como:
            // codigo 1000 (código específico de error en nuestra app)
            // código http a enviar 404 de recurso solicitado no existe.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un userdb con ese código.'])],404);
        }

        return response()->json(['status'=>'ok','data'=>$userdb],200);

    }



    /**
     * Update the specified resource in storage.
     *
     * @param  string  $dni
     * @return Response
     */
    public function update(Request $request, $dni)
    {
        // Comprobamos si el userdb que nos están pasando existe o no.
        $userdb=Userdb::find($dni);

        // Si no existe ese userdb devolvemos un error.
        if (!$userdb)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un userdb con ese código.'])],404);
        }       

        // Listado de campos recibidos teóricamente.
        $dni=$request->input('dni');
        $phone_number=$request->input('phone_number');
        $name=$request->input('name');
        $second_surname=$request->input('second_surname');
        $surname=$request->input('surname');

        // Necesitamos detectar si estamos recibiendo una petición PUT o PATCH.
        // El método de la petición se sabe a través de $request->method();
        if ($request->method() === 'PATCH')
        {
            // Creamos una bandera para controlar si se ha modificado algún dato en el método PATCH.
            $bandera = false;

            // Actualización parcial de campos.
            if ($dni != null && $dni!='')
            {
                $userdb->dni = $dni;
                $bandera=true;
            }

            if ($phone_number != null && $phone_number!='')
            {
                $userdb->phone_number = $phone_number;
                $bandera=true;
            }


            if ($name != null && $name!='')
            {
                $userdb->name = $name;
                $bandera=true;
            }

            if ($second_surname != null && $second_surname!='')
            {
                $userdb->second_surname = $second_surname;
                $bandera=true;
            }

            if ($surname != null && $surname!='')
            {
                $userdb->surname = $surname;
                $bandera=true;
            }

            if ($bandera)
            {
                // Almacenamos en la base de datos el registro.
                $avion->save();
                return response()->json(['status'=>'ok','data'=>$userdb], 200);
            }
            else
            {
                // Se devuelve un array errors con los errores encontrados y cabecera HTTP 304 Not Modified – [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo
                // Este código 304 no devuelve ningún body, así que si quisiéramos que se mostrara el mensaje usaríamos un código 200 en su lugar.
                return response()->json(['errors'=>array(['code'=>304,'message'=>'No se ha modificado ningún dato de userdb.'])],304);
            }
        }


        // Si el método no es PATCH entonces es PUT y tendremos que actualizar todos los datos.
        if (!$dni || !$phone_number || !$name || !$second_surname || !$surname)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para errores de validación.
            return response()->json(['errors'=>array(['code'=>422,'message'=>'Faltan datos necesarios para el proceso de alta.'])],422);
        }

        $userdb->dni = $dni;
        $userdb->phone_number = $phone_number;
        $userdb->name = $name;
        $userdb->second_surname = $second_surname;
        $userdb->surname = $surname;

        // Almacenamos en la base de datos el registro.
        $userdb->save();
        return response()->json(['status'=>'ok','data'=>$userdb], 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $dni
     * @return Response
     */
    public function destroy($dni)
    {
        // Primero eliminaremos todos los customer de un userdb y luego el userdb en si mismo.
        // Comprobamos si el userdb que nos están pasando existe o no.
        $userdb=Userdb::find($dni);

        // Si no existe ese userdb devolvemos un error.
        if (!$userdb)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un userdb con ese código.'])],404);
        }       

        // El userdb existe entonces buscamos todos los customers asociados a ese userdb.
        $customer = $userdb->customer; // Sin paréntesis obtenemos el array de todos los customer.

        // Comprobamos si tiene customer ese userdb.
        if (sizeof($customer) > 0)
        {
            // Devolveremos un código 409 Conflict - [Conflicto] Cuando hay algún conflicto al procesar una petición, por ejemplo en PATCH, POST o DELETE.
            return response()->json(['code'=>409,'message'=>'Este userdb posee vehiculos asociados y no puede ser eliminado.'],409);
        }

        // Procedemos por lo tanto a eliminar el userdb.
        $userdb->delete();

        // Se usa el código 204 No Content – [Sin Contenido] Respuesta a una petición exitosa que no devuelve un body (como una petición DELETE)
        // Este código 204 no devuelve body así que si queremos que se vea el mensaje tendríamos que usar un código de respuesta HTTP 200.
        return response()->json(['code'=>204,'message'=>'Se ha eliminado el userdb correctamente.'],204);
        
    }
}