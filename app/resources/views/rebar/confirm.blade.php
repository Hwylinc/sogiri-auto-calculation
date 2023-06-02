<x-menu select-page="1">

    <div class="flex flex-col h-full ">

        {{-- 左：メイン --}}
        <div class="left-side w-[80%] border-r-[1px]">

            {{-- タイトル --}}
            <h1 class="text-xl">鉄筋計算</h1>

            {{-- 鉄筋径選択 --}}
            <div class="mt-8">
                @foreach ($diameters as $diameter)
                    <a 
                        href="{{ route('rebar.confirm', ['diameter' => $diameter->id])}}" 
                        class="
                            button 
                            @if($page['now'] == $diameter->id) select @endif
                        "
                    >
                        {{ $diameter->size }}
                    </a>
                @endforeach
            </div>

            {{-- line --}}
            <div class="h-[2px] w-full bg-black mt-4"></div>

            {{-- メッセージ --}}
            <div class="flex justify-between">
                <x-message :message="session('message')" />
                <button type="button" id="edit-btn">編集</button>
            </div>
            

            {{-- 入力form --}}
            <form method="POST" action="{{ route('rebar.complete') }}">
                @csrf
                <input type="hidden" name="process" value="update" />
                <input type="hidden" name="select_diameter" value="{{ $page['now'] }}" />

                <div class="pr-2">

                    {{-- 部材タブ --}}
                    <div>
                        <ul class="flex ">
                            @foreach ($components as $i => $component)
                            <input 
                                type="checkbox" 
                                class="component
                                    @if( !isset($exist_info['c_' . $i]) ) hidden @endif
                                " 
                                id="comp-check-{{ $i }}" data="{{ $i }}"
                                @if( isset($exist_info['c_' . $i]) ) checked @endif
                                onchange="functions.compoClick({{ $i }}, '{{ $component }}')"
                            />
                            <label 
                                for="comp-check-{{ $i }}" 
                                class="component_name @if( !isset($exist_info['c_' . $i]) ) hidden @endif" 
                            > {{ $component }}</label>
                            @endforeach
                        </ul>
                    </div>

                    {{-- 入力formのtable --}}
                    <div id="CompForm"></div>

                </div>
            </form>
        </div>

        {{-- 右：吐き出し口 --}}
        <div class="right-side">
        </div>

    </div>

</x-menu>

<script>

    window.addEventListener('DOMContentLoaded', function(){ 

        let selectComp = 0;
        const compDetail = @json($comp_detail);
        const existInfo = @json($exist_info);
        console.log(existInfo)
        let screenMode = false; // false: 確認モード, true: 編集モード
        let error = @json($error);

        // 編集ボタンクリック時処理
        $('#edit-btn').on('click', () => {
            
            if (!screenMode) {  // screen modeを変更して編集モードにする
                changeScreen()
            } else {    // 編集内容をpost送信する
                $('form').submit()
            }
            
        })

        const changeScreen = () => {

            screenMode = true;

            $('#CompForm').empty(); // 子要素をリセット

            const reversKeys = Object.keys(existInfo).sort()
            reversKeys.forEach((key) => {
                const id = key.substr(2)
                compDivExe(id)  // form画面を再構築
            })

            // componentのcheckboxのhiddenを削除
            $('.component').each((i, el) => {
                console.log(el)
                $(el).removeClass('hidden')
            })
            // component名のhiddenを削除
            $('.component_name').each((i, el) => {
                console.log(el)
                $(el).removeClass('hidden')
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

            $('<td>', {
                'class': `${bgClass} p-1 border-r-[1px] border-[#DADADA]`,
            }).text(rowInfo.display_order).appendTo(tr)

            const tdLength = $('<td>', {
                'class': `${bgClass} px-3 border-r-[1px] border-[#DADADA] relative`,
            }).appendTo(tr)

            $('<input>', {
                type: 'number',
                name: `input[comp_${selectId}][data][${rowInfo.display_order}][length]`,
                disabled: !screenMode,
                value: rowInfo.length
            }).appendTo(tdLength)

            $('<span>', {
                'class': 'unit',
            }).text('mm').appendTo(tdLength)

            const tdNumber = $('<td>', {
                'class': `${bgClass} px-3 border-r-[1px] border-[#DADADA] relative`,
            }).appendTo(tr)

            $('<input>', {
                type: 'number',
                name: `input[comp_${selectId}][data][${rowInfo.display_order}][number]`,
                value: rowInfo.number,
            }).appendTo(tdNumber)

            $('<span>', {
                'class': 'unit',
            }).text('本').appendTo(tdNumber)

            // const tdDetil = $('<td>', {
            //     'class': `${bgClass} px-1`,
            // }).appendTo(tr)

            // $('<select>', {
            //     name: `input[comp_${selectId}][data][${rowInfo.display_order}][detail]`
            // }).append(createCompDtl).appendTo(tdDetil)

            return tr

        }

        const createCompTableEl = () => {
            const table = $('<table>', {
                'class': 'mt-8 ml-4',
            })

            const tableHead = ['NO', '長さ', '本数', '部材詳細']

            const tr = $('<tr>')

            tableHead.forEach((title) => {
                const th = $('<th>', {
                    'class': "bg-[#F0F0F0] border-r-[1px] border-[#DADADA] w-[25%]"
                }).append(title)

                $(th).appendTo(tr)
            })

            return $(table).append(tr);
        }
         

        // 部材のform枠処理
        const compDivExe = (selectId) => {
            console.log('compDiv')

            const compId =  'comp-div-' + selectId
            if ( !($(`#${compId}`).length) ) {

                const compDiv = $('<div>', {
                    id: compId,
                    'class': "comp-div bg-white p-1"
                }).appendTo('#CompForm')

                const CompoTable = $(createCompTableEl()).appendTo(compDiv)
                const hiddenCompId = $('<input>', {
                    type: 'hidden',
                    name: `input[comp_${selectId}][id]`,
                    value: selectId
                }).appendTo(compDiv)
                
                const button = $('<button>', {
                    type: 'button',
                    'class': `w-[26px] h-[26px] p-[4px] border-[2px]  flex items-center justify-center mt-4 ml-4 ${!screenMode ? 'hidden' : ''}`,
                    on: {
                        click: () => {addForm(compId)}
                    }
                }).text('＋').appendTo(compDiv)

                // データが存在するか確認
                const rows =  existInfo['c_' + selectId]['data'];
                let rowCount = rows ? Object.keys(rows).length : 10;

                if (rowCount < 10) {
                    rowCount = 10;
                }
                
                for (let i=0; i < rowCount; i++) {
                    const rowInfo = rows && rows[i]  ? rows[i] : {detail: "1", display_order: i+1, length: null, number: null}
                    $(createComoTableRowEl(rowInfo, selectId)).appendTo(CompoTable)
                }
                console.log('compDiv done')
                return 
            } 

                // $(`#${compId}`).removeClass('hidden')


        }

        // プラスボタン押下時にformを追加する
        const addForm = (compId) => {
            const CompoTable = $(`#${compId} table`)
            const compoTableRowCount = CompoTable.children().length;
            const initialRow = {detail: "1", display_order: compoTableRowCount, length: null, number: null}
            $(createComoTableRowEl(initialRow, compId)).appendTo(CompoTable)
        }

        // 要素の処理
        const makeFormEl = (selectId) => {
            selectComp = selectId
            compDivExe(selectId)
        }

        // 初期実行時にform情報がある場合のイベント
        if (existInfo.length != 0) {
            console.log(existInfo)
            // 後ろから作成し、若い番号が先頭に来るよにする
            const reversKeys = Object.keys(existInfo).sort()
            reversKeys.forEach((key) => {
                const id = key.substr(2)
                console.log(id)
                // $(`#comp-tab-${id}`).removeAttr('disabled');
                makeFormEl(id)
            })

            // エラーの場合はscreen modeを切り替える
            if (error === "1") {
                changeScreen()
            }
        }
        
        // 部材タブクリック時イベント
        $(document).on('click', ".component:not([disabled])", (e) => {
            const selectId = $(e.target).attr('data')
            makeFormEl(selectId)
        })

        // ---------------------------------
        // The methods used in HTML
        // ---------------------------------
        window.addForm = addForm; 

    });

</script>

<style scoped>

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

    .button {
        width: 100px;
        text-align: center;
        display: inline-block;
        padding: 8px 16px;
        color: #16202E;
        font-size: 16px;
        cursor: pointer;
        border-radius: 100px;
        border: 2px solid #16202E;
    }
    .bt-tran {
        width: 130px;
    }

    .select {
        color: #ffffff;
        background-color: #16202E;
    }

</style>