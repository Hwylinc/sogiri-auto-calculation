{{-- English memo
    rebarGauge: 鉄筋系
--}}

{{-- menu componentのプロパティが2語以上の場合はケバブケースで書くこと --}}
<x-menu select-page="3">
    <div>
        <h1 class="text-xl">予備材一覧</h1>
    </div>

    <div class="mt-8">
        @foreach ($diameters as $diameter)
            <a 
                href="{{ route('spare.list', ['factry_id' => $diameter->id])}}" 
                class="
                    button 
                    @if($select_id == $diameter->id) select @endif
                    @if($screen === 'edit') a-disabled @endif
                "
            >
                {{ $diameter->size }}
            </a>
        @endforeach
    </div>

    <div class="h-[2px] w-full bg-black mt-4"></div>

    <form method="POST" action="{{ route('spare.complete') }}">
        @csrf
        <input type="hidden" name="select_id" value="{{ $select_id }}" />
        <div class="mt-4 flex justify-between">
            <x-message :message="session('message')" />
            @if ($screen === 'list')
                <button type="button" class="p-1 w-[108px] rounded-[64px] bg-[#D9D9D9]" onclick="location.href='{{ route('spare.edit', ['factry_id' => $select_id]) }}'">編集</button>
            @else
                <button class="p-1 w-[108px] rounded-[64px] bg-[#D9D9D9]">完了</button>
            @endif
        </div>

        <div class="flex text-center mt-4">
            @foreach ($show_spares as $spares)
                <table>
                    <tr class="border-b-2">
                        <td class="text-sm min-w-[100px] p-2 bg-[#ffffff]">優先予備材</td>
                        <td class="text-sm min-w-[100px] p-2">長さ</td>
                    </tr>
                    @foreach ($spares as $spare)
                    <tr class="border-b-2">
                        <td class="p-2 flex items-center justify-center h-[40px] bg-[#ffffff]">
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
                                ></label>
                            @else
                                　
                            @endif
                        </td>
                        <td class="p-2 h-[40px]">{{ $spare["name"] }}</td>
                    </tr>
                    @endforeach
                </table>
                
            @endforeach
        </div>
    </form>
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
    }

    .pri-border {
        border: 1px solid black;
    }

    input[type="checkbox"]:checked + label {
        background-color: black;
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

    .select {
        color: #ffffff;
        background-color: #16202E;
    }

</style>