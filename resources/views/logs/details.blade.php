@extends('layouts.app')

@section('content')
    <h2>{!! $log->action !!}</h2>
    @foreach($details as $key =>$detail)
        <div>{!! $key !!}:<b>
                @if($key === 'created_at'  || $key === 'updated_at')
                    {!! \Carbon\Carbon::parse($detail)->format('d/m/Y H:i:s') !!}
                @else
                    {!! $detail !!}
                @endif

            </b></div>
    @endforeach
@endsection

