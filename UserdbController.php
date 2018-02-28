<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

// Cargamos userdb por que lo usamos más abajo.
use App\Userdb;

use Response;

// Activamos el uso de las funciones de caché.
use Illuminate\Support\Facades\Cache;

class UserdbController extends Controller {

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
        // return "En el index de userdb.";
        // Devolvemos un JSON con todos los userdbs.
        // return userdb::all();

        // Caché se actualizará con nuevos datos cada 15 segundos.
        // cacheuserdbs es la clave con la que se almacenarán 
        // los registros obtenidos de userdb::all()
        // El segundo parámetro son los minutos.
        $userdbs=Cache::remember('cacheUserdbs',15/60,function()
        {
            // Para la paginación en Laravel se usa "Paginator"
            // En lugar de devolver 
            // return userdb::all();
            // devolveremos return userdb::paginate();
            // 
            // Este método paginate() está orientado a interfaces gráficas. 
            // Paginator tiene un método llamado render() que permite construir
            // los enlaces a página siguiente, anterior, etc..
            // Para la API RESTFUL usaremos un método más sencillo llamado simplePaginate() que
            // aporta la misma funcionalidad
            return Userdb::simplePaginate(1);  // Paginamos cada 1 elemento.

        });

        // Para devolver un JSON con código de respuesta HTTP sin caché.
        // return response()->json(['status'=>'ok', 'data'=>userdb::all()],200);

        // Devolvemos el JSON usando caché.
        // return response()->json(['status'=>'ok', 'data'=>$userdbs],200);
         
        // Con la paginación lo haremos de la siguiente forma:
        // Devolviendo también la URL a l
        return response()->json(['status'=>'ok', 'siguiente'=>$userdbs->nextPageUrl(),'anterior'=>$userdb->previousPageUrl(),'data'=>$userdb->items()],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    // No se utiliza este método por que se usaría para mostrar un formulario
    // de creación de userdb. Y una API REST no hace eso.
    /*
    public function create()
    {
        //
    }
    */

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        // Método llamado al hacer un POST.
        // Comprobamos que recibimos todos los campos.
        if (!$request->input('email') || !$request->input('password') || !$request->input('dni_customer'))
        {
            // NO estamos recibiendo los campos necesarios. Devolvemos error.
            return response()->json(['errors'=>array(['code'=>422,'message'=>'Faltan datos necesarios para procesar el alta.'])],422);
        }

        // Insertamos los datos recibidos en la tabla.
        $nuevoUserdb=Userdb::create($request->all());

        // Devolvemos la respuesta Http 201 (Created) + los datos del nuevo userdb + una cabecera de Location + cabecera JSON
        $respuesta= Response::make(json_encode(['data'=>$nuevoUserdb]),201)->header('Location','http://www.dominio.local/userdb/'.$nuevoUserdb->email)->header('Content-Type','application/json');
        return $respuesta;
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $email
     * @return Response
     */
    public function show($email)
    {
        // Corresponde con la ruta /userdb/{userdb}
        // Buscamos un userdb por el email.
        $userdb=Userdb::find($email);

        // Chequeamos si encontró o no el userdb
        if (! $userdb)
        {
            // Se devuelve un array errors con los errores detectados y código 404
            return response()->json(['errors'=>Array(['code'=>404,'message'=>'No se encuentra un userdb con ese código.'])],404);
        }

        // Devolvemos la información encontrada.
        return response()->json(['status'=>'ok','data'=>$userdb],200);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $email
     * @return Response
     */
    /*
    public function edit($email)
    {
        //
    }
    */

    /**
     * Update the specified resource in storage.
     *
     * @param  string  $email
     * @return Response
     */
    public function update($email,Request $request)
    {
        // Vamos a actualizar un userdb.
        // Comprobamos si el userdb existe. En otro caso devolvemos error.
        $userdb=Userdb::find($email);

        // Si no existe mostramos error.
        if (! $userdb)
        {
            // Devolvemos error 404.
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra un userdb con ese código.'])],404);
        }

        // Almacenamos en variables para facilitar el uso, los campos recibidos.
        $email=$request->input('email');
        $password=$request->input('password');
        $dni_customer=$request->input('dni_customer');

        // Comprobamos si recibimos petición PATCH(parcial) o PUT (Total)
        if ($request->method()=='PATCH')
        {
            $bandera=false;

            // Actualización parcial de datos.
            if ($email !=null && $email!='')
            {
                $userdb->email=$email;
                $bandera=true;
            }

            // Actualización parcial de datos.
            if ($password !=null && $password!='')
            {
                $userdb->password=$password;
                $bandera=true;
            }

            // Actualización parcial de datos.
            if ($dni_customer !=null && $dni_customer!='')
            {
                $userdb->dni_customer=$dni_customer;
                $bandera=true;
            }

            if ($bandera)
            {
                // Grabamos el userdb.
                $userdb->save();

                // Devolvemos un código 200.
                return response()->json(['status'=>'ok','data'=>$userdb],200);
            }
            else
            {
                // Devolvemos un código 304 Not Modified.
                return response()->json(['errors'=>array(['code'=>304,'message'=>'No se ha modificado ningún dato del userdb.'])],304);
            }
        }


        // Método PUT actualizamos todos los campos.
        // Comprobamos que recibimos todos.
        if (!$email || !$password || !$dni_customer)
        {
            // Se devuelve código 422 Unprocessable Entity.
            return response()->json(['errors'=>array(['code'=>422,'message'=>'Faltan valores para completar el procesamiento.'])],422);
        }

        // Actualizamos los 3 campos:
        $userdb->email=$email;
        $userdb->password=$password;
        $userdb->dni_customer=$dni_customer;

        // Grabamos el userdb
        $userdb->save();
        return response()->json(['status'=>'ok','data'=>$userdb],200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $email
     * @return Response
     */
    public function destroy($email)
    {
        // Borrado de un userdb.
        // Ejemplo: /userdbs/89 por DELETE
        // Comprobamos si el userdb existe o no.
        $userdb=Userdb::find($email);

        if (! $userdb)
        {
            // Devolvemos error codigo http 404
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se encuentra el userdb con ese código.'])],404);
        }

        // Borramos el userdb y devolvemos código 204
        // 204 significa "No Content".
        // Este código no muestra texto en el body.
        // Si quisiéramos ver el mensaje devolveríamos
        // un código 200.
        // Antes de borrarlo comprobamos si tiene customer y si es así
        // sacamos un mensaje de error.
        // $customer = $userdb->customer()->get();
        $customer = $userdb->customer;

        if (sizeof($customer) >0)
        {
            // Si quisiéramos borrar todos los customer del userdb sería:
            // $userdb->customer->delete();

            // Devolvemos un código 409 Conflict. 
            return response()->json(['errors'=>array(['code'=>409,'message'=>'Este userdb posee customer y no puede ser eliminado.'])],409);
        }

        // Eliminamos el userdb si no tiene customer.
        $userdb->delete();

        // Se devuelve código 204 No Content.
        return response()->json(['code'=>204,'message'=>'Se ha eliminado correctamente el userdb.'],204);
    }

}