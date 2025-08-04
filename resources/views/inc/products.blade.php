<label>Productos</label>
@foreach ($products as $product)
    <div class="checkbox">
        <label>
            <input
                type="checkbox"
                name="products[]"
                value="{!! $product->id !!}"
                {{-- Verifica si el producto existe y si contiene esta empresa para marcarlo como seleccionado --}}
{{--                {{ isset($product) && $product?->companies?->contains($company->id) ? 'checked' : '' }}--}}

                {{-- Verifica si el producto existe y si contiene esta empresa para marcarlo como seleccionado --}}
                @checked(isset($company) && $company?->products?->contains($product->id))
            >
            {{ $product->name }}
        </label>
    </div>
@endforeach
