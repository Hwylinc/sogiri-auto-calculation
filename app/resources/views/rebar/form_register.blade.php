<x-menu select-page="1">

    <div class="flex flex-col h-full ">
        <div class="left-side w-[80%] border-r-[1px]">
            <h1 class="text-xl">鉄筋計算</h1>

            <div class="mt-8">
                @foreach ($diameters as $diameter)
                    <a 
                        href="{{ route('spare', ['screen' => 'list', 'select_id' => $diameter->id])}}" 
                        class="
                            button 
                            @if($page['now'] == $diameter->id) select @endif
                            a-disabled
                        "
                    >
                        {{ $diameter->size }}
                    </a>
                @endforeach
            </div>

            <div class="h-[2px] w-full bg-black mt-4"></div>



            <form method="POST" action="{{ route('rebar.store') }}">
                @csrf
                <x-message :message="session('message')" />
                <div class="pr-2">
                    <div>
                        <ul class="flex ">
                            @foreach ($components as $i => $component)
                                <li id="comp-tab-{{ $i }}" class="component" data="{{ $i }}">{{ $component }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <div id="CompForm">
                    </div>

                    @if (array_key_exists('prev', $page))
                        <button type="submit" name="action" value="{{ $page['prev']->id }}">{{ $page['prev']->size }}</button>
                    @endif

                    <input type="hidden" name="select_diameter" value="{{ $page['now'] }}" />
                    <button type="submit" name="action" value="{{ $page['next']["id"] }}">
                        {{ $page['next']["id"] !== -1 ? $page['next']->size . "に進む" : '入力情報確認' }}
                    </button>
                    
                </div>
            </form>
        </div>

        <div class="right-side">
        </div>

    </div>

</x-menu>

<script>
    // $fucntionで読めないため、以下で実行
    window.addEventListener('DOMContentLoaded', function(){

        let selectComp = 0;
        const compDetail = @json($comp_detail);
        const existInfo = @json($exist_info);

        // 部材のform枠処理
        const compDivExe = (selectId) => {
            $('.comp-div').addClass('hidden')

            const compId =  'comp-div-' + selectId
            if ( !($(`#${compId}`).length) ) {

                const compDiv = $('<div>', {
                    id: compId,
                    'class': "comp-div bg-white p-1"
                }).appendTo('#CompForm')

                const CompoTable = $(createCompTableEl()).appendTo(compDiv)
                const hiddenCompId = $('<input>', {
                    type: 'hidden',
                    name: `input[comp_${selectComp}][id]`,
                    value: selectComp
                }).appendTo(compDiv)

                const button = $('<button>', {
                    type: 'button',
                    'class': 'w-[26px] h-[26px] p-[4px] border-[2px]  flex items-center justify-center mt-4 ml-4',
                    on: {
                        click: () => {addForm(compId)}
                    }
                }).text('＋').appendTo(compDiv)

                let existInfoSelect = [];

                if (existInfo.length !== 0) {
                    console.log(existInfo)
                    existInfo.input.forEach((array) => {
                        if (array.id == selectId) {
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
                    $(createComoTableRowEl(rowInfo)).appendTo(CompoTable)
                }

                return;
            } 

            $(`#${compId}`).removeClass('hidden')
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

        const createComoTableRowEl = (rowInfo) => {
            console.log('createComoTableRowEl')
            const bgClass = rowInfo.display_order % 2 === 0 ? "main-bg-color" : '' ; 

            const tr = $('<tr>')
            
            $('<input>', {
                type: 'hidden',
                name: `input[comp_${selectComp}][data][${rowInfo.display_order}][display_order]`,
                value: rowInfo.display_order
            }).appendTo(tr)

            $('<td>', {
                'class': `${bgClass} p-1 border-r-[1px] border-[#DADADA]`,
            }).text(rowInfo.display_order).appendTo(tr)

            const tdLength = $('<td>', {
                'class': `${bgClass} px-3 border-r-[1px] border-[#DADADA] relative`,
            }).appendTo(tr)

            $('<input>', {
                type: 'number',
                name: `input[comp_${selectComp}][data][${rowInfo.display_order}][length]`,
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
                name: `input[comp_${selectComp}][data][${rowInfo.display_order}][number]`,
                value: rowInfo.number,
            }).appendTo(tdNumber)

            $('<span>', {
                'class': 'unit',
            }).text('本').appendTo(tdNumber)

            const tdDetil = $('<td>', {
                'class': `${bgClass} px-1`,
            }).appendTo(tr)

            $('<select>', {
                name: `input[comp_${selectComp}][data][${rowInfo.display_order}][detail]`
            }).append(createCompDtl).appendTo(tdDetil)

            return tr

        }

        // プラスボタン押下時にformを追加する
        const addForm = (compId) => {
            const CompoTable = $(`#${compId} table`)
            const compoTableRowCount = CompoTable.children().length;
            const initialRow = {detail: "1", display_order: compoTableRowCount, length: null, number: null}
            $(createComoTableRowEl(initialRow)).appendTo(CompoTable)
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

        const makeFormEl = (selectId) => {
            $('.component').removeClass('bg-white')
            selectComp = selectId
            compDivExe(selectId)
            $(`#comp-tab-${selectId}`).addClass('bg-white')
        }

        // 初期実行時にform情報がある場合のイベント
        if (existInfo.length != 0) {
            for(let i = existInfo.input.length - 1; i >= 0; i--) {
                makeFormEl(existInfo.input[i].id)
            }
        }
        
        // 部材タブクリック時イベント
        $('.component').on('click', function() {
            const selectId = $(this).attr('data')
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
