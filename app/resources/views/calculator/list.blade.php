<x-menu select-page="2">

    {{-- title --}}
    <x-head title="計算結果履歴一覧" imageFlg="2"></x-head>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        @if (!empty($calculationGroupCodes))
                            <div  class="row flex">
                                <div class="row flex justify-between text-left w-[90%]">
                                    <div class="w-[14%] row-head">日時</div>
                                    <div class="w-[41%] row-head">メーカー</div>
                                    <div class="w-[53%] row-head">邸名</div>
                                </div>
                                <div class="w-[10%] row-head text-center">削除</div>
                            </div>
                            @foreach ($calculationGroupCodes as $group_code => $caliculatedInfo)
                                @if (!empty($caliculatedInfo))
                                    <div class="row-group flex">
                                            <div class="border-top w-[90%]">
                                                @foreach ($caliculatedInfo as $disp_group_id => $codes)
                                                    @if (!empty($codes))
                                                    <a href="{{ route('calculate.detail', ['group_code' => $group_code]) }}">
                                                        @foreach ($codes as $code)
                                                            <div class="row flex justify-between">
                                                                <div class="w-[14%] row-detail">{{ date( "Y/m/d", strtotime($code['created'])) }}</div>
                                                                <div class="w-[41%] row-detail">{{ $code['name'] }}</div>
                                                                <div class="w-[53%] row-detail">{{ $code['house_name'] }}</div>
                                                                {{-- <div class="w-[28%] row-detail">{{ sprintf('%06d', $code['group_id']) }}</div> --}}
                                                            </div>
                                                        @endforeach
                                                    </a>
                                                    @endif

                                                @endforeach
                                            </div>
                                            <div class="w-[10%] border-top row-detail flex items-center justify-center" onclick="deleteBtn(event, '{{ $group_code }}')"><img src="{{ asset("images/delete.svg") }}" alt="" class="head-logo"></div>
                                    </div>
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
</x-menu>

<script type="text/javascript">

function deleteBtn(e, group_code) {
    console.log('test')
    e.preventDefault()
    window.location.href = `{{ route("calculate.delete", ["group_code" => "*"]) }}`.replace('*', group_code);
}
</script>

<style lang="scss" scoped>
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

    .border-top {
        border-bottom: 1px solid #dadada;
        border-left: 1px solid #dadada;
    }
    .row-group:nth-child(odd) {
        background-color: #E3E7ED;
    }
</style>