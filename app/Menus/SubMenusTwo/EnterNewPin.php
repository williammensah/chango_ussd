<?php

namespace App\Menus\SubMenusTwo;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;

class EnterNewPin extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'enter_new_pin';

    public function ask()
    {
        $content = $this->getMenuContent('enter_new_pin');
        return $this->response($content, $this->menuName);
    }
    public function processUserInput($next, $state)
    {
        $validator = Validator::make(request()->all(), [
            'userInput' => 'required|digits:4'
        ]);
        if ($validator->fails()) {
            Log::info('Validator failed for entering new pin', [$validator->errors()]);
            $content =$this->getMenuContent('enter_new_pin');
            $output = $this->prepend($content, $this->invalidInput());
            return $this->response($output, $this->menuName);
        }
        (new ClientState)->setState($next, request()->all(), $state['flow']);
           (new UserState)->store(['newPin' => request()->userInput]);
        return $this->nextScreen($state, $next);
    }
}
