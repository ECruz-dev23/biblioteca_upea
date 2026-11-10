@extends('layouts.app')

@section('titulo', 'Editar Libro - Biblioteca Ciencias del Desarrollo - UPEA')

@section('navbar')
    @include('partials.navbar_admin')
@endsection

@section('contenido')
    <div class="contenedor">
        <h1>Editar Libro</h1>
        <div class="tarjeta">
            @include('admin.libros._form')
        </div>
    </div>
@endsection