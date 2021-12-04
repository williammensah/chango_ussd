<?php

namespace App\Menus\SubMenusTwo;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;
use App\ExternalServices\MagricApi;
use App\Menus\MainMenuTwo;

class PurchaseHistory extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'purchase_history';

   

    public function ask($data = [])
    {
        if (!$data) {
            $userState = (new UserState)->getState();
            $userId = $userState['balances']['user_id'];
            $data = (new MagricApi)->transactionHistory($userId)['data'] ?? '';
            
        }
        $message = $this->getMenuContent();
        
        $history = 'No History Found';
        if ($data) {
            $history = $this->prepareHistory($data);
        }
        
       
        $menu = str_replace('{data}',PHP_EOL.$history.PHP_EOL."1.Refresh".PHP_EOL."2.More".PHP_EOL."3.MainMenu".PHP_EOL,$message);
        return $this->response($menu, $this->menuName);
    }
    public function processUserInput($next, $state)
    {
        if (request()->userInput == 3) {
            return (new MainMenuTwo)->index();
        }
        if (request()->userInput == $this->REFRESH) {
            Log::info('Refreshing transaction history');
            return $this->ask();
        }
        if (request()->userInput == $this->NEXT) {
            $userState = (new UserState)->getState();
            $currentPage = array_key_exists('current_page', $userState) ? $userState['current_page'] : 2;
            $data = (new MagricApi)->transactionHistory($userState['balances']['user_id'], $currentPage)['data'];
            (new UserState)->store(['current_page' => $currentPage + 1]);
            return $this->ask($data);
        }
        return $this->ask();
    }

    public function prepareHistory($data)
    {
    
        $count = count($data);
        $history = '';
        for ($i = 0; $i < $count; $i++) {
            $history .= $i+1 .'  '.$data[$i]['farmer_msisdn'] . ' - ' . date('Y/m/d H:i', strtotime($data[$i]['created_at'])) .' - '.$data[$i]['weight_in_kg'].' kg '.PHP_EOL;
        }
        return $history;
    }
     
   

}
