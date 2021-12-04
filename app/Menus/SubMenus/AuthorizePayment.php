<?php

namespace App\Menus\SubMenus;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;

class AuthorizePayment extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'authorize_payment';

    public function ask()
    {
        $userState = (new UserState)->getState();
        $content = $this->getMenuContent('authorize_payment');
        return $this->endSession($content, 'authorize_payment');
    }

}
