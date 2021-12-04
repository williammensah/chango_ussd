<?php

namespace App\Menus\SubMenusTwo;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;

class PaymentProcessing extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'payment_processing';

    public function ask()
    {
        $content = $this->getMenuContent('payment_processing');
        return $this->endSession($content, $this->menuName);
    }

    public function callApi()
    {

    }


}
