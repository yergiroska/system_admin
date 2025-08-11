@extends('layouts.app')

@section('content')
    <div>Cliente:{!! $customer->getFullName() !!}</div>
    <br/>
    <form action="{{ route('customers.buy', $customer->id) }}" id="form_customer" method="POST">
        @csrf
        @foreach($companies as $company)
            <div class="row">
                <div class="col-12">Compañía</div>
                <div class="col-12">{{ $company->getName() }}</div>
                    <div class="row">
                        <div class="col-12">Productos</div>
                        @foreach($company->products as $product)
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="products[]" value="{!! $product->companyProduct->getId() !!}">
                                    {{ $product->getName() }}
                                </label>
                            </div>
                        @endforeach
                    </div>
            </div>
        @endforeach
        <button type="submit" class="btn btn-primary">Comprar</button>
    </form>
@endsection
