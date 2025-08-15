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
                <strong>{{ $customer->getFullName() }}</strong>
            </h5>
        </div>

        {{-- Formulario de compra --}}
        @include('inc.product_compra')
        {{-- FIN Formulario de compra --}}
    </div>
@endsection
