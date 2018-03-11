<?php
use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Storage\Session;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
use OAuth\Common\Storage\Exception\AuthorizationStateNotFoundException;

/**
 * Stores a token in a PHP session.
 */
class MixedStorage implements TokenStorageInterface
{

    protected $connection;
    protected $sessionStorage;
    protected $user_id;

    public function __construct(
        $connection, $user_id = 0
    ) {
        
        $this->connection = $connection;
        $this->sessionStorage = new Session();
        $this->user_id = $user_id;
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveAccessToken($service)
    {
        $user_id = $this->getUserId();

        if (!$user_id) {
            return $this->sessionStorage->retrieveAccessToken($service);
        }
        
        $q = mysqli_query($this->connection, "SELECT accessToken FROM user WHERE user_id = $user_id");
        
        if ($row = mysqli_fetch_object($q)) {
            return unserialize(hex2bin($row->accessToken));
        }

        throw new TokenNotFoundException('Token not found in the database.');
    }

    /**
     * {@inheritDoc}
     */
    public function storeAccessToken($service, TokenInterface $token)
    {
        $user_id = $this->getUserId();

        if (!$user_id) {
            return $this->sessionStorage->storeAccessToken($service, $token);
        }

        $serializedToken = bin2hex(serialize($token));

        mysqli_query($this->connection, "UPDATE user SET accessToken='$serializedToken' WHERE user_id = $user_id");
        

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAccessToken($service)
    {
        $user_id = $this->getUserId();

        if (!$user_id) {
            return $this->sessionStorage->hasAccessToken($service);
        }
    
        $q = mysqli_query($this->connection, "SELECT accessToken FROM user WHERE user_id = $user_id");

        $row = mysqli_fetch_object($q);
        
        return $row->accessToken != "";
    }

    /**
     * {@inheritDoc}
     */
    public function clearToken($service)
    {
        $user_id = $this->getUserId();

        if (!$user_id) {
            return $this->sessionStorage->clearToken($service);
        }

        mysqli_query($this->connection, "UPDATE user SET accessToken='' WHERE user_id = $user_id");

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearAllTokens()
    {
    
        $user_id = $this->getUserId();

        if (!$user_id) {
            return $this->sessionStorage->clearToken();
        }

        // This is not implemented, very dangerous

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function storeAuthorizationState($service, $state)
    {
        $user_id = $this->getUserId();

        if (!$user_id) {
            return $this->sessionStorage->storeAuthorizationState($service, $state);
        }

        mysqli_query($this->connection, "UPDATE user SET oauthState='$state' WHERE user_id = $user_id");


        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAuthorizationState($service)
    {
        $user_id = $this->getUserId();

        if (!$user_id) {
            return $this->sessionStorage->hasAuthorizationState($service);
        }
        
        $q = mysqli_query($this->connection, "SELECT oauthState FROM user WHERE user_id = $user_id");
        $row = mysqli_fetch_object($q);
        
        return $row->oauthState != "";
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveAuthorizationState($service)
    {
        $user_id = $this->getUserId();

        if (!$user_id) {
            return $this->sessionStorage->retrieveAuthorizationState($service);
        }

        $q = mysqli_query($this->connection, "SELECT oauthState FROM user WHERE user_id = $user_id");
        
        if ($row = mysqli_fetch_object($q)) {
            return $row->oauthState;
        }

        throw new AuthorizationStateNotFoundException('State not found in database.');
    }

    /**
     * {@inheritDoc}
     */
    public function clearAuthorizationState($service)
    {
        $user_id = $this->getUserId();

        if (!$user_id) {
            return $this->sessionStorage->clearAuthorizationState($service);
        }

        mysqli_query($this->connection, "UPDATE user SET oauthState='' WHERE user_id = $user_id");

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearAllAuthorizationStates() {
        $user_id = $this->getUserId();

        if (!$user_id) {
            return $this->sessionStorage->clearAllAuthorizationStates();
        }
        
        // Not implemented, too dangerous
        
        // allow chaining
        return $this;
    }
    
    public function transfer($service) {
        $user_id = $this->getUserId();

        if (!$user_id)  {
            return false;
        }

        if ($this->sessionStorage->hasAccessToken($service)) {
            $this->storeAccessToken($service, $this->sessionStorage->retrieveAccessToken($service));
            $this->storeAuthorizationState($service, $this->sessionStorage->retrieveAuthorizationState($service));
        }
    }

    private function getUserId() {
        if ($this->user_id) {
            return $this->user_id;
        }

        if (isset($_SESSION['user'])) {
            return $_SESSION['user']->user_id;
        }
        else return 0;
    }

}
