<?php

namespace App\Menus\SubMenus;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;

class ConfirmTransaction extends ScreenSession
{
    public $menuName = 'confirm_transaction';
    public function ask()
    {
        Log::info('Confirm Transaction Ask');
        $userState = (new UserState)->getState();
        $content = $this->getMenuContent('confirm_transaction');
        $output = str_replace(['amount', 'msisdn', 'lookupName'], [$userState['amount'], $userState['momoNumber'], ''], $content);
        return $this->response($output, $this->menuName);
    }
    public function processUserInput($next, $state)
    {
        if (request()->userInput == $this->CONFIRM) {
            Log::info('Confirm Transaction - true');
            (new UserState)->store(['confirmed' => request()->userInput])->getState();
            (new ClientState)->setState($next, request()->all(), $state['flow']);
            return $this->nextScreen($state, $next);
        }
        if (request()->userInput === $this->CANCEL) {
            Log::info('Confirm Transaction - cancelled');
            return $this->endSession("Exiting", $this->menuName);
        }
        Log::info('Confirm Transaction Invalid Input');
        return $this->response($this->invalidInput(), $this->menuName);
    }

}
