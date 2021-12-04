<?php

namespace App\Menus\SubMenus;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use Validator;
use App\Menus\SubMenus\ValidationMessage;
use App\ExternalServices\MomoTellerApi;

class Donate extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'donate';

    public function ask()
    {
        $userState = (new UserState)->getState();
        $content = $this->getMenuContent('donate');
        $output = str_replace(['{data}'],[$userState['campaign_name']], $content);
        return $this->response($output, $this->menuName);
    }


    public function processUserInput($next, $state, $back)
    {
        $validator = Validator::make(request()->all(), [
            'userInput' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            Log::info('Validator failed for entering amount', [$validator->errors()]);
           // return $this->response($output, $this->menuName);
        }
         //addd validation
        (new UserState)->store(['amount' => request()->userInput]);
        (new ClientState)->setState($next, request()->all(), $state['flow']);
        return $this->nextScreen($state, $next);
    }

   
}
