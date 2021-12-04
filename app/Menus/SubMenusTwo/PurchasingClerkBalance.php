<?php

namespace App\Menus\SubMenusTwo;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;
use App\ExternalServices\MagricApi;

class PurchasingClerkBalance extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'clerk_balance';

    public function ask()
    {
        $content = $this->formatData();
        if (is_string($content)) {
            return $this->response($content, $this->menuName);
        }
        if (array_key_exists('error', $content)) {
            return $this->endSession($content['error'], $this->menuName);
        }
    }

    public function processUserInput($next, $state)
    {
        $validator = Validator::make(request()->all(), [
            'userInput' => 'required|in:1,2'
        ]);
        if ($validator->fails()) {
            Log::info('Validator failed for clerk_balance', [$validator->errors()]);
            $content = $this->formatData();
            return $this->response($content, $this->menuName);
        }
        if (request()->userInput == 2) {
            return $this->goToMainMenu2();
        }

        (new ClientState)->setState($next, request()->all(), $state['flow']);
        (new UserState)->store(['confirmBalance' => request()->userInput]);
        return $this->nextScreen($state, $next);
    }


    public function callApi()
    {
        $userState = (new UserState)->getState();
        $clerkBalance = (new MagricApi)->purchasingClerkBalance($userState['balances']['user_id'], $userState['produce']['id']);
        if (is_string($clerkBalance)) {
            $data =  ['error'  => $clerkBalance];
            return $data;
        }
        return [
            'produce' => $userState['produce']['name'],
            'balances' => $clerkBalance
        ];
    }

    private function formatData()
    {
        $data = $this->callApi();

        if (array_key_exists('error', $data)) {
            return $data;
        } else {
            $content = $this->getMenuContent('clerk_balance');
            $balanceInBag = $data['balances']['balance_in_bags'] ?? '0.00';
            $balanceInKg =  $data['balances']['balance_in_kg'] ?? '0.00';

            $balances = "{$balanceInKg} kgs ({$balanceInBag} bags)";
            (new UserState)->store(['clerkLimit' => $balanceInKg]);
            $output = str_replace(['{produce}','{bags}', '{clerkBalance}',], [$data['produce'],$balanceInBag.' bags',$balances], $content);
            return $output;
        }
    }
}
