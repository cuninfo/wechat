<?php

namespace Stoneworld\Wechat;

/**
 * 全局通用 AccessToken
 */
class AccessToken
{


    /**
     * 应用ID
     *
     * @var string
     */
    protected $appId;

    /**
     * 应用secret
     *
     * @var string
     */
    protected $appSecret;

    /**
     * 缓存类
     *
     * @var Cache
     */
    protected $cache;

    /**
     * 缓存前缀
     *
     * @var string
     */
    protected $cacheKey = 'Stoneworld.wechat.access_token';

    // API
    const API_TOKEN_GET = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken';

    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->appId     = $appId;
        $this->appSecret = $appSecret;
        $this->cacheKey = $this->cacheKey.'.'.$appId;
        $this->cache     = new Cache($appId);
    }

    /**
     * 缓存 setter
     *
     * @param Cache $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * 获取Token
     *
     * @param bool $forceRefresh
     *
     * @return string
     */
    public function getToken($forceRefresh = false)
    {
        $cacheKey = $this->cacheKey;

        $cached = $this->cache->get($cacheKey);

        if ($forceRefresh || !$cached) {
            $token = $this->getTokenFromServer();

            $this->cache->set($cacheKey, $token['access_token'], $token['expires_in'] - 800);

            return $token['access_token'];
        }

        return $cached;
    }

    /**
     * Get the access token from WeChat server.
     *
     * @param string $cacheKey
     *
     * @return array|bool
     */
    protected function getTokenFromServer()
    {
        $http = new Http();
        $params = array(
            'corpid'      => $this->appId,
            'corpsecret'     => $this->appSecret,
        );

        $token = $http->get(self::API_TOKEN_GET, $params);

        return $token;
    }
}
