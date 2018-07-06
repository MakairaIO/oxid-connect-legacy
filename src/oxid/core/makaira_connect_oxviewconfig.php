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
    protected static $makairaFilter = null;

    protected $activeFilter = null;

    protected $generatedFilterUrl = [];

    public function redirectMakairaFilter($baseUrl, $disableSeoFilter = false)
    {
        $useSeoFilter = $this->getConfig()->getShopConfVar(
            'makaira_connect_seofilter',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );

        $filterParams = $this->getAggregationFilter();

        if ($disableSeoFilter || !$useSeoFilter || !oxRegistry::getUtils()->seoIsActive()) {
            $finalUrl = $this->generateFilterUrl($baseUrl, $filterParams);
            oxRegistry::getUtils()->redirect($finalUrl, false, 302);
            return;
        }

        $finalUrl = $this->generateSeoUrlFromFilter($baseUrl, $filterParams);

        oxRegistry::getUtils()->redirect($finalUrl, false, 302);
    }

    public function getAggregationFilter()
    {
        if (null !== $this->activeFilter) {
            return $this->activeFilter;
        }

        $this->activeFilter = [];
        $categoryId     = $this->getActCatId();
        $manufacturerId = $this->getActManufacturerId();
        $searchParam    = $this->getActSearchParam();
        $className      = $this->getActiveClassName();

        // get filter cookie
        $cookieFilter = $this->loadMakairaFilterFromCookie();
        // get filter from form submit
        $requestFilter = (array) oxRegistry::getConfig()->getRequestParameter('makairaFilter', true);

        $isFilterAction = oxRegistry::getConfig()->getRequestParameter('isFilterAction');

        if ($isFilterAction || !empty($requestFilter)) {
            // TODO Handle range filter in frontend and remove this
            $requestFilter = $this->filterRangeValues($requestFilter);

            $cookieFilter = $this->buildCookieFilter($className, $requestFilter, $categoryId, $manufacturerId, $searchParam);
            $this->saveMakairaFilterToCookie($cookieFilter);
            $this->activeFilter = $requestFilter;

            return $this->activeFilter;
        }

        if (empty($cookieFilter)) {
            return $this->activeFilter;
        }

        if (isset($searchParam) && 'search' == $className) {
            $this->activeFilter = isset($cookieFilter['search'][$searchParam]) ? $cookieFilter['search'][$searchParam] : [];
        } elseif (isset($categoryId)) {
            $this->activeFilter = isset($cookieFilter['category'][$categoryId]) ? $cookieFilter['category'][$categoryId] : [];
        } elseif (isset($manufacturerId)) {
            $this->activeFilter = isset($cookieFilter['manufacturer'][$manufacturerId]) ?
                $cookieFilter['manufacturer'][$manufacturerId] : [];
        }

        return $this->activeFilter;
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
    public function loadMakairaFilterFromCookie()
    {
        if (null !== static::$makairaFilter) {
            return static::$makairaFilter;
        }
        $oxUtilsServer   = oxRegistry::get('oxUtilsServer');
        $lang            = oxRegistry::getLang()->getLanguageAbbr();
        $rawCookieFilter = $oxUtilsServer->getOxCookie('makairaFilter_' . $lang);
        $cookieFilter    = !empty($rawCookieFilter) ? json_decode(base64_decode($rawCookieFilter), true) : [];

        static::$makairaFilter = (array)$cookieFilter;

        return static::$makairaFilter;
    }

    /**
     * @param $cookieFilter
     */
    public function saveMakairaFilterToCookie($cookieFilter)
    {
        static::$makairaFilter = $cookieFilter;
        $oxUtilsServer         = oxRegistry::get('oxUtilsServer');
        $lang                  = oxRegistry::getLang()->getLanguageAbbr();
        $oxUtilsServer->setOxCookie('makairaFilter_' . $lang, base64_encode(json_encode($cookieFilter)));
    }

    public function savePageNumberToCookie()
    {
        $pageNumber    = oxRegistry::getConfig()->getRequestParameter('pgNr');
        $oxUtilsServer = oxRegistry::get('oxUtilsServer');
        $oxUtilsServer->setOxCookie('makairaPageNumber', $pageNumber);
    }

    /**
     * @param $className
     * @param $requestFilter
     * @param $cookieFilter
     * @param $categoryId
     * @param $manufacturerId
     * @param $searchParam
     * @return mixed
     */
    public function buildCookieFilter($className, $requestFilter, $categoryId, $manufacturerId, $searchParam)
    {
        $cookieFilter = [];
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
        return $cookieFilter;
    }

    /**
     * @param $baseUrl
     * @param $filterParams
     *
     * @return string
     */
    public function generateSeoUrlFromFilter($baseUrl, $filterParams)
    {
        if (isset($this->generatedFilterUrl[$baseUrl])) {
            return $this->generatedFilterUrl[$baseUrl];
        }

        if (empty($filterParams)) {
            return $baseUrl;
        }

        $useSeoFilter = $this->getConfig()->getShopConfVar(
            'makaira_connect_seofilter',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );
        if (!$useSeoFilter) {
            $this->generatedFilterUrl[$baseUrl] = $baseUrl;
            return $this->generatedFilterUrl[$baseUrl];
        }

        $path = [];
        foreach ($filterParams as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $path[] = "{$key}_" . urlencode($item);
                }
            } else {
                $path[] = "{$key}_" . urlencode($value);
            }
        }
        $filterString = implode('/', $path);

        $parsedUrl = parse_url($baseUrl);

        $path       = rtrim($parsedUrl['path'], '/');
        $pageNumber = '';
        if (preg_match('#(.*)/(\d+)$#', $path, $matches)) {
            $path       = $matches[1];
            $pageNumber = $matches[2] . '/';
        }
        $path = implode('/', [$path, $filterString, $pageNumber]);

        $query = $parsedUrl['query'] ? "?{$parsedUrl['query']}" : "";

        $this->generatedFilterUrl[$baseUrl] = "{$parsedUrl['scheme']}://{$parsedUrl['host']}{$path}{$query}";

        return $this->generatedFilterUrl[$baseUrl];
    }

    /**
     * @param $baseUrl
     * @param $filterParams
     *
     * @return string
     */
    private function generateFilterUrl($baseUrl, $filterParams)
    {
        if (isset($this->generatedFilterUrl[$baseUrl])) {
            return $this->generatedFilterUrl[$baseUrl];
        }

        $params = [
            'makairaFilter' => $filterParams,
        ];
        $filterQuery  = http_build_query($params);

        $parsedUrl = parse_url($baseUrl);

        $path = rtrim($parsedUrl['path'], '/') . '/';

        $query = '';
        if ('' !== $parsedUrl['query']) {
            $queryArray = explode('&amp;', $parsedUrl['query']);
            $queryArray      = array_filter(
                $queryArray, function ($part) {
                return stripos($part, 'fnc=redirectmakaira') !== 0;
            });
            $query = implode('&', $queryArray);
        }

        if ('' !== $filterQuery) {
            $query = $query ? "{$query}&{$filterQuery}" : "{$filterQuery}";
        }

        if ('' !== $query) {
            $query = '?' . $query;
        }

        $this->generatedFilterUrl[$baseUrl] = "{$parsedUrl['scheme']}://{$parsedUrl['host']}{$path}{$query}";

        return $this->generatedFilterUrl[$baseUrl];
    }

    /**
     * @param $filterParams
     *
     * @return array
     */
    private function filterRangeValues($filterParams)
    {
        // TODO Handle range filter in frontend and remove this
        foreach ($filterParams as $key => $value) {
            if (false !== ($pos = strrpos($key, '_to'))) {
                if (isset($filterParams[substr($key, 0, $pos) . '_rangemax'])) {
                    if ($value == $filterParams[substr($key, 0, $pos) . '_rangemax']) {
                        unset($filterParams[$key]);
                        continue;
                    }
                }
            }
            if (false !== ($pos = strrpos($key, '_from'))) {
                if (isset($filterParams[substr($key, 0, $pos) . '_rangemin'])) {
                    if ($value == $filterParams[substr($key, 0, $pos) . '_rangemin']) {
                        unset($filterParams[$key]);
                        continue;
                    }
                }
            }
        }

        $filteredFilterParams = [];
        foreach ($filterParams as $key => $value) {
            if ((false !== strrpos($key, '_rangemin')) || (false !== strrpos($key, '_rangemax'))) {
                continue;
            }
            $filteredFilterParams[$key] = $value;
        }

        $filterParams         = $filteredFilterParams;

        return $filterParams;
    }
}
