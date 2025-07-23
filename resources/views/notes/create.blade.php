@extends('layouts.app')

@section('content')
    <h1>Create Note</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('notes.store') }}" id="form_note" method="POST">
        @csrf

        <label>Title:</label>
        <input type="text" id="title" name="title" value="{{ old('title') }}"><br>

        <label>Content:</label>
        <input type="text" id="contents" name="contents" value="{{ old('contents') }}"><br>

        <label>Completed:</label>
        <!--<input type="checkbox" id="completed" name="completed" value="{{ old('completed') }}"><br>-->
        <input type="hidden" name="completed" value="0"> <!-- Este se enviará siempre -->
        <input type="checkbox" id="completed" name="completed" value="1"> <!-- Este solo si está seleccionado -->

        <button type="submit" id="saved">Save</button>
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#saved').on('click', function(evento) {
                evento.preventDefault();
                $.ajax({
                    url: $('#form_note').attr('action'),
                    method: $('#form_note').attr('method'),
                    data: $('#form_note').serialize(),
                    success: function(response) {
                        if(response.status === 'success'){
                            alert('Registro existoso');
                        }
                    },
                    error: function(xhr) {
                        // Manejar errores
                    }
                });

            })
        });
    </script>
@endsection
