<?php

namespace App\Menus\SubMenusTwo;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;
use App\ExternalServices\MagricApi;

class ConfirmNewPin extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'confirm_new_pin';

    public function ask()
    {
        $response = $this->callApi();
        if (!$response) {
            $message = " Your current  Pin is invalid! ".PHP_EOL."1.Retry".PHP_EOL."2.Cancel";
            $output = $this->prepend($content, $this->invalidInput($message));
            return $this->response($output, $this->menuName);
        }
        $content = $this->getMenuContent('confirm_new_pin');
        return $this->response($content, $this->menuName);
    }

    public function processUserInput($next, $state)
    {
        $validator = Validator::make(request()->all(), [
            'userInput' => 'required'
        ]);
        if ($validator->fails()) {
            Log::info('Validator failed for confirming pin', [$validator->errors()]);
            $content =$this->getMenuContent('confirm_new_pin');
            $output = $this->prepend($content, $this->invalidInput());
            return $this->response($output, $this->menuName);
        }
        $userState = (new UserState)->getState();

        if (request()->userInput == '1') {
            $previousMenu = 'enter_current_pin';
            $state['current_menu'] = $previousMenu;
            (new ClientState)->setState($previousMenu, request()->all(), $state['flow']);
            return $this->nextScreen($state, $previousMenu);
        }
        if (request()->userInput == '2') {
            Log::info('Clerk ended session - cancelled');
            return $this->endSession();
        }

        if (request()->userInput !== $userState['newPin']) {
            $message = 'Your Pin do not match!'.PHP_EOL;
            $content = $this->getMenuContent('confirm_new_pin');
            $output = $this->prepend($content, $this->invalidInput($message));
            return $this->response($output, $this->menuName);
        }

        (new ClientState)->setState($next, request()->all(), $state['flow']);
        return $this->nextScreen($state, $next);
    }

    public function callApi()
    {
        $userState = (new UserState)->getState();
        return (new MagricApi)->changePin([
            'current_pin' => $userState['currentPin'],
            'pin' =>$userState['newPin'],
            'pin_confirmation' => request()->userInput,
            'msisdn' => request()->msisdn
        ]);
    }
}
