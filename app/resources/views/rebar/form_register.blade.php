<x-menu select-page="1">

    {{-- title --}}
    <x-head title="鉄筋計算" imageFlg="1"></x-head>

    {{-- 直径/定尺寸法 --}}
    <div>
        {{-- 直径 --}}
        <div class="mt-4 flex">
            @foreach ($diameters as $diameter)
                <div class="mr-0.5">
                    <p class="text-center h-1.5">@if($page['now'] == $diameter->id) 現在の直径 @endif</p>
                    <a 
                        class="
                            button 
                            @if($page['now'] == $diameter->id) select @endif
                            a-disabled
                        "
                    >
                        D{{ $diameter->size }}
                    </a> 
                </div>
            @endforeach
        </div>
        {{-- 定尺寸法 --}}
        <div class="relative">
            <div>
                <label for="std-size-name">定尺寸法</label> 
                <input type="text" name="" id="std-size-name" class="std-size-name">
                {{-- https://qiita.com/7note/items/86253752adfb95e9bf47 --}}
            </div>
            <select name="" id="std-size-select" size="5" class="std-size-select hidden absolute" style="z-index: 1">
                @foreach ($std_size as $size)
                <option value="{{ $size }}">{{ $size }}</option>
                @endforeach
            </select>
        </div>
    </div>
    

            <div class="h-[2px] w-full bg-black mt-4"></div>

        <div class="left-side w-[80%] border-r-[1px]">
            <form method="POST" action="{{ route('rebar.store') }}">
                @csrf
                <x-message :message="session('message')" />
                <div class="pr-2">
                    <div>
                        @foreach ($components as $i => $component)
                            <input 
                                name="component[]" 
                                type="checkbox" 
                                class="component" 
                                id="comp-check-{{ $i }}" data="{{ $i }}" 
                                value="comp_{{ $i }}"
                                onchange="compoClick({{ $i }}, '{{ $component }}')"
                            >{{ $component }}
                        @endforeach
                    </div>

                    <div id="CompForm">
                    </div>

                    @if (array_key_exists('prev', $page))
                        <button type="submit" name="action" value="{{ $page['prev']->id }}">{{ $page['prev']->size }}</button>
                    @endif

                    <input type="hidden" name="select_diameter" value="{{ $page['now'] }}" />
                    <button type="submit" name="action" value="{{ $page['next']["id"] }}">
                        {{ $page['next']["id"] !== -1 ? $page['next']->size . "に進む" : '確認に進む' }}
                    </button>
                </div>
            </form>
        </div>

        

        <div class="right-side">
        </div>

</x-menu>

<script>
    const functions = {}
    // $fucntionで読めないため、以下で実行
    window.addEventListener('DOMContentLoaded', function(){

        let selectComp = 0;
        const compDetail = @json($comp_detail);
        const existInfo = @json($exist_info);

        // 部材のform枠処理
        const compDivExe = (id, name) => {
            // $('.comp-div').addClass('hidden')

            const compId =  'comp-div-' + id
            if ( !($(`#${compId}`).length) ) {
                
                // ① make outline
                const compDiv = $('<div>', {
                    id: compId,
                    'class': "comp-div bg-white p-1"
                }).appendTo('#CompForm')

                // ② make title
                $('<p>').text(name).appendTo(compDiv);
                
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
                    console.log(existInfo.input)
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
                'class': 'mt-8 ml-4',
            })

            const tableHead = ['NO', '長さ', '本数', '削除']

            const tr = $('<tr>')

            tableHead.forEach((title) => {
                const th = $('<th>', {
                    'class': "bg-[#F0F0F0] border-r-[1px] border-[#DADADA] w-[25%]"
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

            $('<td>', {
                'class': `${bgClass} p-1 border-r-[1px] border-[#DADADA]`,
            }).text(rowInfo.display_order).appendTo(tr)

            const tdLength = $('<td>', {
                'class': `${bgClass} px-3 border-r-[1px] border-[#DADADA] relative`,
            }).appendTo(tr)

            $('<input>', {
                type: 'number',
                name: `input[comp_${id}][data][${rowInfo.display_order}][length]`,
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
                name: `input[comp_${id}][data][${rowInfo.display_order}][number]`,
                value: rowInfo.number,
            }).appendTo(tdNumber)

            $('<span>', {
                'class': 'unit',
            }).text('本').appendTo(tdNumber)

            // const tdDetil = $('<td>', {
            //     'class': `${bgClass} px-1`,
            // }).appendTo(tr)

            // $('<select>', {
            //     name: `input[comp_${id}][data][${rowInfo.display_order}][detail]`
            // }).append(createCompDtl).appendTo(tdDetil)

            return tr

        }

        // プラスボタン押下時にformを追加する
        const addForm = (compId, id) => {
            const CompoTable = $(`#${compId} table`)
            const compoTableRowCount = CompoTable.children().length;
            const initialRow = {detail: "1", display_order: compoTableRowCount, length: null, number: null}
            $(createComoTableRowEl(id, initialRow)).appendTo(CompoTable)
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

        const makeFormEl = (id, name) => {
            // $('.component').removeClass('bg-white')
            selectComp = id
            compDivExe(id, name)
            // $(`#comp-tab-${selectId}`).addClass('bg-white')
        }

        // 初期実行時にform情報がある場合のイベント
        if (existInfo.length != 0) {
            for(let i=0; i < existInfo.input.length ; i++) {
                makeFormEl(existInfo.input[i].id, existInfo.input[i].name)
            }
        }

        const compoClick = (id, compoName) => {
            const selectCheck = $(`#comp-check-${id}`).prop('checked');
            if( selectCheck ) {
                makeFormEl(id, compoName)
            } else {
                $(`#comp-div-${id}`).addClass('hidden')
            }
        }

        // ---------------------------------
        // 定尺寸法
        // ---------------------------------
        $('#std-size-name').on('click', function() {
            $('#std-size-select').removeClass('hidden');
        })

        $('#std-size-select').on('change', function() {
            const item =  $(this).val()
            $('#std-size-name').val(item)
            $(this).addClass('hidden')
        })

        // ---------------------------------
        // The methods used in HTML
        // ---------------------------------
        window.addForm = addForm;
        window.compoClick = compoClick;
        
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

</style>
