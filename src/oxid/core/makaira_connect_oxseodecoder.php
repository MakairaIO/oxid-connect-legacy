<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Alexander Kraus <kraus@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

class makaira_connect_oxseodecoder extends makaira_connect_oxseodecoder_parent
{
    public function decodeUrl($seoUrl)
    {
        // check for filter values in url to read filter parameters from url
        $iUnderscorePos = strpos($seoUrl, "_");
        if ($iUnderscorePos === false) {
            return parent::decodeUrl($seoUrl);
        }

        preg_match_all("#([^_]*/)([^/]*_[^/]*)#", $seoUrl, $aMatches);
        if (!isset($aMatches[2])) {
            return parent::decodeUrl($seoUrl);
        }

        // check setting late because other checks should be faster than database access
        $useSeoFilter = $this->getConfig()->getShopConfVar(
            'makaira_connect_seofilter',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );
        if (!$useSeoFilter) {
            return parent::decodeUrl($seoUrl);
        }

        $pageNumber = '';
        if (preg_match('#.*/(\d+)$#', rtrim($seoUrl, '/'), $matches)) {
            $pageNumber = $matches[1] . '/';
        }

        $filter = [];
        foreach ($aMatches[2] as $filterParam) {
            $parts = explode('_', $filterParam);
            $value = urldecode(array_pop($parts));
            $key   = implode('_', $parts);

            if (isset($filter[ $key ])) {
                $value = array_merge((array) $filter[ $key ], (array) $value);
            }
            $filter[ $key ] = $value;
        }

        $seoUrl = $aMatches[1][0] . $pageNumber;

        $decodedUrl = parent::decodeUrl($seoUrl);
        $oxViewConfig = oxNew('oxViewConfig');
        $cookieFilter =
            $oxViewConfig->buildCookieFilter(
                $decodedUrl["cl"],
                $filter,
                $decodedUrl["cnid"],
                $decodedUrl["mnid"],
                null
            );
        $oxViewConfig->saveMakairaFilterToCookie($cookieFilter);

        return $decodedUrl;
    }
}
