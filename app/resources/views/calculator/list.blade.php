@extends('layouts.app')

@section('pageCss')
<style>
    a {
        color:inherit;
        text-decoration: none;
    }
    border-top {
        width: 100%;
        border-top: thin 2px red;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    @if (!empty($calculationGroupCodes))
                        <div class="row">
                            <div class="col-3">日時</div>
                            <div class="col-3">メーカー</div>
                            <div class="col-3">邸名</div>
                            <div class="col-3">登録番号</div>
                        </div>
                        @foreach ($calculationGroupCodes as $group_code => $caliculatedInfo)
                            @if (!empty($caliculatedInfo))
                                <a href="{{ route('calculate.detail', ['group_code' => $group_code]) }}">
                                    <div class="border-top">
                                    @foreach ($caliculatedInfo as $disp_group_id => $codes)
                                        @if (!empty($codes))
                                            @foreach ($codes as $code)
                                                <div class="row">
                                                    <div class="col-3">{{ date( "Y/m/d", strtotime($code['created'])) }}</div>
                                                    <div class="col-3">{{ $code['name'] }}</div>
                                                    <div class="col-3">{{ $code['house_name'] }}</div>
                                                    <div class="col-3">{{ $code['group_id'] }}</div>
                                                </div>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </div>
                                </a>
                            @endif
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


@section('pageJs')
<script type="text/javascript">
	$(function(){
        alert('tedst');
    });
</script>
@endsection