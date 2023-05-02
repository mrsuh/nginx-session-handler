<?php

namespace Mrsuh\NginxSessionHandlerBundle\Session\Storage\Handler;

use Predis\ClientInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RedisSessionHandler implements \SessionHandlerInterface
{
    const PREFIX_PHP_SESSION = 'php-session';
    const PREFIX_USER_ID     = 'user-id';
    const PREFIX_USER_ROLE   = 'user-role';

    private ClientInterface       $redis;
    private TokenStorageInterface $tokenStorage;
    private int                   $ttl;
    private string                $prefix;

    public function __construct(ClientInterface $redis, TokenStorageInterface $tokenStorage, array $params)
    {
        $this->tokenStorage = $tokenStorage;
        $this->redis        = $redis;
        $this->prefix       = $params['session_prefix'] ?: 'phpsession';
        $this->ttl          = $params['session_lifetime'] ?: 3600;
    }

    /**
     * @param string $path
     * @param string $name
     */
    public function open($path, $name): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * @param string $id
     */
    public function read($id): string
    {
        return $this->redis->hget($this->getRedisKey($id), self::PREFIX_PHP_SESSION) ?: '';
    }

    /**
     * @param string $id
     * @param string $data
     */
    public function write($id, $data): bool
    {
        if ($this->tokenStorage->getToken() && is_object($user = $this->tokenStorage->getToken()->getUser())) {
            $userId    = $user->getId();
            $userRoles = $user->getRoles();
        } else {
            $userId    = null;
            $userRoles = ['IS_AUTHENTICATED_ANONYMOUSLY'];
        }

        $key = $this->getRedisKey($id);
        $this->redis->hmset($key, [
            self::PREFIX_PHP_SESSION => $data,
            self::PREFIX_USER_ID     => $userId,
            self::PREFIX_USER_ROLE   => implode(' ', $userRoles)
        ]);
        $this->redis->expire($key, $this->ttl);

        return true;
    }

    /**
     * @param string $id
     */
    public function destroy($id): bool
    {
        $this->redis->del($this->getRedisKey($id));

        return true;
    }

    /**
     * @param int $max_lifetime
     */
    public function gc($max_lifetime): int|false
    {
        return 0;
    }

    private function getRedisKey($sessionId): string
    {
        return $this->prefix . ':' . $sessionId;
    }
}
