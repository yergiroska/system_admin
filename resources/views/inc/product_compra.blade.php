<form action="{{ route('customers.buy', $customer->id) }}" id="form_customer" method="POST">
    @csrf
    @foreach($companies as $company)
        @if($company->products->isNotEmpty())
            <div class="card mb-4 shadow-sm">
                {{--<div class="col-12">Compañía</div>--}}
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-building"></i> {{ $company->getName() }}
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="text-muted"><i class="fas fa-boxes"></i> Productos</h6>

                    @foreach($company->products as $product)
                        <div class="row">
                            @php
                                $price = $product->companyProduct?->getPrice(); // Precio desde el pivote
                                $id = $product->companyProduct?->getId(); // Precio desde el pivote
                            @endphp
                            <div class="col-md-4 mb-2">
                                <div class="form-check border rounded p-2">
                                    <label class="form-check-label">
                                        <input
                                            id="product_{!! $id !!}"
                                            type="checkbox"
                                            name="products[{!! $id !!}][id]"
                                            value="{!! $id !!}"
                                            data-id="product_{!! $id !!}"
                                            data-price="{{ $price ?? 0.00 }}"
                                            class="product-checkbox"
                                        >
                                        {{ $product->getName() }}
                                    </label>
                                    <span class="badge bg-light text-dark">
                                        {{ $price !== null ? (number_format((float)$price, 2, '.', ''). '€ ' ) : '-' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2 d-flex align-items-center">
                                <label for="first_name" class="form-label">Cantidad:</label>
                                <input
                                    type="text"
                                    id="product_{!! $id !!}_quantity"
                                    name="products[{!! $id !!}][quantity]"
                                    class="form-control p-2 product-quantity"
                                    value="0"
                                    disabled="disabled"
                                    data-id="product_{!! $id !!}"
                                    data-price="{{ $price ?? 0.00 }}"
                                >
                            </div>
                            <div class="col-md-4 mb-2 d-flex align-items-center">
                                <label for="first_name" class="form-label">Total:</label>
                                <input
                                    type="text"
                                    id="product_{!! $id !!}_total"
                                    name="products[{!! $id !!}][total]"
                                    class="form-control p-2"
                                    readonly="readonly"
                                >
                            </div>
                            <input
                                type="hidden"
                                id="product_{!! $id !!}_price"
                                name="products[{!! $id !!}][price]"
                                value="{{ $price ?? 0.00 }}"
                            >
                        </div>
                    @endforeach

                </div>
            </div>
        @endif
    @endforeach
    {{-- Botón de comprar --}}
    <div class="d-grid">
        <button type="submit" class="btn btn-success btn-lg">
            <i class="fas fa-shopping-cart"></i> Comprar
        </button>
    </div>
</form>
@section('scripts')
    @parent
    <script>
        $(document).ready(function () {
            /**
             * Evento que se activa cuando se cambia el estado de un checkbox de producto.
             *
             * Este código maneja la interacción del usuario al seleccionar/deseleccionar productos:
             * - Obtiene el ID del producto del atributo data-id del checkbox
             * - Referencia el campo de cantidad asociado al producto
             * - Verifica si el checkbox está marcado o desmarcado
             * - Si se desmarca el checkbox:
             *   - Deshabilita el campo de cantidad
             *   - Establece la cantidad en 0
             *   - Establece el total en 0
             * - Si se marca el checkbox:
             *   - Habilita el campo de cantidad para edición
             */
            $('.product-checkbox').on('change', function (evento) {
                const productId = $(this).data('id');
                const $quantityId = $('#' + productId + '_quantity')
                const checked = $(this).is(':checked');

                $quantityId.prop('disabled', !checked).val(0);
                $('#' + productId + '_total').val(0)
            })

            /**
             * Evento que se activa cuando se cambia el valor del campo de cantidad de un producto.
             *
             * Este código calcula automáticamente el total al modificar la cantidad:
             * - Obtiene el ID del producto desde el atributo data-id del campo
             * - Obtiene el precio unitario desde el atributo data-price
             * - Obtiene la cantidad actual del campo de cantidad
             * - Calcula el total multiplicando precio por cantidad
             * - Actualiza el campo de total con el resultado
             */
            $('.product-quantity').on('change', function (evento) {
                const productId = $(this).data('id');
                const price = $(this).data('price');
                const $quantityId = $('#' + productId + '_quantity')
                const quantity = $quantityId.val()

                const total = price * quantity;
                $('#' + productId + '_total').val(total)
            })
        });
    </script>
@endsection
