<?php

namespace App\Providers;

use App\Repositories\PassportRepository;
use League\OAuth2\Server\Grant\PasswordGrant;
use Laravel\Passport\PassportServiceProvider as PassportProvider;
use Laravel\Passport\Passport;

class PassportServiceProvider extends PassportProvider
{
    /**
     * Create and configure a Password grant instance.
     *
     * @return PasswordGrant
     */
    protected function makePasswordGrant()
    {
        $grant = new PasswordGrant(
        //主要是这里，我们调用我们自己UserRepository
            $this->app->make(PassportRepository::class),
            $this->app->make(\Laravel\Passport\Bridge\RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}
