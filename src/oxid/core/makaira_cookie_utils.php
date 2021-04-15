<?php

class makaira_cookie_utils
{
    private static $bannerEnabled;

    public function hasCookiesAccepted()
    {
        if (null === self::$bannerEnabled) {
            self::$bannerEnabled = oxRegistry::get('oxconfig')->getShopConfVar(
                'makaira_cookie_banner_enabled',
                null,
                oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
            );
        }

        if (!self::$bannerEnabled) {
            return true;
        }

        if (isset($_COOKIE['cookie-consent'])) {
            return 'accept' === $_COOKIE['cookie-consent'];
        }

        return false;
    }

    public function setCookie(
        $name,
        $value,
        $expire = 0,
        $path = '/',
        $domain = null,
        $saveToSession = true,
        $secure = false,
        $httpOnly = true
    ) {
        if ($this->hasCookiesAccepted()) {
            /** @var oxUtilsServer $oxidServerUtils */
            $oxidServerUtils = oxRegistry::get('oxutilsserver');

            return $oxidServerUtils->setOxCookie(
                $name,
                $value,
                $expire,
                $path,
                $domain,
                $saveToSession,
                $secure,
                $httpOnly
            );
        }

        return false;
    }
}
