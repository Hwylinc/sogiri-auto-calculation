<div class="mt-4">
    <div>
        <ul>
            @if(session('message'))
                @foreach (session('message') as $key => $messages)
                    @foreach( $messages as $message )
                        <li class="{{ $key }} common {{ $align }}">{{ $message }}</li>
                    @endforeach
                @endforeach
            @endif
        </ul>
    </div>
</div>

<style>
    .common {
        padding: 4px;
        padding-left: 16px;
    }
    .success {
        background-color: #d6ffdc;
        color: #04d023;
    }
    .error {
        background-color: #ffa1a1;
        color: #ba0000;
    }
    .center {
        text-align: center;
    }
    .left {
        text-align: left;
    }
</style>

