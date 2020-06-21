## use laravel-http-logger

ロギング機能のライブラリ、laravel-http-loggerをinstallします

↓具体的な方法は以下から参照できます

https://github.com/spatie/laravel-http-logger


## add LogWriter

ミドルウェアにLogWriter.phpを追加します

パス
app\Http\Middleware\accesslog\LogWriter.php

```
<?php

namespace App\Http\Middleware\AccessLogs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\HttpLogger\DefaultLogWriter as DefaultLogWriter;

class LogWriter extends DefaultLogWriter
{
    public function logRequest(Request $request): void
    {
        $method = strtoupper($request->getMethod());
        
        $uri = $request->getPathInfo();
        
        $bodyAsJson = json_encode($request->except(config('http-logger.except')));
    
        $message = "{$method} {$uri} - {$bodyAsJson}";
    
        Log::info($message);
    }
}
```

## add http-logger.php

コンフィグにhttp-logger.phpを追加します

config\http-logger.php

```
<?php

return [

/*
 * The log profile which determines whether a request should be logged.
 * It should implement `LogProfile`.
 */
'log_profile' => \Spatie\HttpLogger\LogNonGetRequests::class,

/*
 * The log writer used to write the request to a log.
 * It should implement `LogWriter`.
 */
// 'log_writer' => \Spatie\HttpLogger\DefaultLogWriter::class,
// パスをMiddleWareに作ったファイルに変える
'log_writer' => \Spatie\HttpLogger\accesslog\LogWriter::class,

/*
 * Filter out body fields which will never be logged.
 */
'except' => [
    'password',
    'password_confirmation',
],
];
```

## add loging.php

logging.phpに標準出力用のチャンネルを作ります

config\logging.php

```
'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'stdout' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'with' => [
                'stream' => 'php://stdout', // 標準出力にログを吐き出す
            ],
            'level' => 'debug',
        ],
```

## change .env

.envのLOG_CHANNELをstdoutにします

```
LOG_CHANNEL=stdout
```

## add kernel.php

Kernel.phpのミドルウェアにルートを追加します

add\Http\Kernel.php

```
protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \Spatie\HttpLogger\Middlewares\HttpLogger::class // ライブラリのパスを追加
    ];
```