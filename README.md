## add ThrottleRequest.php

app/Http/Middleware/RateLimitting/ThrottleRequests.php

middlewareにレートリミット用のファイルを作成します

↓ 記事の最後尾に実装のベースとなったコードが記載されています
Create a new file ApiThrottleRequests.php in app/Http/Middleware/ and paste the code below:
https://stackoverflow.com/questions/40246741/laravel-rate-limit-to-return-a-json-payload

レスポンスを一部変更

```
$errorinfo = json_encode([
'status_code' => '429',
'error' => 'Too Many Requests',
'error_description' => 'アクセス数が上限に達しました。',
]);
```

環境変数の値からレートリミットを設定
$maxAttempts = (int) config('app.maxAttempts');

※Laravelではconfig経由で.envの値取得を推奨されています
↓設定キャッシュの項目に記載あり
https://readouble.com/laravel/6.x/ja/configuration.html

引数の値を変えて、ここでは1分間の制限に指定
$this->limiter->hit($key, 60);

## change app/Http/Kernel.php

kernel.phpに追加したパスを指定します
※環境変数からレートリミットを制御させたいので、数値指定を削除しました

```

'api' => [
'throttle', //環境変数から制御する為に数値設定を削除
\Illuminate\Routing\Middleware\SubstituteBindings::class,
],

protected $routeMiddleware = [
'auth' => \App\Http\Middleware\Authenticate::class,
'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
'can' => \Illuminate\Auth\Middleware\Authorize::class,
'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
'throttle' => \App\Http\Middleware\RateLimitting\ThrottleRequests::class, //ミドルウェアのファイルパスを指定
'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
];

```

## add app.php

config\app.php

環境変数の設定数値を読み込むようにします

```
'maxAttempts' => env('MAX_ATTEMPTS_PER_MINUTE', '100'),
```

## setting .env

.env

環境変数にアクセス上限数を設定します

```
#レートリミット制限値の設定：設定した数値のアクセス数を超えると制限がかかる
MAX_ATTEMPTS_PER_MINUTE=100
```
