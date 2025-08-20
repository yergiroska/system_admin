<label class="form-label fw-semibold">Empresas</label>
<div class="list-group">
@foreach ($companies as $company)

    @php
        // Si existe el producto, busco la compañía que tenga ese producto con el company_id actual
        // luego accedo a la tabla pivot que tenga ese product_id y company_id y obtengo el precio
        // Si no existe product_id ni company_id devuelvo null
        $price = isset($product)
        ? $product->companies->firstWhere('id', $company->id)?->companyProduct?->price
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
                            name="companies[{!! $company->id !!}][company_id]"
                            id="company_{{ $company->id }}"
                            value="{!! $company->id !!}"
                            {{-- Verifica si el producto existe y si contiene esta empresa para marcarlo como seleccionado --}}
                            {{--                {{ isset($product) && $product?->companies?->contains($company->id) ? 'checked' : '' }}--}}

                            {{-- Verifica si el producto existe y si contiene esta empresa para marcarlo como seleccionado --}}
                            @checked(isset($product) && $product?->companies?->contains($company->id))
                        >
                        <label class="form-check-label ms-1" for="product-{{ $company->id }}">
                            {{ $company->name }}
                        </label>
                    </label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="input-group mb-2">
                    <label for="companyprice-{{ $company->id }}"></label>
                    <input
                        class="form-control"
                        id="company_{{ $company->id }}_price"
                        type="number"
                        step="0.01"
                        min="0"
                        name="companies[{{ $company->id }}][price]"
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
                const checked = $(this).is(':checked');
                const $priceId = $('#' + companyId + '_price')
                $priceId.prop('disabled', !checked);

            })
        });
    </script>
@endsection
