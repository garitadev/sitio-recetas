@extends('layouts.app')

{{-- Dentro de las vistas laravel/blade incluye los "yield" que son espacios para insertar contenido
para insertar en esas secciones se hace así: --}}

@section('botones')
    <a href="{{route('recetas.create')}}" class="btn btn-primary mr-2 text-white">Crear Receta</a>
    

@endsection

@section('content')
    <h2 class="text-center mb-5">Administra tus recetas</h2>


    <div class="col-md-10 mx-auto bg-white p-3" >
        <table class="table">
            <thead class="bg-primary text-light">
                <tr>
                    <th scole="col" class="text-center">Titulo</th>
                    <th scole="col" class="text-center">Categoría</th>
                    <th scole="col" class="text-center">Acciones</th>
                </tr>

                <tbody>
                    @foreach($recetas as $receta)

                        <tr class="text-center">
                            <td>{{$receta->titulo}}</td>
                            <td>{{$receta->categoria->nombre}}</td>
                            <td>
                                <a href="" class="btn btn-danger mr-1">Eliminar</a>
                                <a href="" class="btn btn-dark mr-1">Editar</a>
                                <a href="" class="btn btn-success mr-1">Ver</a>
                            </td>
                        </tr>   
                    @endforeach
                    
                </tbody>

            </thead>
        </table>
    </div>

@endsection

