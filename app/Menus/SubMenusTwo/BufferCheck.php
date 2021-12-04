<?php

namespace App\Menus\SubMenusTwo;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;

class BufferCheck extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'buffer_check';


    public function ask()
    {
        $content = $this->getMenuContent('buffer_check');
        return $this->endSession($content, $this->menuName);
    }



}
