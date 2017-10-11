<?php

/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */
class makaira_connect_oxviewconfig extends makaira_connect_oxviewconfig_parent
{
    public function getAggregationFilter()
    {
        $categoryId     = $this->getActCatId();
        $manufacturerId = $this->getActManufacturerId();
        $searchParam    = $this->getActSearchParam();
        $className      = $this->getActiveClassName();

        // get filter cookie
        $cookieFilter = $this->loadMakairaFilterFromCookie();
        // get filter from form submit
        $requestFilter = (array) oxRegistry::getConfig()->getRequestParameter('makairaFilter');

        if (!empty($requestFilter)) {
            switch ($className) {
                case 'alist':
                    $cookieFilter['category'][$categoryId] = $requestFilter;
                    break;
                case 'manufacturerlist':
                    $cookieFilter['manufacturer'][$manufacturerId] = $requestFilter;
                    break;
                case 'search':
                    $cookieFilter['search'][$searchParam] = $requestFilter;
                    break;
            }
            $this->saveMakairaFilterToCookie($cookieFilter);

            return $requestFilter;
        }

        if (empty($cookieFilter)) {
            return [];
        }

        if (isset($categoryId)) {
            return isset($cookieFilter['category'][$categoryId]) ? $cookieFilter['category'][$categoryId] : [];
        } elseif (isset($manufacturerId)) {
            return isset($cookieFilter['manufacturer'][$manufacturerId]) ?
                $cookieFilter['manufacturer'][$manufacturerId] : [];
        } elseif (isset($searchParam)) {
            return isset($cookieFilter['search'][$searchParam]) ? $cookieFilter['search'][$searchParam] : [];
        }

        return [];
    }

    public function resetMakairaFilter($type, $ident)
    {
        $cookieFilter = $this->loadMakairaFilterFromCookie();
        unset($cookieFilter[$type][$ident]);
        $this->saveMakairaFilterToCookie($cookieFilter);
    }

    public function getMakairaMainStylePath()
    {
        $modulePath = $this->getModulePath('makaira/connect') . '';
        $file       = glob($modulePath . 'out/dist/*.css');

        return substr(reset($file), strlen($modulePath));
    }

    public function getMakairaMainScriptPath()
    {
        $modulePath = $this->getModulePath('makaira/connect') . '';
        $file       = glob($modulePath . 'out/dist/*.js');

        return substr(reset($file), strlen($modulePath));
    }

    /**
     * @return array|mixed
     */
    private function loadMakairaFilterFromCookie()
    {
        $oxUtilsServer = oxRegistry::get('oxUtilsServer');
        $rawCookieFilter = $oxUtilsServer->getOxCookie('makairaFilter');
        $cookieFilter    = !empty($rawCookieFilter) ? json_decode(html_entity_decode($rawCookieFilter), true) : [];
        // for some reason cookie is double html entity encoded in widget context
        if (null === $cookieFilter) {
            $cookieFilter = json_decode(html_entity_decode(html_entity_decode($rawCookieFilter)), true);
        }

        return $cookieFilter;
    }

    /**
     * @param $cookieFilter
     */
    private function saveMakairaFilterToCookie($cookieFilter)
    {
        $oxUtilsServer = oxRegistry::get('oxUtilsServer');
        $oxUtilsServer->setOxCookie('makairaFilter', json_encode($cookieFilter));
    }
}
