<?php

namespace App\Menus\SubMenusTwo;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;

class GiveAdvance extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'give_advance';

    public function ask()
    {
        $content = $this->getMenuContent('give_advance');
        return $this->endSession($content, $this->menuName);
    }

    public function processUserInput($next, $state)
    {
        (new ClientState)->setState($next, request()->all(), $state['flow']);
        (new UserState)->store(['FarmerIdNumber' => request()->userInput])->getState();
        return $this->nextScreen($state, $next);
    }

}
