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

            <div>
                <form method="POST" action="{{ route('rebar.store') }}">
                    @csrf
                    <x-message :message="session('message')" />
                    <input type="hidden" name="process" value="insert" />
                    <div>
                        <div class="comp-frame bg-white flex w-full mb-3">
                            @foreach ($components as $component)
                            <div class="mr-2">
                                <input 
                                    name="component[]" 
                                    type="checkbox" 
                                    class="component mr-2" 
                                    id="comp-check-{{ $component['id'] }}" data="{{ $component['id'] }}" 
                                    value="comp_{{ $component['id'] }}"
                                    onchange="compoClick({{ $component['id'] }}, '{{ $component['name'] }}')"
                                    @if ($component['factory_id'] != $factory_id) disabled @endif
                                >
                                <label for="comp-check-{{ $component['id'] }}" data="{{ $component['id'] }}">{{ $component['name'] }}</label>
                            </div>
                            @endforeach
                        </div>

                        <div id="CompForm" class="comp-form  bg-white p-4 hidden">
                        </div>

                        <div class="flex justify-center mt-4">

                            {{-- 現在選択されている鉄筋径 --}}
                            <input type="hidden" name="select_diameter" value="{{ $page['now'] }}" />

                            {{-- 戻るボタン --}}
                            @if (array_key_exists('prev', $page))
                                <button type="submit" name="action" value="{{ $page['prev']->id }}" class="page-btn prev-btn mr-2">D{{ $page['prev']->size }}へ戻る</button>
                            @endif

                            @if ($page['next']["id"] !== -1)
                                {{-- 進むボタン --}}
                                <button type="submit" name="action" value="{{ $page['next']["id"] }}" class="page-btn bg-black text-white ml-2">
                                    {{ $page['next']->size . "へ進む" }}
                                </button>
                            @else
                                {{-- 確認ボタン --}}
                                <button type="submit" name="action" value="{{ $page['next']["id"] }}" class="page-btn confirm-btn bg-black text-white ml-2">
                                    {{ '確認に進む' }}
                                </button>
                            @endif
                            
                           

                        </div>
                    </div>
                </form>
            </div>
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
<div>
</x-menu>

<script>
    const functions = {}
    // $fucntionで読めないため、以下で実行
    window.addEventListener('DOMContentLoaded', function(){

        const existInfo = @json($exist_info);

        // 部材のform枠処理
        const compDivExe = (id, name) => {
            // $('.comp-div').addClass('hidden')

            const compId =  'comp-div-' + id
            if ( !($(`#${compId}`).length) ) {
                
                // ① make outline
                const compDiv = $('<div>', {
                    id: compId,
                    'class': "comp-div bg-white comp-div-outline"
                }).appendTo('#CompForm')

                // ② make title
                $('<p class="comp-div-title">').text(name).appendTo(compDiv);
                
                // ② make table and table header 
                const CompoTable = $(createCompTableEl()).appendTo(compDiv)

                // ② 選択されたidをhiddenに追加
                const hiddenCompId = $('<input>', {
                    type: 'hidden',
                    name: `input[comp_${id}][id]`,
                    value: id
                }).appendTo(compDiv)

                // ② 選択された名前をhiddenに追加
                $('<input>', {
                    type: 'hidden',
                    name: `input[comp_${id}][name]`,
                    value: name
                }).appendTo(compDiv)

                // ② make add button
                const button = $('<button>', {
                    type: 'button',
                    'class': 'w-[26px] h-[26px] p-[4px] border-[2px]  flex items-center justify-center mt-4 ml-4',
                    on: {
                        click: () => {addForm(compId, id)}
                    }
                }).text('＋').appendTo(compDiv)

                // 編集時に必要な処理
                let existInfoSelect = [];

                if (existInfo.length !== 0) {
                    existInfo.input.forEach((array) => {
                        if (array.id == id) {
                            existInfoSelect = array.data
                        }
                    })
                }
                console.log(existInfoSelect)

                let rowCount = Object.keys(existInfoSelect).length

                if (rowCount < 10) {
                    rowCount = 10;
                }
                
                for (let i=1; i <= rowCount; i++) {
                    const rowInfo = existInfoSelect[i]  ? existInfoSelect[i] : {detail: "1", display_order: i, length: null, number: null}
                    $(createComoTableRowEl(id, rowInfo)).appendTo(CompoTable)
                }

                return;
            } 

            $(`#${compId}`).removeClass('hidden')
        }

        const createCompTableEl = () => {

            const table = $('<table>', {
                'class': 'mt-2 input-table',
            })

            const tableHead = ['NO', '長さ', '本数', '削除']

            const tr = $('<tr>')

            tableHead.forEach((title) => {

                let wClass = "w-40per"

                if (title === "NO" || title === "削除") {
                    wClass = "w-10per"
                } 

                const th = $('<th>', {
                    'class': `comp-div-th ${wClass}`
                }).append(title)

                $(th).appendTo(tr)
            })

            return $(table).append(tr);
        }

        const createComoTableRowEl = (id, rowInfo) => {
            console.log('createComoTableRowEl')
            const bgClass = rowInfo.display_order % 2 === 0 ? "main-bg-color" : '' ; 

            const tr = $('<tr>')
            
            $('<input>', {
                type: 'hidden',
                name: `input[comp_${id}][data][${rowInfo.display_order}][display_order]`,
                value: rowInfo.display_order
            }).appendTo(tr)

            let showOrder = "000" + rowInfo.display_order;
            const order = showOrder.substr(showOrder.length - 4)

            $('<td>', {
                'class': `${bgClass} p-1 border-r-[1px] border-[#DADADA] w-10per text-center`,
            }).text(order).appendTo(tr)

            const tdLength = $('<td>', {
                'class': `${bgClass} px-3 border-r-[1px] border-[#DADADA] relative`,
            }).appendTo(tr)

            $('<input>', {
                type: 'number',
                name: `input[comp_${id}][data][${rowInfo.display_order}][length]`,
                value: rowInfo.length,
                id: `comp-len-${id}-${rowInfo.display_order}`
            }).appendTo(tdLength)

            $('<span>', {
                'class': 'unit',
            }).text('mm').appendTo(tdLength)

            const tdNumber = $('<td>', {
                'class': `${bgClass} px-3 border-r-[1px] border-[#DADADA] relative`,
            }).appendTo(tr)

            $('<input>', {
                type: 'number',
                name: `input[comp_${id}][data][${rowInfo.display_order}][number]`,
                value: rowInfo.number,
                id: `comp-num-${id}-${rowInfo.display_order}`
            }).appendTo(tdNumber)

            $('<span>', {
                'class': 'unit',
            }).text('本').appendTo(tdNumber)

            // 削除
            const tdDelete = $('<td>', {
                'class': `${bgClass} px-3 border-r-[1px] border-[#DADADA] relative text-center`,
            }).appendTo(tr)

            $('<img>', {
                src: "{{ asset("images/delete.svg") }}",
                height: '16px',
                width: '16px',
                on: {
                    click: () => {deleteInput(id, rowInfo.display_order)}
                },
                'class': 'delete-icon'
            }).appendTo(tdDelete)


            return tr

        }

        // プラスボタン押下時にformを追加する
        const addForm = (compId, id) => {
            const CompoTable = $(`#${compId} table`)
            const compoTableRowCount = CompoTable.children().length;
            const initialRow = {detail: "1", display_order: compoTableRowCount, length: null, number: null}
            $(createComoTableRowEl(id, initialRow)).appendTo(CompoTable)
        }

        // 削除ボタン処理
        const deleteInput = (compId, display_order) => {
            $(`#comp-len-${compId}-${display_order}`).val("");
            $(`#comp-num-${compId}-${display_order}`).val("");
        }

        // 初期実行時にform情報がある場合のイベント
        console.log(existInfo)
        if (existInfo.length != 0) {
            for(let i=0; i < existInfo.input.length ; i++) {
                $('.component').each((index, element) => {
                    if ($(element).val() === `comp_${existInfo.input[i].id}`) {
                        $(element).prop('checked', true)
                        $("#CompForm").removeClass('hidden');
                    }
                })
                compDivExe(existInfo.input[i].id, existInfo.input[i].name)
            }
        }

        const compoClick = (id, compoName) => {

            const checkComponent = $('.component:checked').length;
            if( checkComponent === 0 ) {
                $("#CompForm").addClass('hidden');
                return;
            } else {
                $("#CompForm").removeClass('hidden');
            }

            const selectCheck = $(`#comp-check-${id}`).prop('checked');
            if( selectCheck ) {
                compDivExe(id, compoName)
            } else {
                $(`#comp-div-${id}`).addClass('hidden')
            }
  
        }

        // ---------------------------------
        // The methods used in HTML
        // ---------------------------------
        window.addForm = addForm;
        window.compoClick = compoClick;
        
     });

     
</script>

<style scoped>

    .font-bold{
        font-weight: 700;
    }

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

    .comp-frame {
        padding-top: 8px;
        padding-bottom: 8px;
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
        left: 250px;
        top: 50%;
        transform: translateY(-50%);
        color: #d0d0d0;
    }

    .a-disabled {
        pointer-events: none;
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
    .bt-tran {
        width: 130px;
    }

    .diameter-select {
        color: #ffffff;
        background-color: #3A7EBA;
    }

    .h-1\.5 {
        height: 1.5rem;
    }

    .mr-0\.5 {
        margin-right: 0.5rem;
    }

    .std-size-name,
    .std-size-select {
        width: 108px;
    }

    .page-btn {
        font-size: 14px;
        padding: 4px 32px 4px 32px;
        width: 160px;
        height: 35px;
        border-radius: 62px;
        &:hover {
            cursor: pointer;
        }
    }

    /* 入力項目の外枠 */
    .comp-div-outline {
        border: 1px solid #DADADA;
        padding: 16px;
        margin-bottom: 8px;
    }
    /* 入力項目のタイトル */
    .comp-div-title {
        border-left: 4px solid #2083D7;
        font-weight: 600;
        padding: 4px;
    }
    /* 入力項目のテーブルヘッダー */
    .input-table {
       border: 1px solid #DADADA;
       width: 100%;
    }
    /* 入力項目のテーブルヘッダー */
    .comp-div-th {
        border: 1px solid #DADADA;
        background-color: #333333;
        color: #ffffff;
        padding-left: 10px;
    }

    .w-10per {
        width: 10%
    }
    .w-40per {
        width: 40%
    }

    /* ボタン */
    .prev-btn {
        background-color: #ffffff;
        color: #000000;
        border: 1px solid #000000;
    }
    .confirm-btn {
        background-color: #53BC00;
    }
</style>
