@extends('layouts.app')
@section('content')
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-note-sticky"></i> Crear Nota</h4>
            </div>

            <div class="card-body">
                {{-- Mostrar errores --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Formulario --}}
                <form action="{{ route('notes.store') }}" id="form_note" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="title" class="form-label">Título:</label>
                        <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}">
                    </div>

                    <div class="mb-3">
                        <label for="contents" class="form-label">Contenido:</label>
                        <textarea id="contents" name="contents" class="form-control" rows="3" value="{{ old('contents') }}"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="completed" class="form-label">Completado:</label>
                        <!--<input type="checkbox" id="completed" name="completed" value="{{ old('completed') }}"><br>-->
                        <input type="hidden" name="completed" value="0"> <!-- Este se enviará siempre -->
                        <input type="checkbox" id="completed" name="completed" class="form-check-input" value="1"> <!-- Este solo si está seleccionado -->
                    </div>

                    <div class="d-grid">
                        <button type="submit" id="saved" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
                            alert('Registro exitoso');
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
