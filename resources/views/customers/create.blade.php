@extends('layouts.app')

@section('content')
    <h1>Create Customer</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('customers.store') }}" method="POST">
        @csrf

        <label>First Name:</label>
        <input type="text" name="first_name" value="{{ old('first_name') }}"><br>

        <label>Last Name:</label>
        <input type="text" name="last_name" value="{{ old('last_name') }}"><br>

        <label>Birth Date:</label>
        <input type="date" name="birth_date" value="{{ old('birth_date') }}"><br>

        <label>Identity Document:</label>
        <input type="text" name="identity_document" value="{{ old('identity_document') }}"><br>

        <button type="submit">Save</button>
    </form>
@endsection
