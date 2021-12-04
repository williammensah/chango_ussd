<?php

namespace App\Menus\SubMenus;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;

class AuthorizePayment extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'authorize_payment';

    public function ask()
    {
        $userState = (new UserState)->getState();
        $uniwalletData = $this->uniwalletData($userState);
        //pass the information to queue
        $content = $this->getMenuContent('authorize_payment');
        return $this->endSession($content, 'authorize_payment');
    }
    private function uniwalletData ($data)
    {
        $transactionData = [
            'trans_type' => 'debit',
            'transaction_id' => strtoupper(date("Ymd") . uniqid()),
            'customer_number' => request()->msisdn,
            'amount' => $data['amount'],
            'network' =>request()->operator,
            'campaignId' => $data['campaign_id'],
            'campaignCode' => $data['campaign_code'],
            'campaignAlias' => $data['campaign_alias']
        ];
        return $transactionData;
    }
    public function pushToQueue()
    {

    }
}
