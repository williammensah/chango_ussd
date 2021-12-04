<?php

namespace App\Menus\SubMenusTwo;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;
use App\ExternalServices\MagricApi;

class FarmerData extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'farmer_data';

    public function ask()
    {
       $output = $this->formatData();
       if (!$output) {
         $noRecord = "No Record Found ! ".PHP_EOL."2.Retry".PHP_EOL."3.Cancel";

        return $this->response($noRecord, $this->menuName);
       }
       return $this->response($output, $this->menuName);
    }

    public function processUserInput($next, $state)
    {


        $validator = Validator::make(request()->all(), [
            'userInput' => 'required|in:1,0,3,2'
        ]);

        if (request()->userInput == "3") {
            Log::info('Purchasing clerk farmer lookup ended - cancelled');
            return $this->endSession();
        }
        if (request()->userInput  == "2") {
            $previousMenu = 'enter_farmer_id_number';
            $state['current_menu'] = $previousMenu;
            (new ClientState)->setState($previousMenu, request()->all(), $state['flow']);
            return $this->nextScreen($state, $previousMenu);
         }

        if ($validator->fails()) {
            Log::info('Validator failed for confirming farmer Data', [$validator->errors()]);
            $content = $this->formatData();
            $output = $this->prepend($content,$this->invalidInput());
            return $this->response($output, $this->menuName);
        }
         if (request()->userInput == "1") {
            (new UserState)->store(['confirmed' => request()->userInput]);
            (new ClientState)->setState($next, request()->all(), $state['flow']);
            return $this->nextScreen($state, $next);
         }

    }


    private function formatData()
    {
        $data = $this->callApi();
        if (!$data) {
            return null;
        }
        $content = $this->getMenuContent('farmer_data');
        $output = str_replace('{farmerName}', $data, $content);
        return $output;
    }

    private function callApi()
    {
       $FarmerNumber =  (new UserState)->getState()['ConfirmFarmerIdNumber'];
       $farmerDetails = (new MagricApi)->lookupFarmer($FarmerNumber);

       if (!$farmerDetails) {
         return null;
       }
       return $farmerDetails['name'];
    }
}
