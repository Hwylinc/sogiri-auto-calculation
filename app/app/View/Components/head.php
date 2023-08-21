<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Head extends Component
{
    public $show;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title, $imageFlg, $horizon="1")
    {
        $img = "";

        switch($imageFlg) {
            case "1":
                $img = 'calculate';
                break;
            case "2":
                $img = 'clock';
                break;
            case "3":
                $img = 'list';
                break;
            case "4":
                $img = 'totalling';
                break;
            case "5":
                $img = 'mikeisan';
                break;
        }

        $this->show['title'] = $title;
        $this->show['image'] = $img;
        $this->show['horizon'] = $horizon === "0" ? false : true;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.head');
    }
}
