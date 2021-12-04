<?php

namespace App\Menus\SubMenusTwo;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;
use App\ExternalServices\MagricApi;
use App\Menus\MainMenuTwo;

class BuyProduce extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'buy_produce';

    public function ask($data = [])
    {
        $data = $this->getData();
        if ($data) {
            $content = $this->FormatMenuContent($data);
            return $this->response($content, $this->menuName);
        }
        return $this->response('No Product!', $this->menuName);


    }

    public function nextScreen($state, $next, $data = null)
    {
        if ($next === 'main_menu2' || $next === 'ROOT') {
            return $this->goToMainMenu();
        }
        return $this->mapToMenu($state, $next, $data);
    }

    public function processUserInput($next, $state)
    {

        $selectedProduce = $this->checkProduceValue(request()->userInput);
         if (!$selectedProduce) {
            Log::info('Validator failed for selecting a produce', [request()->userInput]);
            $data = $this->getData();
            $content = $this->FormatMenuContent($data);
            return $this->response($content, $this->menuName);
         }
        (new ClientState)->setState($next, request()->all(), $state['flow']);
        (new UserState)->store(['produce' => $selectedProduce]);
        return $this->nextScreen($state, $next);
    }


    public function goToMainMenu()
    {
        return (new MainMenuTwo)->index();
    }

    private function checkProduceValue($userInput)
    {
        $produceValue = (new MagricApi)->produce();
        return array_key_exists($userInput, $produceValue) ? $produceValue[$userInput] : false;
    }

    public function getData()
    {
        $menu = (new MagricApi)->produce();
        // dd($menu);
        $data = $this->formatData($menu);
        return $data;
    }
    public function formatData($datas)
    {
        $netRes = '';
        foreach ($datas as $key => $data) {
            $netRes .=  $key.'.'.$data['name'].PHP_EOL;
        }
        return $netRes;
    }

    private function FormatMenuContent($data)
    {
        $content = $this->getMenuContent('buy_produce');
        $output = str_replace(['{data}'],[$data], $content);
        return $output;

    }

}
