<?php

namespace App\Menus\SubMenusThree;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;

class Summary extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'summary';


    public function ask()
    {
        $output = $this->formatSummary();

        return $this->response($output, $this->menuName);
    }
    public function processUserInput($next, $state)
    {
        $validator = Validator::make(request()->all(), [
            'userInput' => 'required|in:1,2,0'
        ]);
        if ($validator->fails()) {
            Log::info('Validator failed for entering summary menu input', [$validator->errors()]);
            $output = $this->formatSummary();
            return $this->response($output, $this->menuName);
        }
        if (request()->userInput == $this->CONFIRM) {
            Log::info('Confirm summary - true');
            (new UserState)->store(['confirmed' => request()->userInput])->getState();
            (new ClientState)->setState($next, request()->all(), $state['flow']);
            return $this->nextScreen($state, $next);
        }
        if (request()->userInput == $this->CANCEL) {
            Log::info('user ended session for evacuations - cancelled');
            return $this->endSession();
        }

        (new ClientState)->setState($next, request()->all(), $state['flow']);
        return $this->nextScreen($state, $next);
    }

    private function formatSummary()
    {
        $userState = (new UserState)->getState();
       
        $content = $this->getMenuContent('summary');
       $output = str_replace(['{produce}','{numberOfBags}','{wayBill}'], ['Cocoa ' ,$userState['pendingEvacuation']['number_of_bags'],$userState['wayBillNumber'] ], $content);
       return $output;
    }

}
