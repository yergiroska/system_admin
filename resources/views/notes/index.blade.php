@extends('layouts.app')

@section('content')
    <h1>Note List</h1>
    <a href="{{ route('notes.create') }}">Create New</a> |
    <a href="{{ route('notes.view.notes') }}">Note List</a>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <table>
        <thead>
        <tr>
            <th>Title</th>
            <th>Content</th>
            <th>Completed</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($notes as $note)
            <tr>
                <td>{{ $note->title }}</td>
                <td>{{ $note->contents }}</td>
                <td>{{ $note->completed ? 'Yes' : 'No' }}</td>
                <td>
                    <a href="{{ route('notes.edit', $note) }}">Edit</a>
                    <form action="{{ route('notes.destroy', $note) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

