<x-menu select-page="5">

    {{-- title --}}
    <x-head title="加工指示書CSVアップロード" imageFlg="1"></x-head>

    <div class="row justify-content-center main_height">
        <div class="col-md-8 sub_height">
            <div class="card sub_height">
                <div class="card-body">
                    <form action="{{ route('csv.csv-result') }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="name_of_mansion" value="{{ $name_of_mansion }}">
                        <input type="hidden" name="maker_name" value="{{ $maker_name }}">
                        @if (!empty($csv_data))
                            @foreach ($csv_data as $val)
                                <input type="hidden" name="component_id[]" value="{{ $val[1] }}">
                                <input type="hidden" name="diameter_id[]" value="{{ $val[2] }}">
                                <input type="hidden" name="length[]" value="{{ $val[3] }}">
                                <input type="hidden" name="number[]" value="{{ $val[4] }}">
                            @endforeach
                        @endif
                        <div class="text-center p-3 rounded col-md-10 mx-auto">
                            <div id="regist_title">
                                <p>
                                    {{ $maker_name }} {{ $name_of_mansion }}の<br>
                                    @if (empty($result_flag))
                                        鉄筋情報の登録しますか？
                                    @else
                                        鉄筋情報の登録が完了いたしました。
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if (empty($result_flag))
                            <div class="black-btn">
                                <button type="button" class="back" onclick="location.href='{{ route('csv.csv-upload') }}'">戻る</button>
                            </div>
                            <div class="green-btn">
                                <input class="submit" type="submit" value="登録する">
                            </div>
                        @else
                            <div class="black-btn">
                                <button type="button" class="back" onclick="location.href='{{ route('csv.csv-upload') }}'">案件を追加する</button>
                            </div>
                            <div class="green-btn">
                                <input class="submit" type="submit" value="計算へ進む">
                            </div>
                        @endif
                        @foreach($errors->all() as $error)
                            <p class="alert-danger">{{$error}}</p>
                        @endforeach
                    </form>
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