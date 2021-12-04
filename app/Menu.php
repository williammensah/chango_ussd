<?php

namespace App;

use Cache;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'ussd_menus';
    protected static function getPreviousMenukey()
    {
        return request()->msisdn . request()->sessionId . '.back';
    }
    public static function isBackRequest()
    {
        return Cache::remember(self::getBackKey(), 60, function () {
            if (request()->userInput == '0') {
                return '0';
            }
            return null;
        });
    }
    public static function clearBackRequest()
    {
        return Cache::forget(self::getBackKey());
    }
    public static function getBackKey()
    {
        return request()->sessionId . request()->msisdn . '.is-back-request';
    }

    public static function setPreviousMenu()
    {
        app('redis')->rpush(self::getPreviousMenuKey(), request()->currentMenu);
        app('redis')->expire(self::getPreviousMenuKey(), 600);
    }
    public static function getPreviousMenu()
    {
        $menu = app('redis')->rpop(self::getPreviousMenuKey());
        return $menu ? $menu : 'main_menu'; // return main menu if empty
    }

    public static function transformMenu()
    {
        $menu = self::get(['name', 'content']);
        $transformedMenu = [];
        foreach ($menu as $value) {
            $transformedMenu[$value->name] = $value->content;
        }
        return $transformedMenu;
    }
}
