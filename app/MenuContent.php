<?php

namespace App;

use Cache;

trait MenuContent
{
    protected $previousMenuKey;
    protected $menu = [];
    public function __construct()
    {
        $this->menu = Cache::rememberForever('chango_ussd_menu-cache', function () {
            return Menu::transformMenu();
        });
        $this->previousMenuKey = request()->msisdn . request()->sessionId . '.back';
    }
    public function invalidInput($message = null)
    {
        return  $message ?? "Invalid Input".PHP_EOL;
    }
    public function getMenuContent($menuName = null): string
    {
        $this->setAsPreviousMenu();
        $message = $this->menu[$menuName ?? $this->menuName];
        return $this->properties['customMessage'] ?? $message;
    }

    public function prepend(& $string, $prefix) {
        $string = substr_replace($string, $prefix, 0, 0);
        return $string;
    }

    public function setAsPreviousMenu()
    {
        if (Menu::isBackRequest()) {
            return;
        }
        Menu::setPreviousMenu();
    }

    public function goBackToPreviousMenu($previousMenu)
    {
        $goBack = $previousMenu;
        $state['current_menu'] = $goBack;
        return $state['current_menu'];
    }
}
