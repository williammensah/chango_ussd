<?php

namespace App\Http\Controllers;

use App\MapMenus;
use App\MenuOptions;
use App\Menus\MainMenu;
use App\State\ClientState;
use App\State\UserState;
use Illuminate\Support\Facades\Artisan;
use App\Classes\UssdShortCodes;
use Log;
use App\Http;
use App\ExternalServices\MomoTellerApi;
use Cache;


class SessionController extends USSDController
{
    use MenuOptions, MapMenus;

    public function index($isBackMenu = null)
    {
        
        if (request()->requestType === 'INITIATION') {
            Log::info('New Session Start', [request()->all()]);
            return (new MainMenu)->index();
        }
        $clientState = new ClientState;
        $currentState = $clientState->getState();
        if ($currentState) {
            return $this->mapToMenu($currentState, $currentState['current_menu']);
        }
        Log::error('Something went wrong with request');
        return response("Sorry an error occured!. Please try again later.", 500);
    }

    public function refreshMenus()
    {
        Artisan::call('cache:clear');
        Log::info('Menu cache cleared');
        return "Cache cleared";
    }
}
