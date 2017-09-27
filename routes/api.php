<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| 其他（后台等）api请求
| prefix=api
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
