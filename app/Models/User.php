<?php


namespace App\Models;


use Exception;
use Illuminate\Contracts\Auth\Authenticatable;

class User implements Authenticatable
{
    protected string $id;

    protected string $name;

    protected string $email;

    protected string $token;

    /**
     * User constructor.
     * @param string $id
     * @param string $name
     * @param string $email
     * @param string $token
     */
    public function __construct(string $id, string $name, string $email, string $token)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return $this->email;
    }

    /**
     * @return mixed|string
     */
    public function getAuthIdentifier(): string
    {
        return $this->id;
    }

    /**
     * @return string|void
     * @throws Exception
     */
    public function getAuthPassword(): string
    {
        throw new Exception('Not implemented');
    }

    /**
     * @return string|void
     * @throws Exception
     */
    public function getRememberToken(): string
    {
        throw new Exception('Not implemented');
    }

    /**
     * @param string $value
     * @throws Exception
     */
    public function setRememberToken($value)
    {
        throw new Exception('Not implemented');
    }

    /**
     * @return string|void
     * @throws Exception
     */
    public function getRememberTokenName(): string
    {
        throw new Exception('Not implemented');
    }


}
