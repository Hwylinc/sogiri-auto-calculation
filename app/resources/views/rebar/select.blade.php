<x-menu select-page="1">

    <div class="flex justify-center items-center w-full h-5/6">

        <form method="POST" action="{{ route('rebar.select-store') }}">
            @csrf
            <div class="mb-1 text-center flex justify-around">
                @foreach(config('const.factory_id') as $id => $name)
                    <div>
                        <input 
                            type="radio" 
                            name="factory_id" 
                            id="" 
                            value="{{ $id }}" 
                            class="mr-2"
                            {{ $id === $factory_checked ? "checked" : ''}}
                        >{{ $name }}
                    </div>
                @endforeach
            </div>
            <div class="mb-1">
                <label for="client_name" class="form-label">メーカー</label>
                <input 
                    list="client_names" 
                    name="client_name" 
                    id="client_name" 
                    placeholder="メーカー名" 
                    class="@error('client_name') is-invalid @enderror" 
                    value="{{ old('client_name') }}"
                >
                <input 
                    type="hidden" 
                    name="client_id" 
                    value="{{ old('client_id') }}" 
                    id="client_id"
                >
                <datalist id="client_names">
                    @foreach($clients as $client)
                    <option value="{{ $client['name'] }}" data-id="{{ $client['id'] }}"></option>
                    @endforeach
                </datalist>
                @error('client_name')
                     <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-2">
                <label for="house_name" class="form-label" >邸名</label>
                <input 
                    type="text" 
                    name="house_name" 
                    id="house_name" 
                    placeholder="邸名" 
                    class="@error('house_name') is-invalid @enderror" 
                    value="{{ old('house_name') }}"
                >
                @error('house_name')
                     <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="text-center">
                <input type="submit" value="計算へ進む" class="button">
            </div>
            
    
        </form>

    </div>
    
</x-menu>

<script>
window.addEventListener('DOMContentLoaded', function(){
    
    // ---------------------------------------------
    // メーカ名が選択された時にhiddenにidを保存する
    // ---------------------------------------------
    $('#client_name').on('change', function() {
        const id = $("#client_names option[value='" + $(this).val() + "']").data('id');
        $('#client_id').val(id)
    })

})
</script>

<style scoped lang="scss">
    .form-label {
        display: inline-block;
        width: 88px;
    }

    .mb-1 {
        margin-bottom: 1rem;
    }

    .mb-2 {
        margin-bottom: 2rem;
    }

    .h-5\/6 {
        height: 83.333333%;
    }

    .justify-around {
        justify-content: space-around;
    }

    .button {
        background-color: black;
        font-size: 14px;
        padding: 4px 32px 4px 32px;
        color: #ffffff;
        border-radius: 2px;
        &:hover {
            cursor: pointer;
        }
    }
</style>