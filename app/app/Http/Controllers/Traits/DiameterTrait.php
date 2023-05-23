<?php

namespace App\Http\Controllers\Traits;

trait DiameterTrait
{

    /* **************************************** */
    /*  表示用のリストに加工
    /* **************************************** */
    private function convertDiameterDisplayData($diameterList)
    {
        $diameterDisplayList = [];
        if (!empty($diameterList)) {
            foreach ($diameterList as $diameter) {
                $diameterDisplayList['D'.$diameter['size']] = $diameter['id'];
            }
        }
        
        return $diameterDisplayList;
    }
}