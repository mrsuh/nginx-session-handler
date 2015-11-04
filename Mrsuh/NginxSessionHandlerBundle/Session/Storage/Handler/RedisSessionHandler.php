<?php

namespace Mrsuh\NginxSessionHandlerBundle\Session\Storage\Handler;

class RedisSessionHandler implements \SessionHandlerInterface
{
    const PREFIX_PHP_SESSION = 'php-session';
    const PREFIX_USER_ID = 'user-id';
    const PREFIX_USER_ROLE = 'user-role';

    private $redis;
    private $tokenStorage;
    private $ttl;
    private $prefix;

    public function __construct($redis, $tokenStorage, array $params)
    {
        $this->tokenStorage = $tokenStorage;
        $this->redis = $redis;
        $this->prefix = $params['session_prefix'] ?: 'phpsession';
        $this->ttl = $params['session_lifetime'] ?: 3600;
    }

    public function open($savePath, $sessionName)
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($sessionId)
    {
        return $this->redis->hget($this->getRedisKey($sessionId), self::PREFIX_PHP_SESSION) ?: '';
    }

    public function write($sessionId, $data)
    {
        if($this->tokenStorage->getToken() && !is_string($user = $this->tokenStorage->getToken()->getUser())){
            $userId = $user->getId();
            $userRole = $user->getRole();
        } else {
            $userId = null;
            $userRole = 'IS_AUTHENTICATED_ANONYMOUSLY';
        }

        var_dump($userId, $userRole);
        $key = $this->getRedisKey($sessionId);
        $this->redis->hmset($key, self::PREFIX_PHP_SESSION, $data, self::PREFIX_USER_ID, $userId, self::PREFIX_USER_ROLE, $userRole);
        $this->redis->expire($key, $this->ttl);

        return true;
    }

    public function destroy($sessionId)
    {
        $this->redis->del($this->getRedisKey($sessionId));
        return true;
    }

    public function gc($lifetime)
    {
        return true;
    }

    private function getRedisKey($sessionId)
    {
        return $this->prefix . ':' . $sessionId;
    }
}