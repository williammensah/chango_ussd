<?php

namespace App\Menus\SubMenusThree;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;
use App\ExternalServices\MagricApi;

class Evacuation extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'evacuations';

    public function ask()
    {
        $userState = (new UserState)->getState();
        $content = $this->getMenuContent('evacuations');
        return $this->response($content, $this->menuName);
    }
    public function processUserInput($next, $state)
    {
        $validator = Validator::make(request()->all(), [
            'userInput' => 'phone:GH'
        ]);
        if ($validator->fails()) {
            Log::info('Validator failed for entering purchasing clerk Number', [$validator->errors()]);
            $output = $this->checkPurchasingClerkId();
            return $this->response($output, $this->menuName);
        }
        (new ClientState)->setState($next, request()->all(), $state['flow']);
        $purchasingClerkNumber = preg_replace('/^0/', '233',  request()->userInput);
        (new UserState)->store(['purchase_officer_number' => $purchasingClerkNumber]);
        return $this->nextScreen($state, $next);
    }

   
    private function checkPurchasingClerkId()
    {
            $validationMessage = 'Invalid Phone Number!'.PHP_EOL.'Kindly try again'.PHP_EOL;
            $content = $this->getMenuContent('evacuations');
            $output = $this->prepend($content,$this->invalidInput($validationMessage));
            return $output;
    }



}
