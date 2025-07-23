@extends('layouts.app')

@section('content')
    <h1>Edit Note</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('notes.update', $note) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Title:</label>
        <input type="text" name="title" value="{{ old('title', $note->title) }}"><br>

        <label>Content:</label>
        <input type="text" name="contents" value="{{ old('contents', $note->contents) }}"><br>

        <label>Completed:</label>
        <!--<input type="checkbox" name="completed" value="{{ old('completed', $note->completed) }}"><br>-->
        <input type="hidden" name="completed" value="0"> <!-- Se envía siempre -->
        <input type="checkbox" name="completed" value="1" {{ old('completed', $note->completed) ? 'checked' : '' }}>

        <button type="submit">Update</button>
    </form>
@endsection
