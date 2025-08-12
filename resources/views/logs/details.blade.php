@extends('layouts.app')

@section('content')
    <div class="container mt-5" style="max-width: 700px;">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h2>{!! $log->action !!}</h2>
            </div>

            @foreach($details as $key =>$detail)
                <div class="mb-3">{!! $key !!}:<b>
                        @if($key === 'created_at'  || $key === 'updated_at')
                            {!! \Carbon\Carbon::parse($detail)->format('d/m/Y H:i:s') !!}
                        @else
                            {!! $detail !!}
                        @endif

                    </b>
                </div>
            @endforeach
        </div>
    </div>
@endsection

