<?php

namespace App\Menus\SubMenusThree;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;

class BagsToEvacuate extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'number_of_bags_to_evaculate';


    public function ask()
    {
        $output = $this->formatData();
        return $this->response($output, $this->menuName);
    }

    public function processUserInput($next, $state)
    {
        $userState = (new UserState)->getState();
        $validator = Validator::make(request()->all(), [
            'userInput' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            Log::info('Validator failed for confirming number of bags', [$validator->errors()]);
            $content =  $this->formatData();
            $output = $this->prepend($content, $this->invalidInput());
            return $this->response($output, $this->menuName);
        }
       
        // if (request()->userInput >  $userState['pendingEvacuation']['number_of_bags']) {
        //     $message = 'You have exceeded the number of bags limit! ';
        //     $content =  $this->formatData();
        //     $output = $this->prepend($content, $this->invalidInput($message));
        //     return $this->response($output, $this->menuName);
        // }

        if (request()->userInput  == "2") {
            $previousMenu = 'evacuations';
            $state['current_menu'] = $previousMenu;
            (new ClientState)->setState($previousMenu, request()->all(), $state['flow']);
            return $this->nextScreen($state, $previousMenu);
         }

         if (request()->userInput == 1) {
            (new ClientState)->setState($next, request()->all(), $state['flow']);
            (new UserState)->store(['ConfirmNoBags' => request()->userInput]);
            return $this->nextScreen($state, $next);
        }

    }

    private function formatData()
    {
        $content = $this->getMenuContent('number_of_bags_to_evaculate');
        $userState = (new UserState)->getState();
        $formatData = $userState['pendingEvacuation']['number_of_bags'] . ' bags' . ' (Cocoa)';
        $output = str_replace(['{pendingEvacuation}'], [$formatData], $content);
        return $output;
    }
}
