<?php

namespace App\Http\Controllers;

use App\Receta;
use App\CategoriaReceta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class RecetaController extends Controller
{
    //Con esto se crea la url protegida
    public function __construct(){
        $this->middleware('auth', ['except'=>'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       // $recetas = Auth::user()->recetas;

       $usuario = auth()->user();

       //auth()->user()->meGusta->dd();
    
       //Recetas con paginación
        $recetas = Receta::where('user_id', $usuario->id)->paginate(3);
        
        return view('recetas.index')
            ->with('recetas', $recetas)
            ->with('usuario', $usuario);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //DB::table('categoria_receta')->get()->pluck('nombre','id')->dd();
        //Obtener las categorias sin modelo
        //$categorias = DB::table('categoria_recetas')->get()->pluck('nombre','id');

        //Con modelo ||  si no defines en el modelo la tabla que vas a usar laravel por defecto lo asignará automáticamente. Usando la palabra de tu modelo en plural
        $categorias = CategoriaReceta::all(['id', 'nombre']);

        return view('recetas.create')->with('categorias', $categorias);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //dd($request['imagen']->store('upload-recetas', 'public'));
        //ademas de agregar la condicion hay que inicar al usuario cuál es el error en la vista
        //VALIDACION
        $data = request()->validate([
            'titulo' => 'required|min:6',
            'preparacion'=> 'required',
            'ingredientes'=> 'required',
            'imagen'=> 'required|image',
            'categoria' =>'required' 
        ]);

        //Obtener la ruta de la imagen
        $ruta_imagen = $request['imagen']->store('upload-recetas', 'public');

        //Resize de la imagen
        $img = Image::make(public_path("storage/{$ruta_imagen}"))->fit(1000,550);
        $img->save();


        //Almacenar en la bd (sin modelo)
        // DB::table('recetas')->insert([
        //     'titulo' => $data['titulo'],
        //     'preparacion' => $data['preparacion'] ,
        //     'ingredientes' => $data['ingredientes'] ,
        //     'imagen' => $ruta_imagen,
        //     'user_id' => Auth::user()->id,
        //     'categoria_id' => $data['categoria'],

        // ]);
        
        
        //Almacenar en la bd con modelo
        auth()->user()->recetas()->create([

            'titulo' => $data['titulo'],
            'preparacion' => $data['preparacion'] ,
            'ingredientes' => $data['ingredientes'] ,
            'imagen' => $ruta_imagen,
            'categoria_id' => $data['categoria'],

        ]);

        //Rediccionar
        return redirect() -> action('RecetaController@index');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Receta  $receta
     * @return \Illuminate\Http\Response
     */
    public function show(Receta $receta)
    {
        //Obtener si el usuario actual le gusta le receta y está autenticado
        $like = (auth()->user()) ? auth()->user()->meGusta->contains($receta->id) :false;

        


        //Pasa la cantidad de likes a la vista | Count dice cuantos elementos hay en un arreglo
        $likes = $receta->likes->count();


        return view('recetas.show',compact('receta', 'like', 'likes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Receta  $receta
     * @return \Illuminate\Http\Response
     */
    public function edit(Receta $receta)
    {
        //Revisar el Policy de view
        $this->authorize('view', $receta);
        
        $categorias = CategoriaReceta::all(['id', 'nombre']);
        return view('recetas.edit', compact('categorias', 'receta'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Receta  $receta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Receta $receta)
    {

        //Revisar el policy
        $this->authorize('update', $receta);
        

        $data = request()->validate([
            'titulo' => 'required|min:6',
            'preparacion'=> 'required',
            'ingredientes'=> 'required',
            'imagen'=> 'image',
            'categoria' =>'required' 
        ]);

        //Asignar los valores
        $receta->titulo = $data['titulo'];
        $receta->preparacion = $data['preparacion'];
        $receta->ingredientes = $data['ingredientes'];
        $receta->categoria_id = $data['categoria'];
        //aquí debeido a que dentro del blade el name es categoria se pone así

        //Si el usuario sube una nueva imagen
        if(request('imagen')){
            //Obtener ruta de la imagen
            $ruta_imagen = $request['imagen']->store('upload-recetas', 'public');

             //Resize de la imagen
            $img = Image::make(public_path("storage/{$ruta_imagen}"))->fit(1000,550);
            $img->save();

            //Asignar al objeto
            $receta->imagen = $ruta_imagen;

        }

        $receta->save();
        //return $receta;

        //Redireccionar
        return redirect()->action('RecetaController@index');

       

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Receta  $receta
     * @return \Illuminate\Http\Response
     */
    public function destroy(Receta $receta)
    {

        //Ejecutar el Policy
        $this->authorize('delete', $receta);

        //Eliminar la receta
        $receta->delete();

        return redirect()->action('RecetaController@index');
    }
}
