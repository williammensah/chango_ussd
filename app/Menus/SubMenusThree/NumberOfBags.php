<?php

namespace App\Menus\SubMenusThree;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;

class NumberOfBags extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'confirm_number_of_bags';


    public function ask()
    {

        $content = $this->getMenuContent('confirm_number_of_bags');
        return $this->response($content, $this->menuName);
    }

    public function processUserInput($next, $state)
    {
        $validator = Validator::make(request()->all(), [
            'userInput' => 'required|numeric'
        ]);
         $userState =  (new UserState);
         $userPreviousInput =  $userState->getState()['numberOfBags'];

        if ($validator->fails()) {
            Log::info('Validator failed for entering advance pin', [$validator->errors()]);
            $output = $this->getContent();
            return $this->response($output, $this->menuName);
        }
        if ($userPreviousInput !== request()->userInput) {
            $validationMessage = 'Input does not match!'.PHP_EOL.'Kindly try again';
            $output = $this->getContent($validationMessage);
            return $this->response($output, $this->menuName);
        }
        (new ClientState)->setState($next, request()->all(), $state['flow']);
        (new UserState)->store(['confirmNumberOfBags' => request()->userInput]);
        return $this->nextScreen($state, $next);
    }

    public function getContent($validationMessage = null)
    {
        $content = $this->getMenuContent('confirm_number_of_bags');
        $output = $this->prepend($content,$this->invalidInput($validationMessage));
        return $output;
    }

}
