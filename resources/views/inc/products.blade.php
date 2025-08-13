<label class="form-label fw-semibold">Productos</label>
<div class="list-group">
@foreach ($products as $product)
        @php
            // Si existe el producto, busco la compañía que tenga ese producto con el company_id actual
            // luego accedo a la tabla pivot que tenga ese product_id y company_id y obtengo el precio
            // Si no existe product_id ni company_id devuelvo null
            $price = isset($company)
            ? $company->products->firstWhere('id', $product->id)?->companyProduct?->price
            : null
        @endphp
        <div class="list-group-item">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="form-check">
                        <label>
                            <input
                                type="checkbox"
                                class="form-check-input product-checkbox"
                                name="products[{!! $product->id !!}][__checked]"
                                id="product_{{ $product->id }}"
                                value="{!! $product->id !!}"
                                {{-- Verifica si el producto existe y si contiene esta empresa para marcarlo como seleccionado --}}
                                {{--{{ isset($product) && $product?->companies?->contains($company->id) ? 'checked' : '' }}--}}

                                {{-- Verifica si el producto existe y si contiene esta empresa para marcarlo como seleccionado --}}
                                @checked(isset($company) && $company?->products?->contains($product->id))
                            >
                            <label class="form-check-label ms-1" for="product-{{ $product->id }}">
                                {{ $product->name }}
                            </label>
                        </label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="input-group mb-2">
                        <label for="productprice-{{ $product->id }}"></label>
                        <input
                            class="form-control"
                            id="product_{{ $product->id }}_price"
                            type="number"
                            step="0.01"
                            min="0"
                            name="products[{{ $product->id }}][price]"
                            value="{{ $price ?? '' }}"
                            placeholder="0.00"
                            @disabled(!isset($price))
                        >
                        <span class="input-group-text">€</span>
                    </div>
                </div>
            </div>
        </div>
@endforeach
</div>

@section('scripts')
    @parent
    <script>
        $(document).ready(function () {
            $('.product-checkbox').on('change', function (evento) {
                const productId = $(this).attr('id');
                const checked = $(this).is(':checked');
                const $priceId = $('#' + productId + '_price')
                $priceId.prop('disabled', !checked);

            })
        });
    </script>
@endsection
