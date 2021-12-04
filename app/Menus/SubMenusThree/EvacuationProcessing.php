<?php

namespace App\Menus\SubMenusThree;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;

class EvacuationProcessing extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'evacuation_processing';


    public function ask()
    {
        $content = $this->getMenuContent('evacuation_processing');
        return $this->endSession($content, $this->menuName);
    }


}
