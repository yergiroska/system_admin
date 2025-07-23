@extends('layouts.app')

@section('content')
<h1>Customer List</h1>
<a href="{{ route('customers.create') }}">Create New</a>

@if (session('success'))
    <p>{{ session('success') }}</p>
@endif

<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Last Name</th>
        <th>Birth Date</th>
        <th>ID</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($customers as $customer)
        <tr>
            <td>{{ $customer->first_name }}</td>
            <td>{{ $customer->last_name }}</td>
            <td>{{ $customer->birth_date }}</td>
            <td>{{ $customer->identity_document }}</td>
            <td>
                <a href="{{ route('customers.edit', $customer) }}">Edit</a>
                <form action="{{ route('customers.destroy', $customer) }}" method="POST" style="display:inline;">
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

