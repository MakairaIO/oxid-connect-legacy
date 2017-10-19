<?php

/**
 * Created by PhpStorm.
 * User: support
 * Date: 19.10.17
 * Time: 14:55
 */
class makaira_connect_oxseodecoder extends makaira_connect_oxseodecoder_parent
{
    public function decodeUrl($seoUrl)
    {
        // check for filter values in url to read filter parameters from url
        $iUnderscorePos = strpos($seoUrl, "_");
        if ($iUnderscorePos !== false) {
            preg_match_all("/([^_]*\/)([^\/]*_[^\/]*)/", $seoUrl, $aMatches);

            $filter = [];
            if (isset($aMatches[2])) {
                foreach ($aMatches[2] as $filterParam) {
                    $parts = explode('_', $filterParam);
                    $value = array_pop($parts);
                    $key   = implode('_', $parts);

                    if (isset($filter[$key])) {
                        $value = array_merge((array)$filter[$key], (array)$value);
                    }
                    $filter[$key] = $value;
                }

                $seoUrl = $aMatches[1][0];
            }
        }

        $decodedUrl   = parent::decodeUrl($seoUrl);
        $oxViewConfig = oxNew('oxViewConfig');
        $cookieFilter = $oxViewConfig->buildCookieFilter($decodedUrl["cl"], $filter, $decodedUrl["cnid"], $decodedUrl["mnid"], null);
        $oxViewConfig->saveMakairaFilterToCookie($cookieFilter);
        return $decodedUrl;
    }

}