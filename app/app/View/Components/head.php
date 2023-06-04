<?php

namespace App\View\Components;

use Illuminate\View\Component;

class head extends Component
{
    public $show;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title, $imageFlg)
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
        }

        $this->show['title'] = $title;
        $this->show['image'] = $img;
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
