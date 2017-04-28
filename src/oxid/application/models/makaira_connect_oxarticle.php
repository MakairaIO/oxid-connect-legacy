<?php

use Makaira\Connect\Analyse\EventDispatcher;

class makaira_connect_oxarticle extends makaira_connect_oxarticle_parent
{

    public function save()
    {
        $result = parent::save();
        if ($result) {
            $this->touch();
        }
        return $result;
    }

    public function delete($sOXID = null)
    {
        $this->touch($sOXID);
        return parent::delete($sOXID);
    }

    public function getParentId($oxid = null)
    {
        if (!isset($oxid)) {
            return parent::getParentId();
        } else {
            /** @var \Makaira\Connect\DatabaseInterface $db */
            $db = oxRegistry::get('yamm_dic')['oxid.database'];
            $parentId = $db->query('SELECT OXPARENTID FROM oxarticles WHERE OXID = :id', ['id' => $sOXID]);
            return empty($parentId) ? null : $parentId[0]['OXPARENTID'];
        }
    }

    public function touch($oxid = null)
    {
        $id = $oxid ?: $this->getId();
        if ($parentId = $this->getParentId($oxid)) {
            $this->getRepository()->touch('product', $parentId);
            $this->getRepository()->touch('variant', $id);
        } else {
            $this->getRepository()->touch('product', $id);
            /** @var \Makaira\Connect\DatabaseInterface $db */
            $db = oxRegistry::get('yamm_dic')['oxid.database'];
            $variants = $db->query(
                'SELECT OXID FROM oxarticles WHERE OXPARENTID = :parentId',
                ['parentId' => $id]
            );
            foreach ($variants as $variant) {
                $this->getRepository()->touch('variant', $variant['OXID']);
            }
        }
    }

    /**
     * @return \Makaira\Connect\Repository
     */
    private function getRepository()
    {
        return oxRegistry::get('yamm_dic')['makaira.connect.repository'];
    }

    /**
     * @param int $iRating
     */
    public function addToRatingAverage($iRating)
    {
        parent::addToRatingAverage($iRating);

        $parameters         = ['product_rated' => $this->getId(), 'rating_value' => $iRating];
        $this->dispatchTrackingEvent($parameters);
    }

    /**
     * @param int|float $dAmount
     *
     * @return mixed
     */
    public function updateSoldAmount($dAmount = 0)
    {
        $parentResult = parent::updateSoldAmount($dAmount);

        // we only want to track buys not canceled orders
        if ($dAmount <= 0) {
            return $parentResult;
        }

        $parameters         = ['product_bought' => $this->getId(), 'amount' => ceil($dAmount)];
        $this->dispatchTrackingEvent($parameters);

        return $parentResult;
    }

    /**
     * @param $parameters
     */
    private function dispatchTrackingEvent($parameters)
    {
        $dic = oxRegistry::get('yamm_dic');
        /** @var EventDispatcher $trackingDispatcher */
        $trackingDispatcher = $dic['makaira.connect.analyse.event_dispatcher'];
        $trackingDispatcher->dispatch($parameters);
    }
}
