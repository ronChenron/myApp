<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/8/7
 * Time: 9:12
 */

namespace App\Repositories;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Laravel\Passport\Bridge\UserRepository as Repository;
use Laravel\Passport\Bridge\User;
use RuntimeException;

class PassportRepository extends Repository
{
    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        $guard = App::make(Request::class)->get('guard') ?: 'api';//其实关键的就在这里，就是通过传递一个guard参数来告诉它我们是使用api还是admin_api provider来做认证
        $provider = config("auth.guards.{$guard}.provider");
        if (is_null($model = config("auth.providers.{$provider}.model"))) {
            throw new RuntimeException('Unable to determine user model from configuration.');
        }

        if (method_exists($model, 'findForPassport')) {
            $user = (new $model)->findForPassport($username);
        } else {
            $user = (new $model)->where('email', $username)->first();
        }


        if (!$user) {
            return;
        } elseif (method_exists($user, 'validateForPassportPasswordGrant')) {
            if (!$user->validateForPassportPasswordGrant($password)) {
                return;
            }
        } elseif (!$this->hasher->check($password, $user->password)) {
            return;
        }

        return new User($user->getAuthIdentifier());
    }
}