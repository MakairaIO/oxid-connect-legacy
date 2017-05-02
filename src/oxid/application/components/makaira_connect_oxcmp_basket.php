<?php

/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */
class makaira_connect_oxcmp_basket extends makaira_connect_oxcmp_basket_parent
{
    /**
     * Track add to basket
     *
     * @param string|null $sProductId
     * @param float|null $dAmount
     * @param array|null $aSel
     * @param array|null $aPersParam
     * @param bool $blOverride
     *
     * @return mixed
     */
    public function tobasket($sProductId = null, $dAmount = null, $aSel = null, $aPersParam = null, $blOverride = false)
    {
        $aProducts   = $this->_getItems($sProductId, $dAmount, $aSel, $aPersParam, $blOverride);

        if (is_array($aProducts)) {
            $dic = oxRegistry::get('yamm_dic');
            /** @var EventDispatcher $trackingDispatcher */
            $trackingDispatcher = $dic['makaira.connect.analyse.event_dispatcher'];

            // TODO: We should send all products with one request. Define parameters for single request.
            foreach ($aProducts as $id => $product) {
                $parameters = ['add_to_basket' => $id, 'amount' => $product['am']];
                $trackingDispatcher->dispatch($parameters);
            }
        }

        return parent::tobasket($sProductId, $dAmount, $aSel, $aPersParam, $blOverride);
    }
}
