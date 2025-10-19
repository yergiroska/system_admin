<label class="form-label fw-semibold">Productos</label>
<div class="list-group">
@foreach ($companies_products as $company_product)
        @php
            // Calcula el precio del producto para una compañía específica
            // Si la compañía está definida:
            //   - Busca el producto dentro de la colección de productos de la compañía usando firstWhere()
            //   - Accede a la relación pivot (companyProduct) para obtener el precio
            // Si la compañía no está definida, devuelve null
            $price = isset($pivot_company_product)
            ? $pivot_company_product->companiesProducts->firstWhere('id', $company_product->id)?->companyProduct?->price
            : null;

            // Verifica si el producto está asociado a la compañía actual
            // Devuelve true si:
            //   - La compañía está definida (isset($company)) Y
            //   - El producto existe en la colección de productos de la compañía (contains())
            // En caso contrario devuelve false
            $checked = isset($pivot_company_product) && $pivot_company_product?->companiesProducts?->contains($company_product->id)
        @endphp
        <div class="list-group-item">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="form-check">
                        <label>
                            <input
                                type="checkbox"
                                class="form-check-input product-checkbox"
                                name="companies_products[{!! $company_product->id !!}][company_product_id]"
                                id="company_product{{ $company_product->id }}"
                                value="{!! $company_product->id !!}"
                                {{-- Verifica si el producto existe y si contiene esta empresa para marcarlo como seleccionado --}}
                                {{--{{ isset($product) && $product?->companies?->contains($company->id) ? 'checked' : '' }}--}}

                                {{-- Verifica si el producto existe y si contiene esta empresa para marcarlo como seleccionado --}}
                                @checked($checked)
                            >
                            <label class="form-check-label ms-1" for="product-{{ $company_product->id }}">
                                {{ $company_product->name }}
                            </label>
                        </label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="input-group mb-2">
                        <label for="company_product{{ $company_product->id }}_price"></label>
                        <input
                            class="form-control"
                            id="company_product{{ $company_product->id }}_price"
                            type="number"
                            step="0.01"
                            min="0"
                            name="companies_products[{{ $company_product->id }}][price]"
                            value="{{ $price ?? '' }}"
                            placeholder="0.00"
                            @disabled(!$checked)
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
