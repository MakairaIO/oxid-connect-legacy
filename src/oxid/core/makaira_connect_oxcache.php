<?php

/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     René Mäkeler <maekeler@marmalade.de>
 * Author URI: http://www.marmalade.de
 */
class makaira_connect_oxcache extends makaira_connect_oxcache_parent
{
    /**
     * Returns, whether a given class is cachable
     *
     * @param string $sViewName a class name
     *
     * @return bool
     */
    public function isViewCacheable($sViewName)
    {
        if (in_array($sViewName, ['alist', 'manufacturerlist', 'search'])) {
            $oViewConfig   = oxRegistry::get('oxViewConfig');
            $cookieFilters = $oViewConfig->loadMakairaFilterFromCookie();

            if (count($cookieFilters)) {
                /*
                Problem: There could be active filters in the cookie from previous pages. We have to check if entries
                         in the cookie refer to the current page before disabling the caching-mechanism.

                => cookie is structured like this:
                    {
                        "category": {                                - category|manufacturer|search
                            "30e44ab83fdee7564.23264141": {          - catId|manufId|searchTerm
                                "color": [
                                    "blue"
                                ]
                            }
                        }
                    }
                */

                $isFilterActive = false;
                foreach ($cookieFilters as $filterType => $filterValues) {
                    switch ($filterType) {
                        case 'category':
                            $isFilterActive = in_array($oViewConfig->getActCatId(), array_keys($filterValues));
                            break;
                        case 'manufacturer':
                            $isFilterActive = in_array($oViewConfig->getActManufacturerId(), array_keys($filterValues));
                            break;
                        case 'search':
                            $isFilterActive = in_array($oViewConfig->getActSearchParam(), array_keys($filterValues));
                            break;
                    }
                }

                if ($isFilterActive) {
                    return false;
                }
            }
        }

        return parent::isViewCacheable($sViewName);
    }
}
