@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        {{-- Encabezado --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">
                <i class="fas fa-shopping-cart"></i> Comprar productos
            </h2>
        </div>

        {{-- Cliente --}}
        <div class="alert alert-info d-flex align-items-center" role="alert">
            <h5 class="mb-0">
                <i class="fas fa-user"></i> Cliente:
                <strong>{{ $customer->getFullNameAttribute() }}</strong>
            </h5>
        </div>

        {{-- Mensajes de error --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Mensaje de éxito --}}
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        {{-- Formulario de compra --}}
        @include('inc.product_compra')
        {{-- FIN Formulario de compra --}}
    </div>
@endsection
