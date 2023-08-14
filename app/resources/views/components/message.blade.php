<div class="mt-4">
    <div>
        <ul>
            @if(session('message'))
                @foreach (session('message') as $key => $messages)
                    <li class="{{ $key }} p-2 {{ $align }}">{{ $messages }}</li>
                @endforeach
            @endif
        </ul>
    </div>
</div>

<style>
    .success {
        background-color: #CAFFD2;
        color: #53BC00;
    }
    .error {
        color: #ff5353;
    }
    .center {
        text-align: center;
    }
    .left {
        text-align: left;
    }
</style>

