<label>Empresas</label>
@foreach ($companies as $company)
    <div class="checkbox">
        <label>
            <input
                type="checkbox"
                name="companies[]"
                value="{!! $company->id !!}"
                {{-- Verifica si el producto existe y si contiene esta empresa para marcarlo como seleccionado --}}
{{--                {{ isset($product) && $product?->companies?->contains($company->id) ? 'checked' : '' }}--}}

                {{-- Verifica si el producto existe y si contiene esta empresa para marcarlo como seleccionado --}}
                @checked(isset($product) && $product?->companies?->contains($company->id))
            >
            {{ $company->name }}
        </label>
    </div>
@endforeach
