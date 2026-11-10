@extends('layouts.app')

@section('titulo', 'Agregar Libro - Biblioteca Ciencias del Desarrollo - UPEA')

@section('navbar')
    @include('partials.navbar_admin')
@endsection

@section('contenido')
    <div class="contenedor">
        <h1>Agregar Nuevo Libro</h1>
        <div class="tarjeta">
            @include('admin.libros._form')
        </div>
    </div>
@endsection