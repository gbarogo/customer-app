<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

// Necesita los dos modelos Userdb y Customer
use App\Userdb;
use App\Customer;

// Necesitamos la clase Response para crear la respuesta especial con la cabecera de localización en el método Store()
use Response;

// Activamos uso de caché.
use Illuminate\Support\Facades\Cache;

class UserdbCustomerController extends Controller {
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
    public function index($emailUserdb)
    {
        // Devolverá todos los customer.
        //return "Mostrando los customer del userdb con Id $emailUserdb";
        $userdb=Userdb::find($emailUserdb);

        if (! $userdb)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un userdb con ese código.'])],404);
        }

        // Activamos la caché de los resultados.
        // Como el closure necesita acceder a la variable $ userdb tenemos que pasársela con use($userdb)
        // Para acceder a los modelos no haría falta puesto que son accesibles a nivel global dentro de la clase.
        //  Cache::remember('tabla', $minutes, function()
        $customerUser=Cache::remember('claveCustomer',2, function() use ($userdb)
        {
            // Caché válida durante 2 minutos.
            return $userdb->customer()->get();
        });

        // Respuesta con caché:
        return response()->json(['status'=>'ok','data'=>$customerUser],200);

        // Respuesta sin caché:
        //return response()->json(['status'=>'ok','data'=>$userdb->customer()->get()],200);
        //return response()->json(['status'=>'ok','data'=>$userdb->customer],200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request,$emailUserdb)
    {
        /* Necesitaremos el userdb_id que lo recibimos en la ruta
         #Serie (auto incremental)
        Modelo
        Longitud
        Capacidad
        Velocidad
        Alcance */

        // Primero comprobaremos si estamos recibiendo todos los campos.
        if ( !$request->input('modelo') || !$request->input('longitud') || !$request->input('capacidad') || !$request->input('velocidad') || !$request->input('alcance') )
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para errores de validación.
            return response()->json(['errors'=>array(['code'=>422,'message'=>'Faltan datos necesarios para el proceso de alta.'])],422);
        }

        // Buscamos el userdb.
        $userdb= Userdb::find($emailUserdb);

        // Si no existe el userdb que le hemos pasado mostramos otro código de error de no encontrado.
        if (!$userdb)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un userdb con ese código.'])],404);
        }

        // Si el userdb existe entonces lo almacenamos.
        // Insertamos una fila en customer con create pasándole todos los datos recibidos.
        $nuevoCustomer=$userdb->Customer()->create($request->all());

        // Más información sobre respuestas en http://jsonapi.org/format/
        // Devolvemos el código HTTP 201 Created – [Creada] Respuesta a un POST que resulta en una creación. Debería ser combinado con un encabezado Location, apuntando a la ubicación del nuevo recurso.
        $response = Response::make(json_encode(['data'=>$nuevoAvion]), 201)->header('Location', 'http://www.dominio.local/customer/'.$nuevoAvion->serie)->header('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($emailUserdb,$idAvion)
    {
        //
        return "Se muestra avión $idAvion del userdb $emailUserdb";
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $emailUserdb, $idAvion)
    {
        // Comprobamos si el userdb que nos están pasando existe o no.
        $userdb=userdb::find($emailUserdb);

        // Si no existe ese userdb devolvemos un error.
        if (!$userdb)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un userdb con ese código.'])],404);
        }       

        // El userdb existe entonces buscamos el avion que queremos editar asociado a ese userdb.
        $avion = $userdb->customer()->find($idAvion);

        // Si no existe ese avión devolvemos un error.
        if (!$avion)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un avión con ese código asociado a ese userdb.'])],404);
        }   


        // Listado de campos recibidos teóricamente.
        $modelo=$request->input('modelo');
        $longitud=$request->input('longitud');
        $capacidad=$request->input('capacidad');
        $velocidad=$request->input('velocidad');
        $alcance=$request->input('alcance');

        // Necesitamos detectar si estamos recibiendo una petición PUT o PATCH.
        // El método de la petición se sabe a través de $request->method();
        /*  Modelo      Longitud        Capacidad       Velocidad       Alcance */
        if ($request->method() === 'PATCH')
        {
            // Creamos una bandera para controlar si se ha modificado algún dato en el método PATCH.
            $bandera = false;

            // Actualización parcial de campos.
            if ($modelo != null && $modelo!='')
            {
                $avion->modelo = $modelo;
                $bandera=true;
            }

            if ($longitud != null && $longitud!='')
            {
                $avion->longitud = $longitud;
                $bandera=true;
            }

            if ($capacidad != null && $capacidad!='')
            {
                $avion->capacidad = $capacidad;
                $bandera=true;
            }

            if ($velocidad != null && $velocidad!='')
            {
                $avion->velocidad = $velocidad;
                $bandera=true;
            }

            if ($alcance != null && $alcance!='')
            {
                $avion->alcance = $alcance;
                $bandera=true;
            }

            if ($bandera)
            {
                // Almacenamos en la base de datos el registro.
                $avion->save();
                return response()->json(['status'=>'ok','data'=>$avion], 200);
            }
            else
            {
                // Devolveremos un código 304 Not Modified – [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo
                // Este código 304 no devuelve ningún body, así que si quisiéramos que se mostrara el mensaje usaríamos un código 200 en su lugar.
                return response()->json(['code'=>304,'message'=>'No se ha modificado ningún dato de userdb.'],304);
            }

        }

        // Si el método no es PATCH entonces es PUT y tendremos que actualizar todos los datos.
        if (!$modelo || !$longitud || !$capacidad || !$velocidad || !$alcance)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para errores de validación.
            return response()->json(['errors'=>array(['code'=>422,'message'=>'Faltan valores para completar el procesamiento.'])],422);
        }

        $avion->modelo = $modelo;
        $avion->longitud = $longitud;
        $avion->capacidad = $capacidad;
        $avion->velocidad = $velocidad;
        $avion->alcance = $alcance;

        // Almacenamos en la base de datos el registro.
        $avion->save();

        return response()->json(['data'=>$avion], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($emailUserdb,$idAvion)
    {
        // Comprobamos si el userdb que nos están pasando existe o no.
        $userdb=userdb::find($emailUserdb);

        // Si no existe ese userdb devolvemos un error.
        if (!$userdb)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un userdb con ese código.'])],404);
        }       

        // El userdb existe entonces buscamos el avion que queremos borrar asociado a ese userdb.
        $avion = $userdb->customer()->find($idAvion);

        // Si no existe ese avión devolvemos un error.
        if (!$avion)
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 404.
            // En code podríamos indicar un código de error personalizado de nuestra aplicación si lo deseamos.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un avión con ese código asociado a ese userdb.'])],404);
        }

        // Procedemos por lo tanto a eliminar el avión.
        $avion->delete();

        // Se usa el código 204 No Content – [Sin Contenido] Respuesta a una petición exitosa que no devuelve un body (como una petición DELETE)
        // Este código 204 no devuelve body así que si queremos que se vea el mensaje tendríamos que usar un código de respuesta HTTP 200.
        return response()->json(['code'=>204,'message'=>'Se ha eliminado el avión correctamente.'],204);
    }

}