<x-menu select-page="5">

    {{-- title --}}
    <x-head title="加工指示書CSVアップロード" imageFlg="1"></x-head>

    <div class="row justify-content-center main_height">
        <div class="col-md-8 sub_height">
            <div class="card sub_height">
                <div class="card-body">
                    <form action="{{ route('csv.confirm', ['diameter' => '1']) }}" method="POST" id="result">
                        {{ csrf_field() }}
                        <input type="hidden" name="view_type" value="csv">
                        <input type="hidden" name="code" value="{{ $code ?? null }}">
                    </form> 
                    <form action="{{ route('csv.csv-result') }}" method="POST" id="upload" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="house_name" value="{{ $house_name }}">
                        <input type="hidden" name="client_name" value="{{ $client_name }}">
                        @if (!empty($csv_data))
                            @foreach ($csv_data as $key => $val)
                                <?php 
                                    $comp1_count = 1;
                                    $comp2_count = 1;
                                    $comp3_count = 1;
                                    $comp4_count = 1;
                                ?>
                                @foreach ($val as $data)
                                    @switch ($data[1])
                                        @case('スターラップ')
                                            @if ($comp1_count == 1)
                                                <input type="hidden" name="input[{{ $key }}][comp_1][id]" value="{{ $components[0]->id }}">
                                                <input type="hidden" name="input[{{ $key }}][comp_1][name]" value="{{ $components[0]->name }}">
                                                <input type="hidden" name="component[{{ $key }}][]" value="comp_1">
                                            @endif
                                            <input type="hidden" name="input[{{ $key }}][comp_1][data][{{ $comp1_count }}][length]" value="{{ $data[3] }}">
                                            <input type="hidden" name="input[{{ $key }}][comp_1][data][{{ $comp1_count++ }}][number]" value="{{ $data[4] }}">
                                            @break
                                        @case('主筋')
                                        @case('腹筋')
                                        @case('受筋')
                                            @if ($comp2_count == 1)
                                                <input type="hidden" name="input[{{ $key }}][comp_2][id]" value="{{ $components[1]->id }}">
                                                <input type="hidden" name="input[{{ $key }}][comp_2][name]" value="{{ $components[1]->name }}">
                                                <input type="hidden" name="component[{{ $key }}][]" value="comp_2">
                                            @endif
                                            <input type="hidden" name="input[{{ $key }}][comp_2][data][{{ $comp2_count }}][length]" value="{{ $data[3] }}">
                                            <input type="hidden" name="input[{{ $key }}][comp_2][data][{{ $comp2_count++ }}][number]" value="{{ $data[4] }}">
                                            @break
                                        @case('スラブ筋')
                                            @if ($comp3_count == 1)
                                                <input type="hidden" name="input[{{ $key }}][comp_3][id]" value="{{ $components[2]->id }}">
                                                <input type="hidden" name="input[{{ $key }}][comp_3][name]" value="{{ $components[2]->name }}">
                                                <input type="hidden" name="component[{{ $key }}][]" value="comp_3">
                                            @endif
                                            <input type="hidden" name="input[{{ $key }}][comp_3][data][{{ $comp3_count }}][length]" value="{{ $data[3] }}">
                                            <input type="hidden" name="input[{{ $key }}][comp_3][data][{{ $comp3_count++ }}][number]" value="{{ $data[4] }}">
                                            @break
                                        @case('付属鉄筋')
                                            @if ($comp4_count == 1)
                                                <input type="hidden" name="input[{{ $key }}][comp_4][id]" value="{{ $components[3]->id }}">
                                                <input type="hidden" name="input[{{ $key }}][comp_4][name]" value="{{ $components[3]->name }}">
                                                <input type="hidden" name="component[{{ $key }}][]" value="comp_4">
                                            @endif
                                            <input type="hidden" name="input[{{ $key }}][comp_4][data][{{ $comp4_count }}][length]" value="{{ $data[3] }}">
                                            <input type="hidden" name="input[{{ $key }}][comp_4][data][{{ $comp4_count++ }}][number]" value="{{ $data[4] }}">
                                            @break
                                        @default
                                            @break
                                    @endswitch
                                @endforeach
                            @endforeach
                        @endif
                        <div class="text-center p-3 rounded col-md-10 mx-auto">
                            <div id="regist_title">
                                <p>
                                    {{ $client_name }} {{ $house_name }}の<br>
                                    @if (empty($result_flag))
                                        鉄筋情報の登録しますか？
                                    @else
                                        鉄筋情報の登録が完了いたしました。
                                    @endif
                                </p>
                            </div>
                        </div>
                    </form>
                    @if (empty($result_flag))
                        <div class="black-btn">
                            <button type="button" class="back" onclick="location.href='{{ route('csv.csv-upload') }}'">戻る</button>
                        </div>
                        <div class="green-btn">
                            <input class="submit" type="submit" form="upload" value="登録する">
                        </div>
                    @else
                        <div class="black-btn">
                            <button type="button" class="back" onclick="location.href='{{ route('csv.csv-upload') }}'">案件を追加する</button>
                        </div>
                        <div class="green-btn">
                            <input class="submit" type="submit" form="result" value="計算へ進む">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-menu>

<script>
    $(function(){                                                                                                                                                  
    });
</script>

<style lang="scss" scoped>
    .main_height {
        height: 95%;
    }

    .sub_height {
        height: 100%;
        width: 100%;
        display: table;
    }

    .card {
        background-color: #ffffff;
        padding: 16px;
    }

    .row-head {
        padding: 4px;
        background-color: #6D6D6D;
        color: #ffffff;
        border: 1px solid #dadada;
    }

    .row-detail {
        border-right: 1px solid #dadada;
        padding: 4px 8px;
    }

    .card-body {
        text-align: center;
        display: table-cell;
        vertical-align: middle;
    }

    .plant-label1 {
        padding-right: 100px;
    }

    .border-top {
        border-bottom: 1px solid #dadada;
        border-left: 1px solid #dadada;
    }

    .row-group:nth-child(odd) {
        background-color: #E3E7ED;
    }

    .black-btn {
        padding-bottom: 15px;
        background-color: initial;
    }

    #regist_title {
        padding-bottom: 30px;
        letter-spacing: 2px;
        font-size: 18px;
    }

    .back {
        background: #000000;
        color: #ffffff;
        width: 400px;
        height: 40px;
        border-radius: 30px;
        font-size: 14px;
    }

    .submit {
        background: rgb(111, 186, 55);
        color: #ffffff;
        width: 400px;
        height: 40px;
        border-radius: 30px;
        font-size: 14px;
    }

    .alert-danger {
        padding-top: 10px;
        color: red;
        font-size: 14px;
    }

</style>