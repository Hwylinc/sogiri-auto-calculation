{{-- English memo
    rebarGauge: 鉄筋系
--}}

<x-menu>
    <div>
        <h1 class="text-xl">予備材一覧</h1>
    </div>

    <div class="mt-8">
        <form>
            
            <input type="radio" id="d10" value="d10" name="rebarGauge">
            <label for="d10" class="text-[#16202E] border-2 border-[#16202E]">D10</label>

            <input type="radio" id="d13" value="d13" name="rebarGauge">
            <label for="d13" class="text-[#16202E] border-2 border-[#16202E]">D13</label>

            <input type="radio" id="d16" value="d16" name="rebarGauge">
            <label for="d16" class="text-[#16202E] border-2 border-[#16202E]">D16</label>

            <input type="radio" id="d19" value="d19" name="rebarGauge">
            <label for="d19" class="text-[#16202E] border-2 border-[#16202E]">D19</label>

            <input type="radio" id="d22" value="d22" name="rebarGauge">
            <label for="d22" class="text-[#16202E] border-2 border-[#16202E]">D22</label>

        </form>
    </div>

    <div class="h-[2px] w-full bg-black mt-4"></div>
</x-menu>

<style scoped>

    input[type="radio"] {
        display: none;
    }

    label {
      width: 100px;
      text-align: center;
      display: inline-block;
      padding: 8px 16px;
      color: white;
      font-size: 16px;
      cursor: pointer;
      border-radius: 100px;
      user-select: none;
    }

    /* チェックが入っている場合のスタイルを定義する */
    input[type=radio]:checked + label {
      background-color: #16202E;
      color: #ffffff;
    }

</style>

