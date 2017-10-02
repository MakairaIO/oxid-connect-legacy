<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

use Makaira\Connect\SearchHandler;
use Makaira\Constraints;
use Makaira\Query;

/**
 * Class makaira_connect_autosuggester
 */
class makaira_autosuggester
{
    private $adapter;

    public function __construct($adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Search for search term and build json response
     *
     * @param string $searchPhrase
     *
     * @return array
     */
    public function search($searchPhrase = "")
    {
        $query                     = $this->adapter->prepareQuery();
        $query->searchPhrase       = $searchPhrase;
        $query->enableAggregations = false;
        $query->isSearch           = true;
        $query->count              = 7;

        $dic = oxRegistry::get('yamm_dic');
        /** @var SearchHandler $searchHandler */
        $searchHandler = $dic['makaira.connect.searchhandler'];

        $result = $searchHandler->search($query);

        return $this->adapter->prepareResults($result);
    }
}
