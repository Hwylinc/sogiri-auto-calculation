<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Menu extends Component
{

    // componentのプロパティは2語以上の場合キャメルケースで書くこと
    public $menuList;
    public $selectPage;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($selectPage="1")
    {
        $this->menuList = [
            '1' => ['title' => '鉄筋計測', 'select' => false],
            '2' => ['title' => '計測結果履歴一覧', 'select' => false],
            '3' => ['title' => '予備材一覧', 'select' => false],
            '4' => ['title' => '部材集計データ', 'select' => false],
        ];

        $this->selectPage = $selectPage;

        $this->menuList[$selectPage]['select'] = true;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.menu');
    }
}
