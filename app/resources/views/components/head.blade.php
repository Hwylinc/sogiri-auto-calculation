<div class="mb-3">
    <!-- The only way to do great work is to love what you do. - Steve Jobs -->
    <div class="flex font mb-3">
        <img src="{{ asset("images/{$show['image']}.svg") }}" alt="" class="mr-2 img" > 
        <h2>{{ $show['title'] }}</h2>
    </div>

    @if($show['horizon'])
    <hr>
    @endif
</div>

<style scoped>
    .font {
        font-size: 24px;
        font-weight: bold;
        color: black;
    }

    .img {
        width: 24px;
        height: auto;
        filter: brightness(0) invert(0);
    }
</style>