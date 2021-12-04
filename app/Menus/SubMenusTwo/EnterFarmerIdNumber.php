<?php

namespace App\Menus\SubMenusTwo;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;
use Illuminate\Validation\Rule;

class EnterFarmerIdNumber extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'enter_farmer_id_number';


    public function processUserInput($next, $state)
    {
        $validator = Validator::make(request()->all(), [
             'userInput' => 'phone:GH'
        ]);

   
        if ($validator->fails()) {
            Log::info('Validator failed for entering farmerId', [$validator->errors()]);
            $output = $this->checkFarmerId();
            return $this->response($output, $this->menuName);
        }

        (new ClientState)->setState($next, request()->all(), $state['flow']);
        $farmerNumber = preg_replace('/^0/', '233',  request()->userInput);
        (new UserState)->store(['ConfirmFarmerIdNumber' => $farmerNumber])->getState();
        return $this->nextScreen($state, $next);
    }

    private function checkFarmerId()
    {
            $validationMessage = 'Invalid Phone Number!'.PHP_EOL.'Kindly try again'.PHP_EOL;
            $content = $this->getMenuContent('enter_farmer_id_number');
            $output = $this->prepend($content,$this->invalidInput($validationMessage));
            return $output;
    }

}
