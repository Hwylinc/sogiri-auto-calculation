<x-menu select-page="1">

    {{-- title --}}
    <x-head title="鉄筋計算" imageFlg="1"></x-head>

    <div class="bg-white" style="height:92%">
        <div class="flex justify-center items-center h-full" >
            <div class="w-full text-center">
                <div class="mb-3">
                    <p class="text-center">
                        {{ $client_name }}　{{ $house_name }}様邸の<br>
                        鉄筋情報の登録が完了いたしました。
                    </p>
                </div>
                <div class="mb-3">
                    {{-- どこに遷移するかわからない --}}
                    <a href="{{ route('rebar.register', ['diameter' => 1]) }}" class="top-bt">TOPへ戻る</a>
                    <a href="{{ route('rebar.select') }}" class="add-bt">案件を追加する</a>
                </div>
                <div>
                    {{-- どこのrouteに飛ばせば良いかわからない --}}
                    <a href="{{ route('rebar.register', ['diameter' => 1]) }}" class="next-bt">計算へ進む</a>
                </div>
            </div>
        </div>
    </div>

</x-menu>

<style scoped>
    a {
        display: inline-block;
        border-radius: 120px;
        padding: 6px 12px;
        width: 160px;
        color: #ffffff;
        font-size: 12px;
    }

    .top-bt {
        border: 1px solid #000000;
        margin-right: 10px;
        color: #000000;
    }

    .add-bt {
        background-color: #000000;
    }

    .next-bt {
        width: 330px;
        background-color: #53BC00;
    }

</style>