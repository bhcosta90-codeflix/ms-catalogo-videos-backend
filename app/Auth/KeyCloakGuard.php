<?php


namespace App\Auth;


use App\Models\User;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Traits\Macroable;
use Tymon\JWTAuth\JWT;

class KeyCloakGuard implements Guard
{
    use GuardHelpers, Macroable {
        __call as macroCall;
    }
    /**
     * The JWT instance.
     *
     * @var JWT
     */
    protected JWT $jwt;

    /**
     * The request instance.
     *
     * @var Request
     */
    protected Request $request;

    public function __construct(JWT $jwt, Request $request)
    {
        $this->jwt = $jwt;
        $this->request = $request;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|null
     */
    public function user(): ?Authenticatable
    {
        if ($this->user !== null) {
            return $this->user;
        }

        if (($token = $this->jwt->setRequest($this->request)->getToken()) &&
            ($payload = $this->jwt->check(true))
        ) {
            return $this->user = new User($payload['sub'], $payload['name'], $payload['email'], $token);
        }

        return null;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = []): bool
    {
        throw new \Exception('Not implemented');
    }

}
