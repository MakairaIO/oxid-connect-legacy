<?php

class makaira_cookie_utils
{
    private static $bannerEnabled;

    public function hasCookiesAccepted()
    {
        if (isset($_COOKIE['cookie-consent'])) {
            return 'accept' === $_COOKIE['cookie-consent'];
        }

        if (null === self::$bannerEnabled) {
            self::$bannerEnabled = oxRegistry::getConfig()->getShopConfVar(
                'makaira_cookie_banner_enabled',
                null,
                oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
            );
        }

        return self::$bannerEnabled;
    }

    public function setCookie(
        $name,
        $value,
        $expire = 0,
        $path = '/',
        $saveToSession = true,
        $secure = false,
        $httpOnly = true
    )
    {
        if ($this->hasCookiesAccepted()) {
            /** @var oxUtilsServer $oxidServerUtils */
            $oxidServerUtils = oxRegistry::get('oxutilsserver');

            return $oxidServerUtils->setOxCookie($name, $value, $expire, $path, $saveToSession, $secure, $httpOnly);
        }

        return false;
    }
}
