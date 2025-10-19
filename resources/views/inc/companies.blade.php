<label class="form-label fw-semibold">Empresas</label>
<div class="list-group">
@foreach ($companies_products as $company_product)

    @php
        // Si existe el producto, busco la compañía que tenga ese producto con el company_id actual
        // luego accedo a la tabla pivot que tenga ese product_id y company_id y obtengo el precio
        // Si no existe product_id ni company_id devuelvo null
        $price = isset($pivot_company_product)
        ? $pivot_company_product->companiesProducts->firstWhere('id', $company_product->id)?->companyProduct?->price
        : null
    @endphp

    <div class="list-group-item">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <div class="form-check">
                    <label>
                        <input
                            type="checkbox"
                            class="form-check-input company-checkbox"
                            name="companies_products[{!! $company_product->id !!}][company_product_id]"
                            id="company_product{{ $company_product->id }}"
                            value="{!! $company_product->id !!}"
                            {{-- Verifica si el producto existe y si contiene esta empresa para marcarlo como seleccionado --}}
                            {{--                {{ isset($product) && $product?->companies?->contains($company->id) ? 'checked' : '' }}--}}

                            {{-- Verifica si el producto existe y si contiene esta empresa para marcarlo como seleccionado --}}
                            @checked(isset($pivot_company_product) && $pivot_company_product?->companiesProducts?->contains($company_product->id))
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
            $('.company-checkbox').on('change', function (evento) {
                const companyId = $(this).attr('id');
                console.log(companyId);
                const checked = $(this).is(':checked');
                const $priceId = $('#' + companyId + '_price')
                $priceId.prop('disabled', !checked);

            })
        });
    </script>
@endsection
