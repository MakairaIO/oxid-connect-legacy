<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

namespace Makaira\Connect\Utils;

use Makaira\AbstractQuery;
use Makaira\Constraints;
use makaira_cookie_utils;

class OperationalIntelligence
{
    const COOKIE_NAME_ID       = 'oiID';
    const COOKIE_NAME_TIMEZONE = 'oiLocalTimeZone';

    private $cookieUtils;

    public function __construct(makaira_cookie_utils $cookieUtils)
    {
        $this->cookieUtils = $cookieUtils;
    }

    public function apply(AbstractQuery $query)
    {
        $query->constraints[ Constraints::OI_USER_AGENT ]    = $this->getUserAgentString();
        $query->constraints[ Constraints::OI_USER_IP ]       = $this->anonymizeIp($this->getUserIP());
        $query->constraints[ Constraints::OI_USER_ID ]       = $this->generateUserID();
        $query->constraints[ Constraints::OI_USER_TIMEZONE ] = $this->getUserTimeZone();
    }

    /**
     * Get User ID (set cookie "oiID")
     */
    private function generateUserID()
    {
        /** @var string $userID */
        $userID = isset($_COOKIE[ self::COOKIE_NAME_ID ]) ? $_COOKIE[ self::COOKIE_NAME_ID ] : false;

        if (!$userID || !is_string($userID)) {
            $userID = $this->getUserIP();
            $userID .= $this->getUserAgentString();

            $userID = md5($userID);

            $this->cookieUtils->setCookie(self::COOKIE_NAME_ID, $userID, time() + 86400);
        }

        return $userID;
    }

    /**
     * Get actual User IP
     * 1) from $_SERVER['X_FORWARDED_FOR']
     * 2) from $_SERVER['REMOTE_ADDR']
     */
    private function getUserIP()
    {
        /** @var string $userIP */
        $remoteAddr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $userIP     = isset($_SERVER['X_FORWARDED_FOR']) ? $_SERVER['X_FORWARDED_FOR'] : $remoteAddr;

        return is_string($userIP) ? $userIP : '';
    }

    /**
     * Replaces the last two digits of an IPv4 address with ".0.0".
     *
     * @param string $ip IPv4 address to anonymize.
     *
     * @return string
     */
    private function anonymizeIp($ip)
    {
        return preg_replace('/\.\d+\.\d+$/', '.0.0', $ip);
    }

    /**
     * Get actual User Agent String (raw data)
     */
    private function getUserAgentString()
    {
        /** @var string $userAgent */
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        return is_string($userAgent) ? $userAgent : '';
    }

    /**
     * Get User Time Zone from cookie "oiLocalTimeZone"
     */
    private function getUserTimeZone()
    {
        /** @var string $userTimeZone */
        $userTimeZone = isset($_COOKIE[ self::COOKIE_NAME_TIMEZONE ]) ? $_COOKIE[ self::COOKIE_NAME_TIMEZONE ] : '';

        return is_string($userTimeZone) ? trim($userTimeZone) : '';
    }
}
