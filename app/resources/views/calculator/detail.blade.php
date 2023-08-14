
<x-menu select-page="1">

    {{-- title --}}
    <div class="title-head flex print-off">
        <x-head title="鉄筋切断指示" imageFlg="1" horizon="0"></x-head>
        <a href="{{ route('calculate.list') }}" type="button" class="btn btn-dark rounded-pill ">一覧へ戻る</a>
        <a href="{{ route('rebar.select') }}" type="button" class="btn btn-dark rounded-pill ">トップへ戻る</a>
    </div>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs print-off">
                        <li class="nav-item nav-item1">
                          <a data-bs-toggle="tab" href="{{ route('calculate.detail', ['group_code' => $group_code, 'page_tab' => 'result']) }}"  @if($page_tab=='result') class="nav-link active" @else class="nav-link" @endif>切断指示</a>
                        </li>
                        <li class="nav-item nav-item2">
                          <a data-bs-toggle="tab" href="{{ route('calculate.detail', ['group_code' => $group_code, 'page_tab' => 'request']) }}" @if($page_tab=='request') class="nav-link active" @else class="nav-link" @endif>計算依頼内容</a>
                        </li>
                        <li class="nav-item nav-item3">
                          <a data-bs-toggle="tab" href="{{ route('calculate.detail', ['group_code' => $group_code, 'page_tab' => 'exception']) }}" @if($page_tab=='exception') class="nav-link active" @else class="nav-link" @endif>例外処理内容</a>
                        </li>
                    </ul>
                    
                    <div class="
                        tab-content
                        @if($page_tab == 'request') bk-request @endif
                        @if($page_tab == 'exception') bk-exeption  @endif
                    ">
                        {{-- 印刷時に使用する要素 --}}
                        <div class="print_header">
                            @if( $page_tab=='result' )
                                <div>切断指示</div>
                            @endif
                            @if( $page_tab=='request' )
                                <div>計算依頼内容</div>
                            @endif
                            @if( $page_tab=='exception' )
                                <div>例外処理内容</div>
                            @endif
                        </div>
                        {{-- 切断指示　Start --}}
                        <div id="result" @if($page_tab!='result') class="tab-pane hidden" @else class="tab-pane" @endif>
                            <div class="rebar-select-frame">
                                <label class="mr-2 font-semibold">現在の鉄筋径</label>
                                @foreach ($diameterDisplayList as $diameter => $id)
                                @if($diameter == 'D10' || $diameter == 'D13' || $diameter == 'D16')
                                    <a 
                                        class="
                                            rebar-button
                                            @if($id == $diameter_id) diameter-select @else print-off @endif
                                            mr-2
                                        "
                                         href="{{ route('calculate.detail',['group_code' => $group_code, 'page_tab' => 'result', 'calculation_id' => $calculation_id, 'diameter_id' => $id]) }}">
                                        {{ $diameter }}<br>
                                    </a>
                                @endif
                                @endforeach
                            </div>
                            <div class="mt-5 print-space">
                                @if (!empty($resultDisplayList[$diameter_id])) 
                                    @foreach ($resultDisplayList[$diameter_id] as $setTimes => $combination)
                                        <div class="time-detail">
                                            <h2 class="time-title">{{ $setTimes }}回目</h2>
                                            @if (!empty($combination['data']))
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td class="left print-size">切断順番</td>
                                                        <td>長さ</td>
                                                        <td>切断本数</td>
                                                        <td>吐き出し口</td>
                                                        <td>予備材判定</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($combination['data'] as $key => $value)
                                                    <tr>
                                                        <th class="left order">{{ $key }}</th>
                                                        <td>{{  number_format($value['length']) }} <span class="unit">mm</span></td>
                                                        <td>{{  number_format($value['number']) }} <span class="unit">本</span></td>
                                                        <td>{{  $value['port'] }}</td>
                                                        <td>{{ $value['spare_flag'] ? '予備材' : '指示材'  }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <p class="mt-4 pl-2">端材　{{ $combination['left'] }}</p>
                                            @endif
                                        </div>
                                @endforeach
                            @else
                                <p class="text-white">表示内容がありません</p>
                            @endif
                            </div>
                        </div>
                        {{-- 切断指示　End --}}
                        
                        {{-- 依頼内容　Start --}}
                        <div id="request"
                            @if ($page_tab != 'request') class="tab-pane hidden" @else class="tab-pane" @endif>
                            <div class="rebar-select-frame">
                                <label class="mr-2 font-semibold">現在の鉄筋径</label>
                                @foreach ($diameterDisplayList as $diameter => $id)
                                    <a 
                                        class="
                                            rebar-button
                                            @if($id == $diameter_id) diameter-select @else print-off @endif
                                            mr-2
                                        "
                                        href="{{ route('calculate.detail', ['group_code' => $group_code, 'page_tab' => 'request', 'calculation_id' => $calculation_id, 'diameter_id' => $id]) }}">
                                        {{ $diameter }}<br>
                                    </a>
                                @endforeach

                                @if (!empty($calculationRequestCodeList))
                                    <select id="calculation_id" class="house_name">
                                        @foreach ($calculationRequestCodeList as $value)
                                            <option @if ($value['code'] == $calculation_id) selected @endif
                                                value="{{ $value['code'] }}">{{ $value['name'] }}
                                                {{ $value['house_name'] }}</option>
                                        @endforeach
                                    </select>
                                @endif

                                <div class="flex items-center">
                                    <label for="std-size-name" class="mr-2">定尺寸法</label> 
                                    <p class="text-xl">{{ number_format($diameter_length) }}mm</p>
                                </div>
                            </div>
                            
                            <div class="mt-5">
                                @if (isset($calculationRequestDisplayList[$calculation_id][$diameter_id]))
                                    @foreach ($calculationRequestDisplayList[$calculation_id][$diameter_id] as $compornent_name => $detailArray)
                                        @if (!empty($detailArray))
                                        <div class="time-detail">
                                            <h3 class="text-center">部材　{{ $compornent_name }}</h3>
                                            <div class="inner-frame mt-2">
                                                <?php $disp_no = 1; ?>
                                                <table class="table notunder">
                                                    <thead>
                                                        <tr>
                                                            <td>No</td>
                                                            <td>長さ</td>
                                                            <td>切断本数</td>
                                                            <td>吐き出し口</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($detailArray as $value)
                                                            <tr>
                                                                <th scope="row">{{ $disp_no }}</th>
                                                                <td>{{ number_format($value['length']) }} <span class="unit">mm</span></td>
                                                                <td>{{ number_format($value['number']) }} <span class="unit">本</span></td>
                                                                <td>{{ $value['port'] }}</td>
                                                            </tr>
                                                            <?php $disp_no++; ?>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        {{-- 依頼内容　End --}}

                        {{-- 例外処理内容　Start --}}
                        <div id="exception" @if($page_tab!='exception') class="tab-pane hidden" @else class="tab-pane" @endif>
                            <div class="rebar-select-frame">
                                <label class="mr-2 font-semibold">現在の鉄筋径</label>
                                @foreach ($diameterDisplayList as $diameter => $id)
                                    @if($diameter == 'D10' || $diameter == 'D13' || $diameter == 'D16' )
                                        <a 
                                            class="
                                                rebar-button
                                                @if($id == $diameter_id) diameter-select @endif
                                                mr-2
                                            "
                                             href="{{ route('calculate.detail',['group_code' => $group_code, 'page_tab' => 'exception', 'calculation_id' => $calculation_id, 'diameter_id' => $id]) }}">
                                            {{ $diameter }}<br>
                                        </a>
                                    @endif
                                @endforeach
                            </div>

                            <div class="mt-5">
                                @if (isset($exceptionDisplayList[$diameter_id]))
                                <div class="time-detail is-inner-frame">
                                    <div class="inner-frame">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    {{-- <td>切断順番</td> --}}
                                                    <td>長さ</td>
                                                    <td>切断本数</td>
                                                    {{-- <td>吐き出し口</td> --}}
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php $count=1;?>
                                            @foreach ($exceptionDisplayList[$diameter_id] as $value)
                                                <tr>
                                                    {{-- <th scope="row">{{ $count }}</th> --}}
                                                    <td>{{  number_format($value['length']) }} <span class="unit">mm</span></td>
                                                    <td>{{  number_format($value['number']) }} <span class="unit">本</span></td>
                                                    {{-- <td>{{  $value['port'] }}</td> --}}
                                                </tr>
                                                <?php $count++; ?>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @else
                                    <p class="text-white">表示内容がありません</p>
                                @endif
                            </div>
                            {{-- 例外処理内容　End --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-menu>
{{--  @endsection  --}}

<script type="text/javascript">
    $(function() {
        $("#calculation_id").on('change', function() {
            // selectボックスの値を取得
            var group_code = "{{ $group_code }}";
            var calculation_id = $("#calculation_id").val();
            window.location.href = "/calculate/detail/" + group_code + "/request/" + calculation_id;
        });
    });

</script>

<style scoped lang="scss">

    .title-head a {
        width: 180px;
        height: 32px;
        line-height: 32px;
        border-radius: 62px;
        background-color: #16202E;
        text-align: center;
        color: #ffffff;
        margin-left: 34px;
    }

    .print_header {
        display: none;
    }

    .nav {
        display: flex;
    }

    .nav .nav-item {
        color: #ffffff;
    }

    .nav .nav-item.nav-item1 {
        background-color: #16202E;
    }

    .nav .nav-item.nav-item2 {
        background-color: #143361;
    }

    .nav .nav-item.nav-item3 {
        background-color: #3F5F8B;
    }

    .nav-item a {
        display: block;
        padding: 12px 78px;
    }

    .tab-content {
        padding: 16px 16px 16px 16px;
        background-color: #16202E;
    }

    .tab-content.bk-request {
        background-color: #143361;
    }

    .tab-content.bk-exeption {
        background-color: #3F5F8B;
    }   

    .rebar-select-frame {
        background-color: #ffffff;
        display: flex;
        align-items: center;
        height: 88px;
        padding: 0px 16px;
    }

    .time-detail {
        background-color: #ffffff;
        margin-bottom: 16px;
        padding: 16px;
    }

    .time-detail .time-title {
        margin-bottom: 8px;
    }

    .time-detail .table {
        width: 100%;
    }

    .time-detail .table tr {
        border-bottom: 1px solid #aaaaaa;
        text-align: center;
        height: 40px;
    }
    .time-detail .table.notunder tr {
        border-bottom: 0px;
    }

    .time-detail .table tr .left {
        text-align: left;
        width: 10%;
    }

    .time-detail .table tr .left.order {
        padding-left: 8px; 
    }


    .house_name {
        margin-left: 32px;
        margin-right: 32px;
        border: 1px solid #000000;
        max-width: 20%;
        overflow: hidden; 
    }
    option {
        white-space: nowrap;      /* テキストを折り返さない */
        overflow: hidden;        /* テキストのオーバーフローを隠す */
        text-overflow: ellipsis; /* テキストがオーバーフローした場合に3点リーダを表示 */
    }

    .is-inner-frame {
        padding-top: 40px;
    }

    .inner-frame {
        border: 1px solid #DADADA;
        padding: 24px 40px;
    }

    .unit {
        color: #b1b1b1;
        font-size: 14px;
    }

    @media print{
    /* 印刷時にのみ適用されるスタイルを記述 */
    .time-detail {
        break-inside: avoid;
    }
    
    .head, .side-menu, .print-off  {
        display: none;
    }

    .tab-content {
        padding: 0;
    }

    .nav-link.active {
        color: #000000;
        font-size: 24px;
        padding: 24px;
    }
    .rebar-button.diameter-select {
        color: #000000;
        padding; 0;
    }

    .rebar-select-frame  {
        padding: 0;
    }

    body {
        font-size: 16px;
    }

    .print_header {
        display: block;
    }

    .table .print-size {
        font-size: 14px;
    }
}

</style>
