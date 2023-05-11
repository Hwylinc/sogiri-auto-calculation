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
                    @foreach ($diameterDisplayList as $diameter => $id)
                        <a @if($id == $diameter_id) class="active" @endif href="{{ route('calculate.complete',['calculation_id' => $calculation_id , 'diameter_id' => $id ]) }}">
                            {{ $diameter }}<br>
                        </a>
                    @endforeach

                    <div class="mt-5">
                        @foreach ($resultDisplayList[$diameter_id] as $setTimes => $combination)
                            <h2 class="text-center">設置　{{ $setTimes }}回目</h2>
                            @if (!empty($combination['data']))
                            <table class="table">
                                <thead>
                                    <tr>
                                        <td>切断順番</td>
                                        <td>長さ</td>
                                        <td>切断本数</td>
                                        <td>吐き出し口</td>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($combination['data'] as $key => $value)
                                      <tr>
                                        <th scope="row">{{ $key }}</th>
                                        <td>{{  $value['length'] }} mm</td>
                                        <td>{{  $value['number'] }} 本</td>
                                        <td>{{  $value['port'] }}</td>
                                      </tr>
                                @endforeach
                                    </tbody>
                                  </table>
                                <p>端材：{{ $combination['left'] }}mm</p>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection