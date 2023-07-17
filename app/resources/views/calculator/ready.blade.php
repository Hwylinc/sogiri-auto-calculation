<x-menu select-page="1">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        @if ($calculationRequestCodeList->isEmpty())
                        <p>表示する内容はありません</p>
                        @else
                            <table class="table table-borderless">
                                <tbody>
                                @foreach ($calculationRequestCodeList as $value)
                                    <tr>
                                        <th>{{ $value['created_at']->format('m/d') }}</th>
                                        <td>{{  $value['name'] }}</td>
                                        <td>{{  $value['house_name'] }}</td>
                                        <td>{{  $value['code'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                        <div class="d-grid gap-2">
                            <a href="{{ route('home') }}" class="btn rounded-pill btn-secondary">トップへ戻る</a>
                            @if ($calculationRequestCodeList->isNotEmpty())
                                <a href="{{ route('calculate.start') }}" class="btn rounded-pill btn-primary">計算を行う</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-menu>

<style lang="scss" scoped>
    .card {
        border: 1px solid #dadada;
        padding: 16px;
    }
</style>