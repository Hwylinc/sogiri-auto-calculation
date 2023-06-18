<x-menu select-page="1">
<div class="rebar">
    {{-- title --}}
    <x-head title="鉄筋計算" imageFlg="1"></x-head>

    <div class="flex mt-4">

        {{-- 左サイド --}}
        <div class="left">

            {{-- 直径/定尺寸法 --}}
            <div class="flex justify-between items-center">
                {{-- 直径 --}}
                <div class="flex items-center">
                    <label class="mr-2 font-semibold">現在の鉄筋径</label>
                    @foreach ($diameters as $diameter)
                        <div class="mr-0.5">
                            <a 
                                class="
                                    button 
                                    @if($page['now'] == $diameter->id) diameter-select @endif
                                    a-disabled
                                "
                            >
                                D{{ $diameter->size }}
                            </a> 
                        </div>
                    @endforeach
                </div>
                {{-- 定尺寸法 --}}
                <div class="flex items-center">
                    <label for="std-size-name" class="mr-2 font-semibold">定尺寸法</label> 
                    <p class="font-bold text-xl">{{ number_format($select_diameter['length']) }}mm</p>
                </div>
            </div>

            {{-- 入力form --}}
            <form method="POST" action="{{ route('rebar.store') }}">
                @csrf
                <input type="hidden" name="process" value="update" />
                <input type="hidden" name="select_diameter" value="{{ $page['now'] }}" />

                <div>

                    {{-- 部材タブ --}}
                    <div id="comp-frame" class="comp-frame bg-white flex w-full mb-3 mt-3 hidden">
                        <ul class="flex">
                            @if( !empty($exist_info) )
                            @forEach( $exist_info['input'] as $data )
                            <div class="flex items-center mr-2">
                                <div class="select-check-flame">
                                    <div class="select-check-bk"></div>
                                </div>
                                <p>{{ $data['name'] }}</p>
                            </div>
                            @endforeach
                            @endif
                        </ul>
                    </div>

                    {{-- メッセージ --}}
                    <div class="flex justify-between items-center mb-3">
                        <x-message :message="session('message')" />
                        <button type="button" id="edit-btn" class="button edit-btn flex items-center hidden">編集</button>
                    </div>

                    {{-- 入力formのtable --}}
                    <div id="CompForm" class="bg-white p-4 hidden"></div>

                    <div class="flex justify-center mt-4">

                        <input type="hidden" name="select_diameter" value="{{ $page['now'] }}" />

                        {{-- 戻るボタン --}}
                        @if (array_key_exists('prev', $page))
                            <a 
                                href="{{ route('rebar.confirm', ['diameter' => $page['prev']->id])}}"
                                class="page-btn prev-btn"
                            >
                                D{{ $page['prev']->size }}へ戻る
                            </a>
                        @endif

                        @if ($page['next']["id"] !== -1)
                            <a 
                                href="{{ route('rebar.confirm', ['diameter' => $page['next']->id])}}"
                                class="page-btn black-btn"
                            >
                                D{{ $page['next']->size }}へ進む
                            </a>
                        @else
                            <a 
                                href="{{ route('rebar.done') }}"
                                class="page-btn confirm-btn"
                            >
                                鉄筋情報を登録
                            </a>
                        @endif
                    </div>

                </div>
            </form>
        </div>

        {{-- 右サイド --}}
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
                        <td class="t-left">{{ $select_info['client_name'] }}</td>
                        <td>{{ $select_info['house_name'] }}</td>
                    </tr>
                </table>
            </div>
        </div>

    </div>
</div>
</x-menu>

<script>

    window.addEventListener('DOMContentLoaded', function(){ 

        const existInfo = @json($exist_info);
        console.log(existInfo)
        let screenMode = false; // false: 確認モード, true: 編集モード
        let error = @json($error);

        console.log(error)

        // 編集ボタンクリック時処理
        $('#edit-btn').on('click', (e) => {

            e.preventDefault();
            
            if (!screenMode) {  // screen modeを変更して編集モードにする
                changeScreen()
            } else {    // 編集内容をpost送信する
                $('form').submit()
            }
            
        })

        const changeScreen = () => {

            screenMode = true;

            $('#CompForm').empty(); // 子要素をリセット

            existInfo.input.forEach((row) => {
                makeFormEl(row.id, row.name, row.data)
            })

            $('#edit-btn').text('完了')

        }

        const createCompDtl = () => {
            let element = ""
            for (const detail of compDetail) {
                element += `
                    <option value="${detail.id}">${detail.name}</option>
                `
            }
            return element;
        }

        const createComoTableRowEl = (rowInfo, selectId) => {
            console.log('createComoTableRowEl')
            const bgClass = rowInfo.display_order % 2 === 0 ? "main-bg-color" : '' ; 

            const tr = $('<tr>')
            
            $('<input>', {
                type: 'hidden',
                name: `input[comp_${selectId}][data][${rowInfo.display_order}][display_order]`,
                value: rowInfo.display_order
            }).appendTo(tr)

            $('<input>', {
                type: 'hidden',
                name: `input[comp_${selectId}][data][${rowInfo.display_order}][id]`,
                value: rowInfo.id ? rowInfo.id : -999
            }).appendTo(tr)

            let showOrder = "000" + rowInfo.display_order;
            const order = showOrder.substr(showOrder.length - 4)

            $('<td>', {
                'class': `${bgClass} p-1 border-r-[1px] border-[#DADADA]`,
            }).text(order).appendTo(tr)

            const tdLength = $('<td>', {
                'class': `${bgClass} px-3 border-r-[1px] border-[#DADADA] relative`,
            }).appendTo(tr)

            // 長さ
            if( screenMode ) {
                $('<input>', {
                    type: 'number',
                    name: `input[comp_${selectId}][data][${rowInfo.display_order}][length]`,
                    disabled: !screenMode,
                    value: rowInfo.length,
                    id: `comp-len-${selectId}-${rowInfo.display_order}`,
                    'class': "len-input"
                }).appendTo(tdLength)
            } else {
                $('<p class="tracking-wider font-semibold">', {
                }).text(rowInfo.length).appendTo(tdLength)
            }

            $('<span>', {
                'class': 'unit',
            }).text('mm').appendTo(tdLength)

            const tdNumber = $('<td>', {
                'class': `${bgClass} px-3 border-r-[1px] border-[#DADADA] relative`,
            }).appendTo(tr)

            if( screenMode ) {
                $('<input>', {
                    type: 'number',
                    name: `input[comp_${selectId}][data][${rowInfo.display_order}][number]`,
                    value: rowInfo.number,
                    id: `comp-num-${selectId}-${rowInfo.display_order}`
                }).appendTo(tdNumber)
            } else {
                $('<p class="tracking-wider font-semibold">', {
                }).text(rowInfo.number).appendTo(tdNumber)
            }

            $('<span>', {
                'class': 'unit',
            }).text('本').appendTo(tdNumber)

            // 削除
        
            if ( screenMode ) {

                const tdDelete = $('<td>', {
                    'class': `${bgClass} px-3 border-r-[1px] border-[#DADADA] relative text-center`,
                }).appendTo(tr)

                $('<img>', {
                    src: "{{ asset("images/delete.svg") }}",
                    height: '16px',
                    width: '16px',
                    on: {
                        click: () => {deleteInput(selectId, rowInfo.display_order)}
                    },
                    'class': 'delete-icon'
                }).appendTo(tdDelete)
            }
            

            return tr

        }

        const createCompTableEl = () => {
            const table = $('<table>', {
                'class': 'mt-2 input-table',
            })

            const tableHead = ['NO', '長さ', '本数', "削除"]

            const tr = $('<tr>')

            tableHead.forEach((title) => {

                let wClass = "w-40%"
                let hiddenClass = ""

                if (title === "NO" || title === "削除") {
                    wClass = "w-10%"
                } 

                if (!screenMode && title === "削除") {
                    hiddenClass = "hidden"
                }

                const th = $('<th>', {
                    'class': `comp-div-th ${wClass} ${hiddenClass}`
                }).append(title)

                $(th).appendTo(tr)
            })

            return $(table).append(tr);
        }
         

        // 部材のform枠処理
        const makeFormEl = (selectId, componentName, inputData=[]) => {
            console.log('compDiv')

            const compId =  'comp-div-' + selectId
            if ( !($(`#${compId}`).length) ) {

                const compDiv = $('<div>', {
                    id: compId,
                    'class': "comp-div bg-white comp-div-outline"
                }).appendTo('#CompForm')

                $('<input>', {
                    type: 'hidden',
                    name: `component[]`,
                    value: 'comp_' + selectId
                }).appendTo('#CompForm')

                // ② make title
                $('<p class="comp-div-title">').text(componentName).appendTo(compDiv);
                $('<input>', {
                    type: 'hidden',
                    name: `input[comp_${selectId}][name]`,
                    value: componentName
                }).appendTo(compDiv)

                const CompoTable = $(createCompTableEl()).appendTo(compDiv)
                const hiddenCompId = $('<input>', {
                    type: 'hidden',
                    name: `input[comp_${selectId}][id]`,
                    value: selectId
                }).appendTo(compDiv)

                // ② 選択された名前をhiddenに追加

                
                const button = $('<button>', {
                    type: 'button',
                    'class': `w-[26px] h-[26px] p-[4px] border-[2px]  flex items-center justify-center mt-4 ml-4 ${!screenMode ? 'hidden' : ''}`,
                    on: {
                        click: () => {addForm(compId)}
                    }
                }).text('＋').appendTo(compDiv)

                // データが存在するか確認
                let rowCount = inputData ? Object.keys(inputData).length : 10;

                if (rowCount < 10) {
                    rowCount = 10;
                }
                
                for (let i=0; i < rowCount; i++) {
                    const rowInfo = inputData.length !== 0 && inputData[i+1]  ? inputData[i+1] : {detail: "1", display_order: i+1, length: null, number: null}
                    console.log(rowInfo)
                    $(createComoTableRowEl(rowInfo, selectId)).appendTo(CompoTable)
                }
                console.log('compDiv done')
                return 
            } 
        }

        // プラスボタン押下時にformを追加する
        const addForm = (compId) => {
            const CompoTable = $(`#${compId} table`)
            const compoTableRowCount = CompoTable.children().length;
            const initialRow = {detail: "1", display_order: compoTableRowCount, length: null, number: null}
            $(createComoTableRowEl(initialRow, compId)).appendTo(CompoTable)
        }

        // 削除ボタン処理
        const deleteInput = (compId, display_order) => {
            $(`#comp-len-${compId}-${display_order}`).val("");
            $(`#comp-num-${compId}-${display_order}`).val("");
        }

        // 初期実行時にform情報がある場合のイベント
        if (existInfo.length != 0) {
            console.log(existInfo)
            existInfo.input.forEach((row) => {
                console.log(row.id)
                makeFormEl(row.id, row.name, row.data)
                $('#comp-frame').removeClass('hidden');
                $("#CompForm").removeClass('hidden');
                $("#edit-btn").removeClass('hidden');
            })

            // エラーの場合はscreen modeを切り替える
            if (error === "1") {
                changeScreen()
            }
        }

        // ---------------------------------
        // The methods used in HTML
        // ---------------------------------
        window.addForm = addForm; 

    });

</script>

<style scoped>

    .right td, th {
        width: 50%;
        text-align: left;
        padding: 6px 8px; 
    }

    .t-col {
        background-color: #6D6D6D;
        color: #ffffff;
    }

    .t-left {
       border-right: solid 2px #DADADA;
    }

    .calc-text {
        padding: 8px 6px;
        background-color: #E3E7ED;
        font-weight: 600;
        border-left: solid 4px #2083D7;
    }

    input[type="number"]::-webkit-outer-spin-button, 
    input[type="number"]::-webkit-inner-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    } 

    input[type="text"],
    input[type="number"] {
        padding: 4px;
        padding-right: 45px; /* 右側に30px分の余白を設定 */
        border: 1px solid #d0d0d0;
    }

    .unit {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%)
    }

    .a-disabled {
        pointer-events: none;
    }

    .bt-tran {
        width: 130px;
    }

    .select {
        color: #ffffff;
        background-color: #16202E;
    }

    .select-check-flame {
        border: 1px solid #d0d0d0;
        width: 16px;
        height: 16px;
        padding: 1px;
        margin-right: 4px;
        .select-check-bk {
            width: 12px;
            height: 12px;
            background-color: #2083D7;
        }
    }

    .button {
        width: 80px;
        text-align: center;
        display: inline-block;
        padding: 8px 16px;
        color: #16202E;
        font-size: 16px;
        cursor: pointer;
        background-color: #DADADA;
    }

    .edit-btn {
        display: flex;
        padding: 4px;
        align-items: center;
        justify-content: center;
        background-color: #53BC00;
        color: #ffffff;
        height: 32px;
    }

    .diameter-select {
        color: #ffffff;
        background-color: #3A7EBA;
    }



    /* ボタン */
    .prev-btn {
        background-color: #ffffff;
        color: #000000;
        border: 1px solid #000000;
        margin-right: 8px;
    }
    .confirm-btn {
        background-color: #53BC00;
        color: #ffffff;
    }

    .len-input {
        border: none;
    }

    .page-btn {
        display: inline-block;
        font-size: 14px;
        padding: 4px 32px 4px 32px;
        width: 180px;
        height: 35px;
        line-height:25px;
        border-radius: 62px;
        text-align: center;
        &:hover {
            cursor: pointer;
        }
    }

    .hidden {
        display: none !important;
    }


</style>