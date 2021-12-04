<?php

namespace App\Menus\SubMenus;

use App\ScreenSession;
use Validator;
use App\State\UserState;
use Log;
use App\State\ClientState;

class CampaignAlias extends ScreenSession
{
    public $menuName = "campaign_alias";

    public function processUserInput($next, $state)
    {
        $validator = Validator::make(request()->all(), [
            'userInput' => 'required'
        ]);
        if ($validator->fails()) {
            Log::info('Validator failed for entering campaign_alias', [$validator->errors()]);
            $content =$this->getMenuContent('campaign_alias');
            $output = $this->prepend($content, $this->invalidInput());
            return $this->response($output, $this->menuName);
        }
        (new ClientState)->setState($next, request()->all(), $state['flow']);
        (new UserState)->store(['campaign_alias' => request()->userInput]);
        return $this->nextScreen($state, $next);
    }
}
