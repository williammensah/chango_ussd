<?php

namespace App\Menus\SubMenusTwo;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;
use Cache;
use App\ExternalServices\MagricApi;

class EnterPin extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'enter_pin';

    public function ask()
    {
        $output = $this->formatData();
        return $this->response($output, $this->menuName);
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

    private function  formatData()
    {
        $userState = (new UserState)->getState();
        $content = $this->getMenuContent('enter_pin');

        $output = str_replace(['{FarmerName}','{10}','{amount}','{totalAmount}'],[$this->farmerName(),$userState['quantityPurchased'],$userState['produce']['price_per_kg'],$this->calculatePrice()], $content);
        return $output;
    }

    private function farmerName()
    {
      $farmerNumber = (new UserState)->getState()['ConfirmFarmerIdNumber'];
      $farmerDetails = (new MagricApi)->lookupFarmer($farmerNumber);
       return $farmerDetails['name'];
    }

    private function calculatePrice()
    {
        $userState = (new UserState)->getState();

        $price = $userState['quantityPurchased'] * $userState['produce']['price_per_kg'];
        return $price;
    }

    public  function callApi()
    {
        $userState = (new UserState)->getState();
        return (new MagricApi)->makePayment([
            'msisdn' => $userState['ConfirmFarmerIdNumber'],
            'lbc_id' =>$userState['lbc_id'],
            'user_id' => $userState['balances']['user_id'],
            'weight_in_kg'=>$userState['quantityPurchased'],
            'product_id' =>$userState['produce']['id'],
            'pin'=> request()->userInput
        ]);
    }


}
