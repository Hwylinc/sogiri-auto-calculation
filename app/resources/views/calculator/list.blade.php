@extends('layouts.app')

@section('pageCss')
<style>
    .active {
        color: red;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    @if (!empty($caliculatedCodeList))
                        @foreach ($caliculatedCodeList as $caliculatedCode)
                            <a  href="{{ route('calculate.detail',['calculation_id' => $caliculatedCode ]) }}">
                                {{ $caliculatedCode }}<br>
                            </a>
                        @endforeach
                    @else
                        表示内容がありません
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection