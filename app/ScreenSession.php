<?php

namespace App;

use App\MenuOptions;
use App\State\ClientState;

abstract class ScreenSession
{
    use MenuOptions, Responses, MapMenus;
    public $canGoBack = false;
    protected $properties = [];

    public function fire($state = null, $menu = null, $data = null)
    {
        $this->properties = $menu;
        $next =  @$this->properties['next'];
        
        if ($this->shouldProcessUserInput()) {
            if (Menu::isBackRequest()) {
                return $this->goBack($state);
            }
            return $this->processUserInput($next, $state, $data);
        }
        return $this->ask($data);
    }
    public function ask()
    {
        return $this->response($this->getMenuContent(), $this->menuName);
    }
    public function goBack($state, $menu = null)
    {
        $previousMenu = $menu ?? Menu::getPreviousMenu();
        Menu::clearBackRequest(); // To Stop Recursion
        $state['current_menu'] = $previousMenu;
        (new ClientState)->setState($previousMenu, request()->all(), $state['flow']);
        return $this->nextScreen($state, $previousMenu);
    }
    public function nextScreen($state, $next, $data = null)
    {
        if ($next === 'main_menu' || $next === 'ROOT') {
            return $this->goToMainMenu();
        }

        if ($next === 'main_menu3') {
            return $this->goToMainMenu3();
        }
        return $this->mapToMenu($state, $next, $data);
    }
    public function shouldProcessUserInput()
    {
        return request()->currentMenu === $this->menuName;
    }
    public function getPreviousMenu()
    {
        $menu = app('redis')->rpop($this->previousMenuKey);
        Log::info("Popped $menu");
        return $menu ? $menu : 'main_menu'; // return main menu if empty
    }
}
