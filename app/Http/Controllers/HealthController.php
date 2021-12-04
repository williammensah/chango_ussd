<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\BaseLogic\Classes\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HealthController extends Controller
{

  /**
   * Create a new controller instance.
   *
   * @return void
   */
 

  public function health()
  { 
      if (!$this->checkDbCon()) {
        return response()->json(['responseCode' => 500, 'status' => 'Failed','error' =>'Could not connect to the database. Please check your configuration.'], 500);
      }

      if (!$this->redisTest()) {
        return response()->json(['responseCode' => 500, 'status' => 'Failed','error' =>'error connecting to redis'], 500);
      }

      return response()->json(['responseCode' => 200, 'status' => 'Success'], 200);
  }

  private function checkDbCon()
  {

    try {
      $checkDbConnection = DB::connection()->getPdo();
      if ($checkDbConnection) {
        return true;
      }
    } catch (\Exception $e) {
      return false;
    }
  }

  public function redisTest()
  {
    try {
      $redis = Redis::connect('127.0.0.1', 3306);
      return true;
    } catch (\Predis\Connection\ConnectionException $e) {
      return false;
    }
  }


}
