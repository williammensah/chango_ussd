<?php

namespace App\Menus\SubMenus;

use App\ExternalServices\ChangoWebApi;
use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;
use App\Menus\SubMenus\SelectOtherCrop;
class CampaignName extends ScreenSession
{
    /* You ask with the current menu name.
    eg. return Mobilenumber::ask(), current menu returned = enter_mobile_number
     */

    public $menuName = 'campaign_name';

    public function ask()
    {
        $data = $this->callApi();

        $userState = (new UserState)->getState();
        $content = $this->getMenuContent('campaign_name');
        $output = str_replace(['{data}','{target}', '{Collected}'],[$data[0]['campaignName'],$data[0]['target'],$data[0]['collected']], $content);
        (new UserState)->store(['campaign_name' => $data[0]['campaignName'],'campaign_id' =>$data[0]['campaignId']]);
        return $this->response($output, $this->menuName);
    }

    public function processUserInput($next, $state, $back)
    {
        $validator = Validator::make(request()->all(), [
            'userInput' => 'required|in:1,0'
        ]);
        if ($validator->fails()) {
            Log::info('Validator failed for selecting campaign name options', [$validator->errors()]);
            $content = $this->getMenuContent('campaign_name');
            //$this->invalidInput();
            return $this->response($content, $this->menuName);
        }

        if (request()->userInput == 1) {
            (new ClientState)->setState($next, request()->all(), $state['flow']);
            (new UserState)->store(['campaignOptions' => request()->userInput]);
            return $this->nextScreen($state, $next);
        }
        if (request()->userInput  == "0") {

            $previousMenu = 'campaign_alias';
            $state['current_menu'] = $previousMenu;
            (new ClientState)->setState($previousMenu, request()->all(), $state['flow']);
            return $this->nextScreen($state, $previousMenu);
         }

    }

    private function callApi()
    {
        $getCampaignInfo = (new ChangoWebApi)->getCampaignByAlias();
        return $getCampaignInfo['data'];
    }
}
