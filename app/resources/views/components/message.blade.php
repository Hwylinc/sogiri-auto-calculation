<div>
    <div>
        <ul>
            @if(session('message'))
                @foreach (session('message') as $key => $messages)
                    <li class="{{ $key }}">{{ $messages }}</li>
                @endforeach
            @endif
        </ul>
    </div>
</div>

<style>
    .success {
        color: #00aaff;
    }
    .error {
        color: #ff5353;
    }
</style>

