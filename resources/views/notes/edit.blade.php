@extends('layouts.app')

@section('content')
    <h1>Editar Nota</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('notes.update', $note) }}" id="form_note" method="POST">
        @csrf
        @method('PUT')

        <label>Título:</label>
        <input type="text" name="title" value="{{ old('title', $note->title) }}"><br>

        <label>Contenido:</label>
        <input type="text" name="contents" value="{{ old('contents', $note->contents) }}"><br>

        <label>Completado:</label>
        <!--<input type="checkbox" name="completed" value="{{ old('completed', $note->completed) }}"><br>-->
        <input type="hidden" name="completed" value="0"> <!-- Se envía siempre -->
        <input type="checkbox" name="completed" value="1" {{ old('completed', $note->completed) ? 'checked' : '' }}>

        <button type="submit" id="update">Actualizar</button>
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#update').on('click', function(evento) {
                evento.preventDefault();
                $.ajax({
                    url: $('#form_note').attr('action'),
                    method: $('#form_note').attr('method'),
                    data: $('#form_note').serialize(),
                    success: function(response) {
                        if(response.status === 'success'){
                            alert('Actualizado con exito');
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
