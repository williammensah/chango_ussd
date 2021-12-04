<?php
namespace App\ExternalServices;
use Log;
use Aws\Sqs\SqsClient;
use Aws\Exception\AwsException;

class  SqsQueue{

    public function uniwalletTransQueue($message)
    {
		$client = new SqsClient([
            'region'  => config('queue.connections.sqs.region'),
            'version' => 'latest',
            'credentials' => [
                'key'    => config('queue.connections.sqs.key'),
                'secret' => config('queue.connections.sqs.secret'),
            ],
        ]);

        $params = [
            'MessageBody' => $message,
            'QueueUrl'  => config('queue.connections.sqs.prefix').config('queue.connections.sqs.uniwallet_trans_queue')
        ];

        try {
         $result = $client->sendMessage($params);
         Log::info("Successfully got a response from  uniwallet trans queue",["status_code: ",$result['@metadata'],"general_response :",$result]);
        }catch(AwsException $e){
            Log::error("Failed to send message to queue",[$e->getMessage()]);
        }
        catch (\Throwable $th) {
            Log::error("Caught Error from the uniwallet transaction queue",[$th->getMessage()]);
        }
    }


}
