<x-menu select-page="1">
    {{-- title --}}
    <x-head title="鉄筋計算" imageFlg="1"></x-head>

    <div class="card">
        <div class="card-body">
            <div class="mt-5">
                <p class="text-center mb-5">計算が完了いたしました。</p>
                <div class="flex justify-center">
                    <a href="{{ route('rebar.select') }}" class="btn btn-top">トップへ戻る</a>
                    <a href="{{ route('calculate.detail', ['group_code' => $group_code]) }}" class="btn btn-confirm">計算結果を確認</a>
                </div>
            </div>
        </div>
    </div>
</x-menu>

<style scoped lang="scss">
    .card {
        border: 1px solid #dadada;
        padding: 16px;
        margin-top: 16px;
        background-color: #ffffff;
        height: 92%;
        display: flex;
        align-items: center;
        justify-content: center;
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
    .btn-confirm {
        background-color: #000000;
        color: #ffffff;
    }

</style>