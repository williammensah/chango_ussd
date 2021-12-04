<?php

use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily'],
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/lumen.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/lumen.log'),
            'level' => 'debug',
            'days' => 14,

    ],
        'filteredLog' => [
            'driver' => 'custom',
            'via'=>\App\Logging\FilterForSensitiveData::class,
            'path' => storage_path('logs/lumen.log'),
            'level' => 'debug',
            'days' => 14,
            'filters'=> ['pan','cvv','expiry'],

    ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL') ?? 'https://hooks.slack.com/services/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',
            'username' => 'GOG_AUTH_API_UAT',
            'emoji' => ':boom:',
            // 'level' => 'info',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],
        'cloudwatch' => [
            'driver' => 'custom',
            'via' => \App\Logging\CloudWatchLoggerFactory::class,
            'sdk' => [
              'region' => env('CLOUDWATCH_LOG_REGION', 'us-west-1'),
              'version' => env('CLOUDWATCH_LOG_VERSION','2010-08-01'),
              'credentials' => [
                'key' => env('CLOUDWATCH_LOG_KEY'),
                'secret' => env('CLOUDWATCH_LOG_SECRET')
              ]
            ],
            'stream_name'=>env('CLOUDWATCH_LOG_STREAM_NAME', 'uat.transflow-platform-knowledge-base'),
            'group_name' => env('CLOUDWATCH_LOG_GROUP_NAME', 'TRANSFLOW'),
            'retention' => env('CLOUDWATCH_LOG_RETENTION_DAYS',7),
            'level' => env('CLOUDWATCH_LOG_LEVEL','debug')
          ],
    ],

];
    