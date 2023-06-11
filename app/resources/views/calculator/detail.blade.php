@extends('layouts.app')

@section('pageCss')
<style>
    .active_diameter {
        color: red;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <a href="{{ route('calculate.list') }}" type="button" class="btn btn-dark rounded-pill ">一覧へ戻る</a>
            <a href="{{ route('home') }}" type="button" class="btn btn-dark rounded-pill ">トップへ戻る</a>

            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                          <a data-bs-toggle="tab" href="#result"  @if($page_tab=='result') class="nav-link active" @else class="nav-link" @endif>切断指示</a>
                        </li>
                        <li class="nav-item">
                          <a data-bs-toggle="tab" href="#request" @if($page_tab=='request') class="nav-link active" @else class="nav-link" @endif>計算依頼内容</a>
                        </li>
                        <li class="nav-item">
                          <a data-bs-toggle="tab" href="#exception" @if($page_tab=='exception') class="nav-link active" @else class="nav-link" @endif>例外処理内容</a>
                        </li>
                    </ul>
                    
                    <div class="tab-content">
                        {{-- 切断指示　Start --}}
                        <div id="result" @if($page_tab=='result') class="tab-pane active" @else class="tab-pane" @endif>
                            @foreach ($diameterDisplayList as $diameter => $id)
                                <a @if($id == $diameter_id) class="active_diameter" @endif href="{{ route('calculate.detail',['group_code' => $group_code, 'page_tab' => 'result', 'calculation_id' => $calculation_id, 'diameter_id' => $id]) }}">
                                    {{ $diameter }}<br>
                                </a>
                            @endforeach
                            <div class="mt-5">
                                @if (!empty($resultDisplayList[$diameter_id])) 
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
                                @else
                                    表示内容がありません
                                @endif
                            </div>
                        </div>
                        {{-- 切断指示　End --}}
                        {{-- 依頼内容　Start --}}
                        <div id="request" @if($page_tab=='request') class="tab-pane active" @else class="tab-pane" @endif>
                            @foreach ($diameterDisplayList as $diameter => $id)
                                <a @if($id == $diameter_id) class="active_diameter" @endif href="{{ route('calculate.detail',['group_code' => $group_code, 'page_tab' => 'request', 'calculation_id' => $calculation_id, 'diameter_id' => $id]) }}">
                                    {{ $diameter }}<br>
                                </a>
                            @endforeach
                            @if (!empty($calculationRequestCodeList)) 
                                <select id="calculation_id">
                                    @foreach ($calculationRequestCodeList as $value)
                                        <option @if($value['code'] == $calculation_id) selected @endif value="{{ $value['code'] }}">{{ $value['name'] }} {{ $value['house_name'] }}</option>
                                    @endforeach
                                </select>
                            @endif
                            @if (isset($calculationRequestDisplayList[$calculation_id][$diameter_id]))
                                @foreach ($calculationRequestDisplayList[$calculation_id][$diameter_id] as $compornent_id => $detailArray)
                                    @if (!empty($detailArray))
                                    <?php $disp_no = 1;?>
                                    <h3 class="text-center">部材　{{ $compornent_id }}</h3>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <td>No</td>
                                                    <td>寸法</td>
                                                    <td>切断本数</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($detailArray as $value)
                                                    <tr>
                                                        <th scope="row">{{ $disp_no }}</th>
                                                        <td>{{  $value['length'] }} mm</td>
                                                        <td>{{  $value['number'] }} 本</td>
                                                        <td>{{  $value['port'] }}</td>
                                                    </tr>
                                                    <?php $disp_no++;?>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        {{-- 依頼内容　End --}}


                        {{-- 例外処理内容　Start --}}
                        <div id="exception" @if($page_tab=='exception') class="tab-pane active" @else class="tab-pane" @endif>
                            @foreach ($diameterDisplayList as $diameter => $id)
                                <a @if($id == $diameter_id) class="active_diameter" @endif href="{{ route('calculate.detail',['group_code' => $group_code, 'page_tab' => 'exception', 'calculation_id' => $calculation_id, 'diameter_id' => $id]) }}">
                                    {{ $diameter }}<br>
                                </a>
                            @endforeach
                            @if (isset($exceptionDisplayList[$diameter_id]))
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
                                <?php $count=1;?>
                                @foreach ($exceptionDisplayList[$diameter_id] as $value)
                                    <tr>
                                        <th scope="row">{{ $count }}</th>
                                        <td>{{  $value['length'] }} mm</td>
                                        <td>{{  $value['number'] }} 本</td>
                                        <td>{{  $value['port'] }}</td>
                                    </tr>
                                <?php $count++;?>
                                @endforeach
                                    </tbody>
                            </table>
                            @else
                                <p>表示内容がありません。</p>
                            @endif
                        </div>
                        {{-- 例外処理内容　End --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pageJs')
<script type="text/javascript">
	$(function(){
        $("#calculation_id").on('change', function() {
            // selectボックスの値を取得
            var group_code = "{{ $group_code }}";
            var calculation_id = $("#calculation_id").val();
            window.location.href = "/calculate/detail/"+group_code+"/request/"+calculation_id;
        });
    });
</script>
@endsection