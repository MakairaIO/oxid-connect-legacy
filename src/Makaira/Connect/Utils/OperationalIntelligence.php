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

use Makaira\Constraints;
use Makaira\Query;

class OperationalIntelligence
{
    const COOKIE_NAME_ID       = 'oiID';
    const COOKIE_NAME_TIMEZONE = 'oiLocalTimeZone';

    private $utilsServer;

    public function __construct(\oxUtilsServer $utilsServer)
    {
        $this->utilsServer = $utilsServer;
    }

    public function apply(Query $query)
    {
        $query->constraints[ Constraints::OI_USER_AGENT ]    = $this->getUserAgentString();
        $query->constraints[ Constraints::OI_USER_IP ]       = $this->getUserIP();
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

            $this->utilsServer->setOxCookie(self::COOKIE_NAME_ID, $userID, time() + 86400);
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
        $userIP =
            isset($_SERVER['X_FORWARDED_FOR']) ? $_SERVER['X_FORWARDED_FOR'] :
                isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

        return is_string($userIP) ? $userIP : '';
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
