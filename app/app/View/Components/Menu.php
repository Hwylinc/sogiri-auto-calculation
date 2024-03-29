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
            '1' => [
                'title' => '鉄筋計測', 
                'select' => false, 
                'route_name' => 'rebar.register', 
                'param' => ['diameter' => "1"],
                'image' => 'calculate'
            ],
            '4' => [
                'title' => '未計算一覧', 
                'select' => false, 
                'route_name' => 'calculate.ready', 
                'image' => 'mikeisan'
            ],
            '2' => [
                'title' => '計測結果履歴一覧', 
                'select' => false, 
                'route_name' => 'calculate.list', 
                'param' => ['factry_id' => '1'],
                'image' => 'clock'
            ],
            '3' => [
                'title' => '予備材一覧', 
                'select' => false, 
                'route_name' => 'spare.list', 
                'param' => ['factry_id' => '1'],
                'image' => 'list'
            ],
            // 20230718時点では不必要なのでコメントアウト
            // '4' => [
            //     'title' => '部材集計データ', 
            //     'select' => false, 
            //     'route_name' => 'spare.list', 
            //     'param' => ['factry_id' => '1'],
            //     'image' => 'totalling'
            // ],
        ];

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
