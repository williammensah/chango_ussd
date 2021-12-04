<?php

namespace App\Menus\SubMenusThree;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;
use App\ExternalServices\MagricApi;

class PendingEvacuations extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'pending_evacuations';


    public function ask()
    {
       
        $output = $this->getDatafromApi();
        if (!$output) {
            return $this->endSession("No Pending Evacuations!",$this->menuName);
         }
         
         if (count($this->getData()) == 3) {
            $state = new ClientState;
            $flow = 'evacuations';
            $next = 'number_of_bags_to_evaculate';
            $state->setState($next, request()->all(), $flow);
            (new UserState)->store(['pendingEvacuation' => $this->getData()]);
            return (new BagsToEvacuate)->fire($state);
        }
        return $this->response($output, $this->menuName);
    }

    public function processUserInput($next, $state)
    {
        $mAgricApi = new MagricApi;
        $validator = Validator::make(request()->all(), [
            'userInput' => 'required|in:1,2,3'
        ]);
        if ($validator->fails()) {
            Log::info('Validator failed for selecting a pending produce', [$validator->errors()]);
            $output = $this->getDatafromApi();
            return $this->response($output, $this->menuName);
        }
        
        if (request()->userInput == 1) {
            (new ClientState)->setState($next, request()->all(), $state['flow']);
           (new UserState)->store(['pendingEvacuation' => $this->getData()]);
            return $this->nextScreen($state, $next);
        }
       
    }


    private function getData()
    {
        $mAgricApi = new MagricApi;
        $userState = (new UserState)->getState();
       
        $data = $userState['purchase_officer_id'];
        $content = $this->getMenuContent('pending_evacuations');
        $pendingEvacuation  = $mAgricApi->pendingEvacuation($data);
        if (!$pendingEvacuation) {
            return null;
        }
        return $pendingEvacuation;
    }

    private function getDatafromApi()
    {
        $mAgricApi = new MagricApi;
        $userState = (new UserState)->getState();
       
        $data = $userState['purchase_officer_id'];
        $content = $this->getMenuContent('pending_evacuations');
        $pendingEvacuation  = $mAgricApi->pendingEvacuation($data);
       
        if (!$pendingEvacuation) {
            return null;
        }
        
         if ($pendingEvacuation['number_of_bags'] == 0) {
               return null;
         }
        $output = str_replace(['{data}'], [PHP_EOL.'Pending Evacuations'. PHP_EOL . "1.Cocoa ". $pendingEvacuation['number_of_bags'].' bags'], $content);
        return $output;
    }

    private function reIndexArray($data)
    {
        return array_combine(range(1, count($data)), array_values($data));
    }


}
