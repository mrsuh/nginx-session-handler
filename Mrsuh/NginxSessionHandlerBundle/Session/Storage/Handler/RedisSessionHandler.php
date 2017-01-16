<?php

namespace Mrsuh\NginxSessionHandlerBundle\Session\Storage\Handler;

class RedisSessionHandler implements \SessionHandlerInterface
{
    const PREFIX_PHP_SESSION = 'php-session';
    const PREFIX_USER_ID     = 'user-id';
    const PREFIX_USER_ROLE   = 'user-role';

    private $redis;
    private $tokenStorage;
    private $ttl;
    private $prefix;

    /**
     * RedisSessionHandler constructor.
     * @param $redis
     * @param $tokenStorage
     * @param array $params
     */
    public function __construct($redis, $tokenStorage, array $params)
    {
        $this->tokenStorage = $tokenStorage;
        $this->redis = $redis;
        $this->prefix = $params['session_prefix'] ?: 'phpsession';
        $this->ttl = $params['session_lifetime'] ?: 3600;
    }

    /**
     * @param string $savePath
     * @param string $sessionName
     * @return bool
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function close()
    {
        return true;
    }

    /**
     * @param string $sessionId
     * @return string
     */
    public function read($sessionId)
    {
        return $this->redis->hget($this->getRedisKey($sessionId), self::PREFIX_PHP_SESSION) ?: '';
    }

    /**
     * @param string $sessionId
     * @param string $data
     * @return bool
     */
    public function write($sessionId, $data)
    {
        if ($this->tokenStorage->getToken() && is_object($user = $this->tokenStorage->getToken()->getUser())) {
            $userId = $user->getId();
            $userRoles = $user->getRoles();
        } else {
            $userId = null;
            $userRoles = ['IS_AUTHENTICATED_ANONYMOUSLY'];
        }

        $key = $this->getRedisKey($sessionId);
        $this->redis->hmset($key, self::PREFIX_PHP_SESSION, $data, self::PREFIX_USER_ID, $userId, self::PREFIX_USER_ROLE, implode(' ',$userRoles));
        $this->redis->expire($key, $this->ttl);

        return true;
    }

    /**
     * @param string $sessionId
     * @return bool
     */
    public function destroy($sessionId)
    {
        $this->redis->del($this->getRedisKey($sessionId));

        return true;
    }

    /**
     * @param int $lifetime
     * @return bool
     */
    public function gc($lifetime)
    {
        return true;
    }

    /**
     * @param $sessionId
     * @return string
     */
    private function getRedisKey($sessionId)
    {
        return $this->prefix . ':' . $sessionId;
    }
}