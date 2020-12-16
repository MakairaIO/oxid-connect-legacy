<?php

class makaira_cookie_utils
{
    private static $bannerEnabled;

    public function hasCookiesAccepted()
    {
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
