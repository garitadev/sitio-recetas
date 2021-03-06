@extends('layouts.app')


@section('content')

    {{-- <h1>{{$receta}}</h1> --}}
    <article class="contenido-receta">
        <h1 class="text-center mb-4">{{$receta->titulo}}</h1>

        <div class="imagen-receta">
            <img src="/storage/{{$receta->imagen}}" class="w-100" alt="">
        </div>

        <div class="receta-meta">
            <p>
                <span class="font-weight-bold text-primary">Escrito en:</span>
                {{$receta->categoria->nombre}}
            </p>

            <p>
                <span class="font-weight-bold text-primary">Fecha:</span>
                @php
                    $fecha = $receta->created_at
                @endphp

                <fecha-receta fecha="{{$fecha}}"></fecha-receta>

            </p>


            <p>
                <span class="font-weight-bold text-primary">Autor:</span>
                {{$receta->autor->name}}
            </p>

            <div class="ingredientes">
                <h2 class="my-3 text-primary">Ingredientes</h2>
                {{-- De esta manera toma el contenido y crea las etiquetas html --}}
                {!! $receta->ingredientes !!}
            </div>

            <div class="preparacion">
                <h2 class="my-3 text-primary">Preparación</h2>
                {{-- De esta manera toma el contenido y crea las etiquetas html --}}
                {!! $receta->preparacion !!}
            </div>
        </div>

        <div class="justify-content-center row text-center">
            <like-button
            receta-id="{{$receta->id}}"
            like="{{$like}}"
            likes="{{$likes}}"
            ></like-button>

        </div>

        {{-- {{$likes}} --}}
        

    </article>
@endsection