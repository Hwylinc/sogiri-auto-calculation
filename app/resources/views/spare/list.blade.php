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
                        <button class="p-1 w-[108px] bg-[#53BC00] text-white">完了</button>
                    @endif
                </div>
                
            </div>

            <div class="h-[2px] w-full bg-[#E3E7ED] mt-4"></div>

            <input type="hidden" name="select_id" value="{{ $select_id }}" />
            
            <x-message :message="session('message')" />

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
    
</x-menu>

<style scoped>

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

</style>