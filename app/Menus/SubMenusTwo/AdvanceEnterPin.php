<?php

namespace App\Menus\SubMenusTwo;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;
use App\ExternalServices\MagricApi;

class AdvanceEnterPin extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'advance_enter_pin';

    public function ask()
    {
        $userState = (new UserState)->getState();
    
        $content = $this->getMenuContent('advance_enter_pin');
        $output = str_replace(['{FarmerName}','{amount}',],[$this->farmerName(),$userState['currentAdvance']], $content);
        return $this->response($output, $this->menuName);
    }
    public function processUserInput($next, $state)
    {
        $validator = Validator::make(request()->all(), [
            'userInput' => 'required|digits:4'
        ]);
        if ($validator->fails()) {
            Log::info('Validator failed for entering advance pin', [$validator->errors()]);
            $content = $this->getContent();
            $output = $this->prepend($content,$this->invalidInput());
            return $this->response($output, $this->menuName);
        }
        (new ClientState)->setState($next, request()->all(), $state['flow']);
        return $this->nextScreen($state, $next);
    }

    private function farmerName()
    {
        $farmerNumber = (new UserState)->getState()['ConfirmFarmerIdNumber'];
        $farmerDetails = (new MagricApi)->lookupFarmer($farmerNumber);
        return $farmerDetails['name'];
    }

    private function getContent()
    {
        $userState = (new UserState)->getState();
        $content = $this->getMenuContent('advance_enter_pin');
        $data = str_replace(['{FarmerName}','{amount}',],[$this->farmerName(),$userState['currentAdvance']], $content);
        return $data;
    }

}
