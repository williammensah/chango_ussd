<?php

namespace App\Menus\SubMenusTwo;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;

class QuantityToPurchase extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'enter_quantity_purchase';

    public function ask()
    {
        $content = $this->formatMenuContent();
        return $this->response($content, $this->menuName);
    }

    public function processUserInput($next, $state)
    {
        $validator = Validator::make(request()->all(), [
            'userInput' => 'required'
        ]);
        if ($validator->fails()) {
            Log::info('Validator failed for entering quantity purchase', [$validator->errors()]);
            $content = $this->formatMenuContent();
            $output = $this->prepend($content,$this->invalidInput());
            return $this->response($output, $this->menuName);
        }

         if (request()->userInput > $this->checkClerkLimit()) {
             $content = $this->formatMenuContent();
             $message = "You have exceeded your limit!".PHP_EOL;
             $output = $this->prepend($content,$this->invalidInput($message));
             return $this->response($output, $this->menuName);
         }

        (new ClientState)->setState($next, request()->all(), $state['flow']);
        (new UserState)->store(['quantityPurchased' => request()->userInput]);
        return $this->nextScreen($state, $next);
    }

    private function formatMenuContent()
    {
        $content = $this->getMenuContent('enter_quantity_purchase');
        $output = str_replace(['12'],[$this->checkClerkLimit()], $content);
        return $output;

    }

    private function checkClerkLimit()
    {
        $userState = (new UserState)->getState();
         $getBag =  $userState['clerkLimit'];
        return $getBag;
    }

    /**
     * Convert cash to bags
     *
     * @param $funds
     * @return float
     */
    public function convertAmountToBags($amount,$basePrice)
    {
        $bags = round($amount / $basePrice);
        return $bags;
    }

    public function getKiloEquivalent($cash,$basePrice)
    {
        $number = round($cash, 4);

        $whole = floor($number);
        $decimal_digit = $number - $whole;
        $result = $decimal_digit * $basePrice;
        $kilos = round($result, 2);

        return $kilos;
    }

    public function convertCashToBagOnly($funds,$basePrice)
    {
        $bags = $funds / $basePrice;
        return $bags;
    }

    public function balanceDue($cash,$basePrice)
    {
        $bag_equivalent = $this->convertCashToBagOnly($cash,$basePrice);
        $bag = $this->getBagEquivalent($bag_equivalent);
        $kilo = $this->getKiloEquivalent($bag_equivalent,$basePrice);
        $unit = ($bag > 1) ? ' bags' : ' bag';
        $balance =   $kilo;
        return $balance;
    }
    public function getBagEquivalent($cash)
    {
        $actualBag = floor($cash);
        return $actualBag;
    }
}
