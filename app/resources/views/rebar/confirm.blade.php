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

            {{-- 入力form --}}
            <form method="POST" action="{{ route('rebar.store') }}">
                @csrf
                <input type="hidden" name="process" value="update" />
                <input type="hidden" name="select_diameter" value="{{ $page['now'] }}" />

                <div>

                    {{-- 部材タブ --}}
                    <div id="comp-frame" class="comp-frame confirm-frame bg-white flex w-full mb-3 mt-3 hidden">
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
                    <div id="CompForm" class="comp-form confirm-frame bg-white p-4 hidden"></div>

                    <div class="flex justify-center mt-4">

                        <input type="hidden" name="select_diameter" value="{{ $page['now'] }}" />

                        {{-- 戻るボタン --}}
                        @if (array_key_exists('prev', $page))
                            <a 
                                href="{{ route('rebar.confirm', ['diameter' => $page['prev']->id])}}"
                                class="page-btn prev-btn"
                                id="prev-btn"
                            >
                                D{{ $page['prev']->size }}へ戻る
                            </a>
                        @endif

                        @if ($page['next']["id"] !== -1)
                            <a 
                                href="{{ route('rebar.confirm', ['diameter' => $page['next']->id])}}"
                                class="page-btn black-btn"
                                id="next-btn"
                            >
                                D{{ $page['next']->size }}へ進む
                            </a>
                        @else
                            <a 
                                href="{{ route('rebar.done') }}"
                                class="page-btn confirm-btn"
                                id="done"
                            >
                                鉄筋情報を登録
                            </a>
                        @endif
                    </div>

                </div>
            </form>
        </div>

        {{-- 右サイド --}}
        <x-calculation_info :select-info=$select_info />

    </div>
</div>
</x-menu>

<script src="{{ asset('/js/common.js') }}"></script>
<script>

    window.addEventListener('DOMContentLoaded', function(){ 

        const existInfo = @json($exist_info);
        let screenMode = false; // false: 確認モード, true: 編集モード
        let error = @json($error);

        // 編集ボタンクリック時処理
        $('#edit-btn').on('click', (e) => {
            // クリック処理を停止する
            e.preventDefault();
            
            // 確認モードか編集モードによって処理を行う
            if (!screenMode) {
                // 編集モードに切り替える準備を行う
                $('#prev-btn').addClass('disabled-a')
                $('#next-btn').addClass('disabled-a')
                $('#done').addClass('disabled-a')
                changeScreen()
            } else { 
                // 編集内容をpost送信する   
                $('form').submit()
            }
            
        })

        // 編集モードに切り替える処理
        const changeScreen = () => {
            // フラグを編集モードに切り替える
            screenMode = true;
            
            // 子要素をリセット
            $('#CompForm').empty(); 

            // textをinputに切り替える為、再度要素を作成
            existInfo.input.forEach((row) => {
                makeFormEl(row.id, row.name, row.data)
            })

            // ボタンのテキストを変更
            $('#edit-btn').text('完了')

        }

        const createComoTableRowEl = (selectId, rowInfo) => {
            const bgClass = getRowBackGroundColor(rowInfo.display_order);  
            const tr = $('<tr>')
            
            // // Noの値をhiddenで作成
            createComponentInputHiddenEl(selectId, rowInfo.display_order, rowInfo.display_order, 'display_order', tr)

            // Noの作成
            const order = createZeroForth(rowInfo.display_order)
            createTdNo(bgClass, order, tr)
            
            const tdLength = createTd(bgClass, tr)

            // 長さ
            if( screenMode ) {
                createInputNumberEl(selectId, rowInfo.display_order, rowInfo.length, tdLength, 'length')
            } else {
                createTextNumberEl(rowInfo.length, tdLength)
            }

            createUnitSpanEl('mm', tdLength)

            const tdNumber = createTd(bgClass, tr)

            if( screenMode ) {
                createInputNumberEl(selectId, rowInfo.display_order, rowInfo.number, tdNumber, 'number')
            } else {
                createTextNumberEl(rowInfo.number, tdNumber)
            }

            createUnitSpanEl('本', tdNumber)

            // 削除
            if ( screenMode ) {
                const tdDelete = createTd(bgClass, tr, 'text-center')
                createRemoveBtnIcon("{{ asset("images/delete.svg") }}", selectId, rowInfo.display_order, tdDelete)
            }
            
            return tr

        }

        const createCompTableEl = () => {

            const table = createFormTableEl();
            const tableHead = getFormTableTitle();
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

                const th = createFormTableHeadEl(wClass, title, hiddenClass);

                $(th).appendTo(tr)
            })

            return $(table).append(tr);
        }
         

        // 部材のform枠処理
        const makeFormEl = (selectId, componentName, inputData=[]) => {

            const compId =  'comp-div-' + selectId
            if ( !($(`#${compId}`).length) ) {

                const compDiv = createComponentFormDiv(compId)

                $('<input>', {
                    type: 'hidden',
                    name: `component[]`,
                    value: 'comp_' + selectId
                }).appendTo('#CompForm')

                setComponentName(componentName, compDiv)
                createComponentNameHidden(selectId, componentName, compDiv)
                const CompoTable = $(createCompTableEl()).appendTo(compDiv)
                createComponentIdHidden(selectId, compDiv)

                const button = createAddBtn(compId, selectId, createComoTableRowEl, compDiv, !screenMode ? ' hidden' : '')

                // データが存在するか確認
                let rowCount = getRowCount(inputData)
                
                for (let i=0; i < rowCount; i++) {
                    const rowInfo = inputData.length !== 0 && inputData[i+1]  ? inputData[i+1] : getInitialFormData(i+1)
                    $(createComoTableRowEl(selectId, rowInfo)).appendTo(CompoTable)
                }
                return 
            } 
        }

        // 初期実行時にform情報がある場合のイベント
        if (existInfo.length != 0) {
            existInfo.input.forEach((row) => {
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

    .button.edit-btn {
        display: flex;
        padding: 4px;
        align-items: center;
        justify-content: center;
        background-color: #53BC00;
        color: #ffffff;
        height: 32px;
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