@extends('layouts.app')

@section('content')
    <h1>Product List</h1>
    <a href="{{ route('products.create') }}">Create New</a>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($products as $product)
            <tr id="{!! $product->id !!}">
                <td>{{ $product->name }}</td>
                <td> {{ $product->description }}</td>
                <td>
                    <a href="{{ route('products.edit', $product) }}">Edit</a>
                    <form id="form_product" action="{{ route('products.destroy', $product) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button data-id="{!! $product->id !!}" id="delete" >Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('#delete').on('click', function(evento) {
                evento.preventDefault();
                let id = $(this).attr('data-id')

                if(confirm('Are you sure?')) {
                    $.ajax({
                        url: $('#form_product').attr('action'),
                        method: $('#form_product').attr('method'),
                        data: $('#form_product').serialize(),
                        success: function(response) {
                            if(response.status === 'success'){
                                alert(response.message);
                                $('tr#'+id).remove()
                            }
                        },
                        error: function(xhr) {
                            // Manejar errores
                        }
                    });
                }

                /**/

            })
        });
    </script>
@endsection


