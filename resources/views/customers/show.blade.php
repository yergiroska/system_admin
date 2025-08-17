@extends('layouts.app')

@section('content')
    <div class="container mt-5" style="max-width: 700px;">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">
                    <i class="fas fa-building"></i> Detalle del Cliente
                </h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p class="mb-1"><strong>ID:</strong> {{ $customer->getId() }}</p>
                </div>
                <div class="mb-3">
                    <p class="mb-1"><strong>Nombre:</strong> {{ $customer->getFullName() }}</p>
                </div>

                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Compañía</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                        <th>Fecha</th>
                    </tr>
                    </thead>
                    <tbody>
                    {{-- Itera sobre las compras del cliente. Si no hay compras, se ejecutará @empty --}}
                    @forelse($customer->purchases as $purchase)
                        @php
                            // Obtiene el producto de la compañía asociado a esta compra
                            $company_product = $purchase->companyProduct;
                            // Obtiene la compañía del producto
                            $company = $company_product->company;
                            // Obtiene el producto
                            $product = $company_product->product;
                            // Obtiene el precio
                            $unit_price = $purchase->getUnitPrice() ;
                            $quantity = $purchase->getQuantity() ;
                            $total = $purchase->getTotal() ;
                        @endphp
                        <tr>
                            {{-- Muestra el nombre de la compañía o un guión si es null --}}
                            <td>{{ $company?->getName() }}</td>
                            {{-- Muestra el nombre del producto o un guión si es null --}}
                            <td>{{ $product?->getName()}}</td>
                            <td>
                                {{-- Si hay precio, lo formatea con 2 decimales y añade el símbolo €. Si no, muestra un guión --}}
                                {{ number_format((float)$unit_price, 2, ',', '') }} €
                            </td>
                            <td>
                                {{ $quantity }}
                            </td>
                            <td>
                                {{-- Si hay precio, lo formatea con 2 decimales y añade el símbolo €. Si no, muestra un guión --}}
                                {{ number_format((float)$total, 2, ',', '') }} €
                            </td>
                            <td>
                                {{ $purchase->getCreatedAt() }}
                            </td>
                        </tr>
                    @empty
                        {{-- Se muestra cuando el cliente no tiene compras --}}
                        <tr>
                            <td colspan="3" class="text-center">Sin compras</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>Volver a la lista</a>
                </div>
            </div>
        </div>
    </div>
@endsection
