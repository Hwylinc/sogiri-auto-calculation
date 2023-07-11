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
                        <div class="mr-2">
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
                            @foreach ($components as $index => $component)
                            <div class="mr-2">
                                <input 
                                    name="component[]" 
                                    type="checkbox" 
                                    class="component mr-2" 
                                    id="comp-check-{{ $component['id'] }}" data="{{ $component['id'] }}" 
                                    value="comp_{{ $component['id'] }}"
                                    onchange="compoClick({{ $index }}, {{ $component['id'] }}, '{{ $component['name'] }}')"
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
                                    D{{ $page['next']->size . "へ進む" }}
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
        <x-calculation_info :select-info=$select_info />
    </div>
<div>
</x-menu>

<script src="{{ asset('/js/common.js') }}"></script>
<script>
    const functions = {}
    // $fucntionで読めないため、以下で実行
    window.addEventListener('DOMContentLoaded', function(){

        const existInfo = @json($exist_info);

        // 部材のform枠処理
        const makeFormEl = (index, id, name, inputData=[]) => {

            const compId =  'comp-div-' + index

            if ( !($(`#${compId}`).length) ) {
                
                const compDiv = createComponentFormDiv(compId)
                setComponentName(name, compDiv)
                const CompoTable = $(createCompTableEl()).appendTo(compDiv)
                createComponentIdHidden(id, compDiv)
                createComponentNameHidden(id, name, compDiv)

                const button = createAddBtn(compId, id, createComoTableRowEl, compDiv, "")

                let rowCount = getRowCount(inputData)
                
                for (let i=0; i < rowCount; i++) {
                    const rowInfo = inputData.length !== 0 && inputData[i+1]  ? inputData[i+1] : getInitialFormData(i+1)
                    $(createComoTableRowEl(id, rowInfo)).appendTo(CompoTable)
                }

                return;
            } 

            $(`#${compId}`).removeClass('hidden')
        }

        const createCompTableEl = () => {

            const table = createFormTableEl();
            const tableHead = getFormTableTitle()
            const tr = $('<tr>')

            tableHead.forEach((title) => {
                let wClass = "w-40%"

                if (title === "NO" || title === "削除") {
                    wClass = "w-10%"
                } 

                const th = createFormTableHeadEl(wClass, title);

                $(th).appendTo(tr)
            })

            return $(table).append(tr);
        }

        const createComoTableRowEl = (id, rowInfo) => {
            const bgClass = getRowBackGroundColor(rowInfo.display_order); 
            const tr = $('<tr>')
            
            // Noの値をhiddenで作成
            createComponentInputHiddenEl(id, rowInfo.display_order, rowInfo.display_order, 'display_order', tr)

            // Noの作成
            const order = createZeroForth(rowInfo.display_order)
            createTdNo(bgClass, order, tr)

            // 長さの作成
            const tdLength = createTd(bgClass, tr)
            createInputNumberEl(id, rowInfo.display_order, rowInfo.length, tdLength, 'length')
            createUnitSpanEl('mm', tdLength)

            // 本数の作成
            const tdNumber = createTd(bgClass, tr)
            createInputNumberEl(id, rowInfo.display_order, rowInfo.number, tdNumber, 'number')
            createUnitSpanEl('本', tdNumber)

            // 削除
            const tdDelete = createTd(bgClass, tr, 'text-center')
            createRemoveBtnIcon("{{ asset("images/delete.svg") }}", id, rowInfo.display_order, tdDelete)

            return tr

        }

        // 初期実行時にform情報がある場合のイベント
        if (existInfo.length != 0) {
            existInfo.input.forEach((row) => {
                $('.component').each((index, element) => {
                    if ($(element).val() === `comp_${row.id}`) {
                        $(element).prop('checked', true)
                        $("#CompForm").removeClass('hidden');
                        makeFormEl(index, row.id, row.name, row.data)
                    }
                })
            })
        }

        const compoClick = (index, id, compoName) => {

            const selectCheck = $(`#comp-check-${id}`).prop('checked');
            if( selectCheck ) {
                makeFormEl(index, id, compoName)

                // 中身要素をid順に並び替える
                const parent = $('#CompForm')
                const children = parent.children()

                children.sort((a,b) => {
                    var idA = $(a).attr('id');
                    var idB = $(b).attr('id');
                    return idA.localeCompare(idB); // localeCompareを使って文字列としてidを比較する
                })

                parent.empty().append(children);

            } else {
                $(`#comp-div-${index}`).addClass('hidden')
            }

            const checkComponent = $('.component:checked').length;
            if( checkComponent === 0 ) {
                $("#CompForm").addClass('hidden');
                return;
            } else {
                $("#CompForm").removeClass('hidden');
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

</style>
