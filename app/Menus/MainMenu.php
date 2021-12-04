<?php

namespace App\Menus;
use App\MenuContent;
use App\MenuOptions;
use App\Menus\SubMenus\CampaignAlias;
use App\Responses;
use App\State\ClientState;
use App\State\UserState;
use Log;
use Validator;


class MainMenu
{
    use MenuOptions, MenuContent, Responses;
    public function index()
    {
        $clientState = new ClientState;
        $userState = new UserState;
        $this->refreshSession($clientState, $userState);
        $clientState->setState('main_menu', request()->all(), 'main_menu');
       
        $content = $this->getMenuContent('main_menu');
        return $this->response($content,'main_menu');
    }


    public function fire($state = null, $next = null, $data = null)
    {
        Log::info('Main Menu Fired', ['state' => $state, 'next' => $next]);
        $state = new ClientState;

        $validator = Validator::make(request()->all(), [
            'userInput' => 'required'
        ]);
        if ($validator->fails()) {
            Log::info('Validator failed invalid menu option selected', [$validator->errors()]);
            return $this->index();
        }

        if (request()->userInput) {
            \Log::info('main menu',[request()->userInput]);
            $flow = 'campaign_code';
            $next = 'campaign_alias';
            $state->setState($next, request()->all(), $flow);
            (new UserState)->store(['campaign_code' => request()->userInput]);
            return (new CampaignAlias)->fire($state);
        }
      
        if ($next && $next === 'main_menu' || $next === 'ROOT') {
            return $this->index();
        }
        return "Invalid Input";
    }


    public function refreshSession($clientState, $userState)
    {
        $userState->clearState();
        $clientState->clearState();
    }
}
