<div class="right ml-4">
    <div class="calc-text">
        計算情報
    </div>
    <div class="bg-white p-4">
        <table class="w-full">
            <tr>
                <th class="t-col t-left">メーカー</th>
                <th class="t-col">邸名</th>
            </tr>
            <tr>
                <td class="t-left">{{ $selectInfo['client_name'] }}</td>
                <td>{{ $selectInfo['house_name'] }}</td>
            </tr>
        </table>
    </div>
</div>

<style scoped>

.calc-text {
    padding: 8px 6px;
    background-color: #E3E7ED;
    font-weight: 600;
    border-left: solid 4px #2083D7;
}

.t-col {
    background-color: #6D6D6D;
    color: #ffffff;
}

.t-left {
    border-right: solid 2px #DADADA;
}

</style>