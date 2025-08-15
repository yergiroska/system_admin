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
                    <div class="row">
                        @foreach($company->products as $product)
                            @php
                                $price = $product->companyProduct?->getPrice(); // Precio desde el pivote
                            @endphp
                            <div class="col-md-4 mb-2">
                                <div class="form-check border rounded p-2">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="products[]"
                                               value="{!! $product->companyProduct->getId() !!}">
                                        {{ $product->getName() }}
                                    </label>
                                    <span class="badge bg-light text-dark">
                                        {{ $price !== null ? (number_format((float)$price, 2, '.', ''). '€ ' ) : '-' }}
                                    </span>
                                </div>

                            </div>
                        @endforeach
                    </div>
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
