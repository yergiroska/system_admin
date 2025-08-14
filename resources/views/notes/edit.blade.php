@extends('layouts.app')
@section('content')
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="fas fa-edit"></i> Editar Nota</h4>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('notes.update', $note) }}" id="form_note" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="title" class="form-label">Título:</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $note->title) }}">
                    </div>

                    <div class="mb-3">
                        <label for="contents" class="form-label">Contenido:</label>
                        <textarea name="contents" id="contents" class="form-control" rows="4">{{ old('contents', $note->contents) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="completed" class="form-label">Completado:</label>
                        <!--<input type="checkbox" name="completed" value="{{ old('completed', $note->completed) }}"><br>-->
                        <input type="hidden" name="completed" value="0"> <!-- Se envía siempre -->
                        <input type="checkbox" name="completed" class="form-check-input" value="1" {{ old('completed', $note->completed) ? 'checked' : '' }}>
                    </div>

                    <div class="d-grid">
                        <button type="submit" id="update" class="btn btn-success text-white">
                            <i class="fas fa-save"></i> Actualizar
                        </button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>
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
