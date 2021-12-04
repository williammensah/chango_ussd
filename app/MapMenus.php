<?php

namespace App;

use App\Menus\MainMenu;
use App\Menus\SubMenus\AuthorizePayment;
use App\Menus\SubMenus\CampaignAlias;
use App\Menus\SubMenus\CampaignName;
use App\Menus\SubMenus\ConfirmFarmerName;
use App\Menus\SubMenus\SelectTypeOfCrop;
use App\Menus\SubMenus\SelectQuantityOfLastSafe;
use App\Menus\SubMenus\Location;
use App\Menus\SubMenus\ConfirmationMessage;
use App\Menus\SubMenus\Donate;
use App\Menus\SubMenus\SelectOtherCrop;
use App\Menus\SubMenus\ValidationMessage;
use App\Menus\SubMenusTwo\BuyProduce;

trait MapMenus
{
    use MenuContent;
    public function goToMainMenu()
    {
        return (new MainMenu)->index();
    }
  
    public function mapToMenu($state, $next, $data = null)
    {
        $menu = $this->getMappings()[$state['flow']][$next];
        return (new $menu['class'])->fire($state, $menu, $data);
    }
    protected function getMappings()
    {
        return [
            'main_menu' => [
                'main_menu' => ['class' => MainMenu::class, 'next' => null],
                'ROOT' => ['class' => MainMenu::class, 'next' => null],
            ],
            'campaign_code' => [
                'campaign_alias' => ['class' => CampaignAlias::class, 'next' => 'campaign_name'],
                'campaign_name' => ['class' => CampaignName::class, 'next' => 'donate'],
                'donate' => ['class' => Donate::class, 'next' => 'authorize_payment'],
                'authorize_payment' => ['class' => AuthorizePayment::class, 'next' => ''],   
            ],
        
          
        ];
    }
}
