{{-- English memo
    rebarGauge: 鉄筋系
--}}

{{-- menu componentのプロパティが2語以上の場合はケバブケースで書くこと --}}
<x-menu select-page="3">
   {{-- title --}}
   <x-head title="優先予備材" imageFlg="3"></x-head>

   <div class="card">
        <form method="POST" action="{{ route('spare.complete') }}">
            @csrf
            <div class="flex items-center justify-between">
                <div>
                    <label class="mr-4 font-semibold">鉄筋径</label>
                    @foreach ($diameters as $diameter)
                        <a 
                            href="{{ route('spare.list', ['factry_id' => $diameter->id])}}" 
                            class="
                                button 
                                @if($select_id == $diameter->id) rebar-select @endif
                                @if($screen === 'edit') a-disabled @endif
                            "
                        >
                            D{{ $diameter->size }}
                        </a>
                    @endforeach
                </div>
                <div>
                    @if ($screen === 'list')
                        <button type="button" class="p-1 w-[108px] bg-[#53BC00] text-white" onclick="location.href='{{ route('spare.edit', ['factry_id' => $select_id]) }}'">編集</button>
                    @else
                        <button id="select_complete" class="p-1 w-[108px] bg-[#53BC00] text-white">選択完了</button>
                    @endif
                </div>
                
            </div>

            <div class="h-[2px] w-full bg-[#E3E7ED] mt-4"></div>

            <input type="hidden" name="select_id" value="{{ $select_id }}" />
            
            <x-message :message="session('message')" />
            <div id="js-error" class="js-error hidden"></div>

            <div class="flex text-center mt-4">
                @foreach ($show_spares as $spares)
                    <table class="mr-2">
                        <tr>
                            <td class="text-sm min-w-[100px] p-2 bg-[#6D6D6D] text-white border-r-[#DADADA] border-r-1">優先予備材</td>
                            <td class="text-sm min-w-[100px] p-2 bg-[#6D6D6D] text-white">長さ</td>
                        </tr>
                        @foreach ($spares as $spare)
                        <tr class="border-b-1 border-r-[#DADADA] spare-detail">
                            <td class="p-2 flex items-center justify-center h-[40px] border-l-1 border-r-1 ">
                                @if ($spare['id'] !== 999999999)
                                    <input 
                                        id="priFlg-{{$spare["id"]}}" 
                                        type="checkbox"
                                        {{ 
                                            (is_array(old("priority"))) 
                                                ? ((in_array($spare["id"], old("priority"))))
                                                    ? 'checked'
                                                    : ''
                                            : (($spare['priority_flg']) 
                                                ? 'checked' 
                                                : ''
                                            ) 
                                        }}
                                        {{ $screen === 'list' ? 'disabled' : '' }}
                                        name='priority[]'
                                        value="{{ $spare["id"] }}"
                                        data-name="{{ $spare["name"] }}"
                                    >
                                    <label 
                                        for="priFlg-{{$spare["id"]}}" 
                                        class="pri-flag {{ $screen === 'edit' ? 'pri-border' : '' }}"
                                    >
                                        <div>

                                        </div>
                                    </label>
                                    {{-- <div class="flex items-center mr-2">
                                        <div class="select-check-flame">
                                            <div class="select-check-bk"></div>
                                        </div>
                                    </div> --}}
                                @else
                                    　
                                @endif
                            </td>
                            <td class="p-2 h-[40px] border-r-1">{{ $spare["name"] }}</td>
                        </tr>
                        @endforeach
                    </table>
                    
                @endforeach
            </div>
        </form>
   </div>

   <div id="modal" class="modal hidden">
        <div class="card">
            <form method="POST" action="{{ route('spare.complete') }}">
                @csrf
                <p class="card_title">優先予備材順に並び替えてください</p>
                <div class="flex justify-center bg-white p-2 mb-4">
                    <div id="order-number" class="order-number">
                        
                    </div>
                    <ul class="priority_order">

                    </li>
                </div>
                <div class="flex justify-center">
                    <button class="p-1 w-[96px] bg-[#53BC00] text-white complete-radius">完了</button>
                </div>
                <div id="send-hiddin">
                    
                </div>
                <input type="hidden" name="select_id" value="{{ $select_id }}" />
            </form>
        </div>
   </div>

    
</x-menu>

<script type="text/javascript">
$(function() {

    $('#select_complete').on('click', function(e) {
        e.preventDefault();
        const checkItem = $(':checkbox[name="priority[]"]:checked')
        const errorElement = $('#js-error');
        if( !checkItem.length ) {
            errorElement.append('<p>選択されていません</p>')
            errorElement.removeClass('hidden')
            return;
        }

        if( checkItem.length > 5 ) {
            errorElement.append('<p>5個以上は選択できません</p>')
            errorElement.removeClass('hidden')
            return;
        } 

        const ulElement = $('.priority_order')
        const orderNumber = $('#order-number')
        $('#send-hiddin').empty();
        checkItem.each((index, element) => {
            const pElemetn = $(`<p class="modal-number">${index+1}</p>`)
            const liElement = $(`<li class="modal-length" id="${$(element).val()}">${$(element).data('name')}</li>`)

            orderNumber.append(pElemetn)
            ulElement.append(liElement)

            const hiddenElement = $(`<input type="hidden" name="priority[${index+1}]" value="${$(element).val()}">`)
            $('#send-hiddin').append(hiddenElement)
        })

        $('#modal').removeClass('hidden')
    })

    $('.priority_order').sortable({
        update: function() {
            const order = $('.priority_order').sortable("toArray");
            $('#send-hiddin').empty();
            order.forEach((id, index) => {
                const hiddenElement = $(`<input type="hidden" name="priority[${index+1}]" value="${id}">`)
                $('#send-hiddin').append(hiddenElement)
            });
        }
    });
        
});
</script>

<style scoped>

    .js-error {
        padding: 4px;
        padding-left: 16px;
        background-color: #ffa1a1;
        color: #ba0000;
    }

    input[type="checkbox"] {
        display: none;
    }

    .a-disabled {
        pointer-events: none;
    }

    .pri-flag {
        display: inline-block;
        width: 16px;
        height: 16px;
        padding: 1px;
    }

    .pri-border {
        border: 1px solid #DADADA;
    }

    input[type="checkbox"]:checked + label {
        border: 1px solid #DADADA;
    }

    input[type="checkbox"]:checked + label  div {
        width: 12px;
        height: 12px;
        background-color: #2083D7;
    }

    .button {
        width: 100px;
        text-align: center;
        display: inline-block;
        padding: 8px 16px;
        color: #989898;
        font-size: 16px;
        cursor: pointer;
        border-radius: 100px;
        margin-right: 8px;
        background-color: #DADADA;
    }

    .rebar-select {
        color: #ffffff;
        background-color: #3A7EBA;
    }

    .card {
        background-color: #ffffff;
        padding: 24px;
    }

    .spare-detail:nth-child(odd) {
        background-color: #F4F6FA;
    }

    .modal {
        width: 100%;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.8); /* 子要素に反映させたくないためrgbaを使用 */
        position: absolute;
        top: 0;
        left: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal .card {
        background-color: #F4F6FA;
        width: 30%;
        border-radius: 8px;
        font-size: 20px;
        margin-bottom: 16px;
    }

    .modal .modal-length {
        border: 1px solid #DADADA;
        margin-bottom: 8px;
        padding: 2px;
        padding-left: 8px;
        border-radius: 4px;
        background-color: #8bb1d2;
        color: #ffffff;
    }

    .modal .modal-number {
        margin-bottom: 8px;
        height: 36px;
    }

    .card_title {
        text-align: center;
        margin-bottom: 16px;
    }

    .order-number {
        width: 15%;
    }

    .priority_order {
        width: 75%;
    }

    .complete-radius {
        border-radius: 4px;
    }

    /* 最後に記載するようにすること */
    .hidden {
        display: none;
    }

</style>