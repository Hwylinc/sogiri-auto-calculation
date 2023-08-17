<x-menu select-page="4">
    {{-- title --}}
    <x-head title="未計算一覧" imageFlg="1"></x-head>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('calculate.confirm') }}" method="POST">
                    @csrf
                    @if ($calculationRequestCodeList->isEmpty())
                        <p class="text-center text-red-500">表示する内容はありません</p>
                    @else
                        <div class="text-center">
                            以下の内容のものを選択しますか?
                        </div>

                        <table class="mt-4 w-full">
                            <thead>
                                <tr>
                                    <td class="w-[16%] row-head">日時</td>
                                    <td class="w-[28%] row-head">メーカー</td>
                                    <td class="w-[28%] row-head">邸名</td>
                                    <td class="w-[28%] row-head">登録番号</td>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($calculationRequestCodeList as $value)
                                <tr class="row-detail-tr">
                                    <td class="row-detail date">{{ date('Y/m/d', strtotime($value['create'])); }}</td>
                                    <td class="row-detail">{{  $value['name'] }}</td>
                                    <td class="row-detail">{{  $value['house_name'] }}</td>
                                    <td class="row-detail">{{  $value['code'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                    <div class="flex justify-center mt-4">
                        <a href="{{ route('calculate.ready') }}" class="btn btn-top">未計算一覧へ戻る</a>
                        @if ($calculationRequestCodeList->isNotEmpty())
                            <a href="{{ route('calculate.start') }}" class="btn btn-exe">計算を行う</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
</x-menu>

<style lang="scss" scoped>
    .card {
        border: 1px solid #dadada;
        padding: 16px;
        margin-top: 16px;
        background-color: #ffffff;
        height: 92%;
    }

    .date {
        letter-spacing: 1px;
    }

    .row-head {
        padding: 4px;
        background-color: #6D6D6D;
        color: #ffffff;
        border: 1px solid #dadada;
    }

    .row-detail {
        border: 1px solid #dadada;
        padding: 4px 8px;
    }

    .row-detail-tr:nth-child(even) {
        background-color: #E3E7ED;
    }

    .btn {
        border-radius: 62px;
        width: 330px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px; 
    }
    .btn-top {
        border: 1px solid #000000;
    }
    .btn-exe {
        background: linear-gradient(90deg, #30CFC7 9.11%, #3A7EBA 89.29%);
        color: #ffffff;
    }

    input[type="checkbox"] {
        display: none;
    }

    .a-disabled {
        pointer-events: none;
    }

    .pri-flag {
        display: inline-block;
        width: 16px;
        height: 16px;
        padding: 1px;
    }

    .pri-border {
        border: 1px solid #DADADA;
    }

    input[type="checkbox"]:checked + label {
        border: 1px solid #DADADA;
    }

    input[type="checkbox"]:checked + label  div {
        width: 12px;
        height: 12px;
        background-color: #2083D7;
    }
</style>