<?php

namespace App\ExternalServices;

use App\Http;
use Cache;
use Log;
use Carbon\Carbon;

class ChangoWebApi
{
    protected $url;
    public $accessToken;
    public function __construct()
    {
        $this->url = config('services.chango_web_api.url');
    }
     
    public function getCampaignByAlias()
    {
        return [
            'responseCode' => 200,
            'responseMessage' => 'success',
            'data' => [
                [
                    'target' => 500.00,
                    'collected' => 2.00,
                    'campaignId' => "012",
                    'campaignName' => "PRESEC QUIZ Condolence Campaign",
                    'created_at' => "2021-03-23 15:00:30"
                ]
            ]
        ];
    }
    public function lookUpPurchasingClerk($msisdn)
    {
            $data = ['msisdn' => $msisdn];
            $response = Http::post($this->url.'/purchasing-officers/lookup',$data);

            if ($response && $response['response_code'] == '200') {
                return $response['data'];
            }
            Log::info('Did not get a success from Purchasing Clerk Lookup');
            return null;
    }

    public function login($data)
    {
        $response = Http::loginPost($this->url.'/login',$data);
        return $response;
    }

    public function lookUp($msisdn)
    {
            $data = ['msisdn' => $msisdn];
            $response = Http::post($this->url.'/lookup',$data);
            if ($response && $response['response_code'] == '200') {
                return $response['data'];
            }
            Log::info('Did not get a success from  Lookup');
            return null;

    }

    public function purchasingClerkHistory()
    {
        return [
            'responseCode' => 200,
            'responseMessage' => 'transaction history successfully fetched',
            'data' => [
                [
                    'clerk_id' => 10359,
                    'transaction_id' => "20210323605A0021A2C44",
                    'clerk_phone_number' => "233556304507",
                    'bags' => "50",
                    'created_at' => "2021-03-23 15:00:30"
                ],
                [
                    'clerk_id' => 10359,
                    'transaction_id' => "20210323605A0021A2C44",
                    'clerk_phone_number' => "233556304507",
                    'bags' => "50",
                    'created_at' => "2021-04-23 15:00:30"
                ],
                [
                    'clerk_id' => 10359,
                    'transaction_id' => "20210323605A0021A2C44",
                    'clerk_phone_number' => "233556304507",
                    'bags' => "50",
                    'created_at' => "2021-04-13 15:00:30"
                ],
                [
                    'clerk_id' => 10359,
                    'transaction_id' => "20210323605A0021A2C44",
                    'clerk_phone_number' => "233556304507",
                    'bags' => "50",
                    'created_at' => "2021-05-23 15:00:30"
                ],
                [
                    'clerk_id' => 10359,
                    'transaction_id' => "20210323605A0021A2C44",
                    'clerk_phone_number' => "233556304507",
                    'bags' => "50",
                    'created_at' => "2021-03-23 15:00:30"
                ],
                [
                    'clerk_id' => 10359,
                    'transaction_id' => "20210323605A0021A2C44",
                    'clerk_phone_number' => "233556304507",
                    'bags' => "50",
                    'created_at' => "2021-03-23 15:00:30"
                ]
            ]
        ];
    }

    public function getPurchasingClerkHistory()
    {
        $response = $this->purchasingClerkHistory();
        return $response['data'];
    }
    public function lookupFarmer($msisdn)
    {
        $cacheKey = "farmer:{$msisdn}";
        $expireAt = Carbon::now()->endOfDay()->addSecond();
        return Cache::remember($cacheKey, $ttl = $expireAt, function () use ($msisdn) {
            $data = ['msisdn' => $msisdn];
            $response = Http::post($this->url.'/farmer/lookup', $data);

            if ($response && array_key_exists('errors', $response)) {
                return null;
            }
            if ($response && $response['response_code'] == '200') {
                return $response['data'];
            }
            Log::info('Did not get a success from Farmer Lookup');
            return null;
        });
    }

    public function purchasingClerkBalance($userId,$productId)
    {
        $data = ['user_id' =>$userId,'product_id' => $productId];

        $response = Http::post($this->url.'/balance',$data);

        if ($response && $response['response_code'] == '200') {
            return $response['data'];
        }
        if ($response && $response['response_code'] == '400') {
            return $response['response_message'];
        }else {
            Log::error('Did not get a success response from  purchase officer balance lookup',[$response]);
            return false;
        }

    }

    public function displayProduce()
    {
        $product = $this->product()['data']['data'];
         return $this->resetArrayIndexToStartFromOne($product);
    }

    public function produce($perPage = 1)
    {
        $cacheKey = "products";
        $expireAt = Carbon::now()->endOfDay()->addSecond();

        return Cache::remember($cacheKey, $ttl = $expireAt, function () use ($perPage) {
            $response = Http::get($this->url . '/products?' . http_build_query(['page' => $perPage]) );
            if ($response && $response['response_code'] == '200') {
                $products = $response['data']['data'];
                return $this->resetArrayIndexToStartFromOne($products);
            }
            Log::error('Did not get a success from productLookup Lookup',[$response]);
        
            return null;
        });

    }

    public function pendingEvacuation($poId)
    {
    

            $response =  $data = ['purchasing_officer_id' => $poId];
            $response = Http::post($this->url.'/evacuations/lookup',$data);
            if ($response && $response['response_code'] == '200') {
               return $response['data'];
            }
            Log::info('Did not get a success from evacuation Lookup',[$response]);
            return null;
    }

    public function getAvailableNetworks()
    {
        $data = [
            "MTN",
            "VODAFONE",
            "ARTLTIGO"
        ];
        return $this->resetArrayIndexToStartFromOne($data);
    }

    public function lookupAgent($msisdn)
    {
        return Cache::remember($msisdn, $ttl = 60, function () use ($msisdn) {
            return  $this->lookupFarmer($msisdn);
            Log::info('Did not get a success from Agent Lookup');
            return null;
        });
    }

    public function transactionHistory($userId, $pageNumber = 1, $perPage = 5)
    {
        $data = ['user_id' => $userId,'page'=>$pageNumber,'per_page' =>$perPage];
        $response = Http::post($this->url . '/purchases/history',$data);
        if ($response && $response['response_code'] == "200") {
            \Log::info('receiving a  200 response trans history api',[$response]);
            return $response['data'];
        }
        if ($response && $response['response_code'] == "400") {
            \Log::info('received a 400 rsponse form trans history  api',[$response]);
            return null;
        }
        
    }

    public function makePayment($data)
    {
        $response = Http::post($this->url . '/purchasing-officers/pay', $data);

        if ($response && $response['response_code'] == '200') {
            return $response;
        }
        return $response;

    }

    
    public function evacuationProcess($data)
    {
        $response = Http::post($this->url . '/evacuations/process', $data);

        if ($response && $response['response_code'] == '200') {
            return $response;
        }
        return $response;

    }

    public function changePin($data)
    {
        $response = Http::post($this->url . '/pin/change', $data);
        if ($response && $response['response_code'] == '200') {
            Log::info("successfully changed user pin",[$response]);
            return true;
        }
        return false;
    }


    public function resetArrayIndexToStartFromOne($networks)
    {
        return array_filter(array_merge(array(0), $networks));
    }
    public function formatData($networks)
    {
        $netRes = '';
        foreach ($networks as $key => $network) {
            $netRes .= "$key. $network ".PHP_EOL;
        }
        return $netRes;
    }
}
