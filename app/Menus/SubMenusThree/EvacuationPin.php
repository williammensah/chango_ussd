<?php

namespace App\Menus\SubMenusThree;

use App\ExternalServices\MagricApi;
use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;

class EvacuationPin extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'evacuation_pin';


    public function ask()
    {
        $content = $this->getMenuContent('evacuation_pin');
        return $this->response($content, $this->menuName);
    }

    public function processUserInput($next, $state)
    {
        $validator = Validator::make(request()->all(), [
            'userInput' => 'required|digits:4'
        ]);
        if ($validator->fails()) {
            Log::info('Validator failed for entering pin', [$validator->errors()]);
            $content = $this->formatData();
            $output = $this->prepend($content,$this->invalidInput());
            return $this->response($this->invalidInput(), $this->menuName);
        }
        $response = $this->callApi();

        if ($response && $response['response_code'] !== '200') {
            return $this->endSession($response['response_message'], $this->menuName);
        }
        (new ClientState)->setState($next, request()->all(), $state['flow']);
        return $this->nextScreen($state, $next);
    }


    
    public  function callApi()
    {
        $userState = (new UserState)->getState();
        return (new MagricApi)->evacuationProcess([
            'purchasing_officer_id' => $userState['purchase_officer_id'],
            'depot_keeper_id' =>$userState['id'],
            'number_of_bags' =>$userState['pendingEvacuation']['number_of_bags'],
            'weight_in_kg'=>$userState['pendingEvacuation']['weight_in_kg'],
            'way_bill_number' =>$userState['wayBillNumber'],
            'pin'=> request()->userInput
        ]);

    }

}
