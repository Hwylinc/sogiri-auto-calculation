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
                    <div class="mt-5">
                        <p class="text-center mb-5">計算が完了いたしました</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('home') }}" class="btn rounded-pill btn-secondary">トップへ戻る</a>
                            <a href="{{ route('calculate.detail', ['group_code' => $group_code]) }}" class="btn rounded-pill btn-primary">計算結果を確認</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection