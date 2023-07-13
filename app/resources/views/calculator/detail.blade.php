
<x-menu select-page="1">

    {{-- title --}}
    <div class="title-head flex">
        <x-head title="鉄筋切断指示" imageFlg="1" horizon="0"></x-head>
        <a href="{{ route('calculate.list') }}" type="button" class="btn btn-dark rounded-pill ">一覧へ戻る</a>
        <a href="{{ route('home') }}" type="button" class="btn btn-dark rounded-pill ">トップへ戻る</a>
    </div>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs">
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
                    
                    <div class="tab-content">
                        {{-- 切断指示　Start --}}
                        <div id="result" @if($page_tab=='result') class="tab-pane active" @else class="tab-pane" @endif>
                            <div class="rebar-select-frame">
                                <label class="mr-2 font-semibold">現在の鉄筋径</label>
                                @foreach ($diameterDisplayList as $diameter => $id)
                                @if($diameter == 'D10' || $diameter == 'D13' || $diameter == 'D16')
                                    <a 
                                        class="
                                            rebar-button
                                            @if($id == $diameter_id) diameter-select @endif
                                            mr-2
                                        "
                                         href="{{ route('calculate.detail',['group_code' => $group_code, 'page_tab' => 'result', 'calculation_id' => $calculation_id, 'diameter_id' => $id]) }}">
                                        {{ $diameter }}<br>
                                    </a>
                                @endif
                                @endforeach
                            </div>
                            <div class="mt-5">
                                @if (!empty($resultDisplayList[$diameter_id])) 
                                    @foreach ($resultDisplayList[$diameter_id] as $setTimes => $combination)
                                        <div class="time-detail">
                                            <h2 class="time-title">{{ $setTimes }}回目</h2>
                                            @if (!empty($combination['data']))
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td class="left">切断順番</td>
                                                        <td>長さ</td>
                                                        <td>切断本数</td>
                                                        <td>吐き出し口</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($combination['data'] as $key => $value)
                                                    <tr>
                                                        <th class="left order">{{ $key }}</th>
                                                        <td>{{  $value['length'] }} mm</td>
                                                        <td>{{  $value['number'] }} 本</td>
                                                        <td>{{  $value['port'] }}</td>
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
                            @foreach ($diameterDisplayList as $diameter => $id)
                                <a @if ($id == $diameter_id) class="active_diameter" @endif
                                    href="{{ route('calculate.detail', ['group_code' => $group_code, 'page_tab' => 'request', 'calculation_id' => $calculation_id, 'diameter_id' => $id]) }}">
                                    {{ $diameter }}<br>
                                </a>
                            @endforeach
                            @if (!empty($calculationRequestCodeList))
                                <select id="calculation_id">
                                    @foreach ($calculationRequestCodeList as $value)
                                        <option @if ($value['code'] == $calculation_id) selected @endif
                                            value="{{ $value['code'] }}">{{ $value['name'] }}
                                            {{ $value['house_name'] }}</option>
                                    @endforeach
                                </select>
                            @endif
                            @if (isset($calculationRequestDisplayList[$calculation_id][$diameter_id]))
                                @foreach ($calculationRequestDisplayList[$calculation_id][$diameter_id] as $compornent_id => $detailArray)
                                    @if (!empty($detailArray))
                                        <?php $disp_no = 1; ?>
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
                                                        <td>{{ $value['length'] }} mm</td>
                                                        <td>{{ $value['number'] }} 本</td>
                                                        <td>{{ $value['port'] }}</td>
                                                    </tr>
                                                    <?php $disp_no++; ?>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        {{-- 依頼内容　End --}}

                        {{-- 例外処理内容　Start --}}
                        <div id="exception" @if($page_tab!='exception') class="tab-pane hidden" @else class="tab-pane" @endif>
                            @foreach ($diameterDisplayList as $diameter => $id)
                                @if($diameter == 'D10' || $diameter == 'D13' || $diameter == 'D16' )
                                    <a @if($id == $diameter_id) class="active_diameter" @endif href="{{ route('calculate.detail',['group_code' => $group_code, 'page_tab' => 'exception', 'calculation_id' => $calculation_id, 'diameter_id' => $id]) }}">
                                        {{ $diameter }}<br>
                                    </a>
                                @endif
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
                                         <?php $count++; ?>
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
</x-menu>
{{--  @endsection  --}}

@section('pageJs')
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
@endsection

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

    .nav {
        display: flex;
        .nav-item {
            padding: 12px 78px;
            color: #ffffff;
            &.nav-item1 {
                background-color: #16202E;
            }
            &.nav-item2 {
                background-color: #143361;
            }
            &.nav-item3 {
                background-color: #3F5F8B;
            }
        };
    }

    .tab-content {
        padding: 16px 16px 16px 16px;
        background-color: #16202E;
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
        .time-title {
            margin-bottom: 8px;
        };
        .table {
            width: 100%;
        }
        .table tr {
            border-bottom: 1px solid #aaaaaa;
            text-align: center;
            height: 40px;
            .left {
                text-align: left;
                width: 10%;
                &.order {
                    padding-left: 8px; 
                }
            }
        }
    }

</style>
