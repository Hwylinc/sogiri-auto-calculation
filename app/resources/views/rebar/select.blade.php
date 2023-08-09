<x-menu select-page="1">

    {{-- title --}}
    <x-head title="手動入力" imageFlg="1"></x-head>

    <div class="flex justify-center items-center w-full h-90 bg-white">

        <form method="POST" action="{{ route('rebar.select-store') }}">
            @csrf
            {{-- <div class="mb-4 text-center flex justify-around">
                @foreach(config('const.factory_id') as $id => $name)
                    <div>
                        <input 
                            type="radio" 
                            name="factory_id" 
                            id="" 
                            value="{{ $id }}" 
                            class="mr-2"
                            {{ $id === $factory_checked ? "checked" : 'disabled'}}
                        >{{ $name }}
                    </div>
                @endforeach
            </div> --}}
            <hr class="mt-1 mb-8">
            <div class="mb-4">
                <label for="client_name" class="form-label">メーカー</label>
                <input 
                    type="text" 
                    name="client_name" 
                    id="client_name" 
                    placeholder="メーカーを記入してください" 
                    class="@error('client_name') is-invalid @enderror w-250px client_name" 
                    value="{{ old('client_name') }}"
                >
                {{-- <select id="client_select" name="client_id" class="w-250px">
                    <option value="" disabled selected style="display:none;">メーカーを選択してください</option>
                    @foreach($clients as $client)
                    <option value="{{ $client['id'] }}" data-id="{{ $client['id'] }}">{{ $client['name'] }}</option>
                    @endforeach
                </select> --}}
                @error('client_name')
                     <div class="alert alert-danger text-center text-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-8">
                <label for="house_name" class="form-label" >邸名</label>
                <input 
                    type="text" 
                    name="house_name" 
                    id="house_name" 
                    placeholder="邸名を記入してください" 
                    class="@error('house_name') is-invalid @enderror w-250px house_name" 
                    value="{{ old('house_name') }}"
                >
                @error('house_name')
                     <div class="alert alert-danger text-center text-error">{{ $message }}</div>
                @enderror
            </div>
            
            <hr class="mt-2 mb-4">

            <div class="text-center">
                <input type="submit" value="計算へ進む" class="button">
            </div>
            
    
        </form>

    </div>
    
</x-menu>

<script>
// メーカ名が入力式になったのコメントアウト(23/08/09 浦野)
// window.addEventListener('DOMContentLoaded', function(){

//     $(document).ready(function() {
//         $('#client_select').select2()
//     })

//     $('#client_select').on('change', function(){
//         if($(this).val() == "placeholder"){
//             $('#select2-client_select-container').css('color','#9ca3af')
//         } else {
//             $('#select2-client_select-container').css('color','#333')
//         }
//     });

// })
</script>

<style scoped lang="scss">
    .form-label {
        display: inline-block;
        width: 88px;
    }

    .button {
        background-color: black;
        font-size: 14px;
        padding: 4px 32px 4px 32px;
        color: #ffffff;
        width: 280px;
        height: 32px;
        border-radius: 62px;
        &:hover {
            cursor: pointer;
        }
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered { 
        color: #9ca3af; 
        
    }

    .select2-container--default .select2-selection--single {
        background-color: #F9F9F9;
    }

    .house_name, .client_name {
        padding: 2px;
        padding-left: 6px;
        border: 1px solid #aaacb0;
        border-radius: 4px;
        height: 26px;
        background-color: #F9F9F9
    }
</style>