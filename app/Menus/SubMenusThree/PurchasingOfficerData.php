<?php

namespace App\Menus\SubMenusThree;

use App\ScreenSession;
use App\State\ClientState;
use App\State\UserState;
use Log;
use validator;
use App\ExternalServices\MagricApi;

class PurchasingOfficerData extends ScreenSession
{
  

  public $menuName = 'purchasing_officer_data';

  public function ask()
  {
    $output = $this->formatData();
    if (!$output) {
      $noRecord = "No Record Found ! " . PHP_EOL . "2.Retry" . PHP_EOL . "3.Cancel";

      return $this->response($noRecord, $this->menuName);
    }
    return $this->response($output, $this->menuName);
  }

  public function processUserInput($next, $state)
  {
    $validator = Validator::make(request()->all(), [
      'userInput' => 'required|in:1,0,3,2'
  ]);

  if (request()->userInput == "3") {
      Log::info('evacuation: depot Keeper lookup  cancelled');
      return $this->endSession();
  }

  if (request()->userInput  == "2") {
      $previousMenu = 'evacuations';
      $state['current_menu'] = $previousMenu;
      (new ClientState)->setState($previousMenu, request()->all(), $state['flow']);
      return $this->nextScreen($state, $previousMenu);
   }

  if ($validator->fails()) {
      Log::info('Validator failed for confirming Purchasing Data', [$validator->errors()]);
      return $this->ask();
  }

  if (request()->userInput == "1") {
      (new UserState)->store(['confirmed' => request()->userInput]);
      (new ClientState)->setState($next, request()->all(), $state['flow']);
      return $this->nextScreen($state, $next);
   }

  }


  private function formatData()
  {
    $data = $this->callApi();
    if (!$data) {
      return null;
    }
    $content = $this->getMenuContent('purchasing_officer_data');
    $output = str_replace('{Name}', $data, $content);
    return $output;
  }

  private function callApi()
  {
    $purchaseOfficerNumber = (new UserState)->getState()['purchase_officer_number'];
    $purchaseOfficerData = (new MagricApi)->lookUp($purchaseOfficerNumber);

    $role = $purchaseOfficerData['roles'] ?? ' ';
 
    if (is_array($role) && in_array('purchasing-officer',$role)) {
      (new UserState)->store(['purchase_officer_id' => $purchaseOfficerData['id']]);
      return $purchaseOfficerData['name'];
    }
  }
}
