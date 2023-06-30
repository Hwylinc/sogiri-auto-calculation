<?php

namespace App\View\Components;

use Illuminate\View\Component;

class calculation_info extends Component
{
    public $selectInfo;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($selectInfo)
    {
        $this->selectInfo = $selectInfo;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.calculation_info');
    }
}
