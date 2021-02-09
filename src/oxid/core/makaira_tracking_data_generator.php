<?php

/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 *
 * @version    0.1
 * @author     Stefan Krenz <krenz@marmalade.de>
 * @link       http://www.marmalade.de
 */

/**
 * This class generates tracking data for certain pages in OXID.
 * To add more pages create a new "generateFor<ControllerClass>" method.
 * For example <ControllerClass> can be "Content" for OXIDs CMS content controller.
 */
class makaira_tracking_data_generator
{
    const TRACKER_URL = 'https://piwik.makaira.io/';

    private static $odoscopeTracking = false;

    /**
     * Generates the tracking data array to send to piwik.
     *
     * @param string $oxidControllerClass OXIDs controller class to generate the tracking data for.
     *
     * @return array
     * @throws oxArticleInputException
     * @throws oxNoArticleException
     */
    public function generate($oxidControllerClass)
    {
        $siteId = oxRegistry::getConfig()->getShopConfVar('makaira_tracking_page_id', null, 'module:makaira/connect');

        if (empty($siteId)) {
            return [];
        }

        $childTrackingData = null;
        $normalizedClass   = $this->normalize($oxidControllerClass);
        $methodName        = "generateFor{$normalizedClass}";

        if (is_callable([$this, $methodName]) && method_exists($this, $methodName)) {
            $childTrackingData = $this->{$methodName}();
        }

        if (oxRegistry::getSession()->getBasket()->isNewItemAdded()) {
            $childTrackingData = $this->generateForBasket();
        }

        if (null === $childTrackingData) {
            $childTrackingData = [['trackPageView']];
        }

        $trackingData = [
            $childTrackingData,
            [
                ['enableLinkTracking'],
                ['setTrackerUrl', "{$this->getTrackerUrl()}/piwik.php"],
                ['setSiteId', $siteId],
            ],
            $this->getCustomTrackingData()
        ];

        if (static::$odoscopeTracking) {
            $trackingData[] = [
                ['trackEvent', 'odoscope', static::$odoscopeTracking['group'], static::$odoscopeTracking['data']]
            ];
        }

        $oxidViewConfig = oxRegistry::get('oxviewconfig');
        if ($oxidViewConfig instanceof makaira_connect_oxviewconfig) {
            foreach ($oxidViewConfig->getExperiments() as $experiment => $variation) {
                $trackingData[] = [['trackEvent', 'abtesting', $experiment, $variation]];
            }
        }

        return array_merge(...$trackingData);
    }

    public static function setOdoscopeData($odoscopeData)
    {
        static::$odoscopeTracking = $odoscopeData;
    }

    /**
     * Hook to add custom data to Piwik/Matomo tracking.
     *
     * @return array
     */
    protected function getCustomTrackingData()
    {
        return [];
    }

    /**
     * Normalizes OXIDs controller class name for the method call.
     *
     * @param string $className OXIDs controller class name.
     *
     * @return string
     */
    protected function normalize($className)
    {
        if (0 === strpos($className, 'ox')) {
            $className = preg_replace('/^ox/', '', $className);
        }

        return ucfirst($className);
    }

    /**
     * Generates tracking data for OXIDs "basket" controller or if a new item was added to the cart.
     *
     * @return array
     * @throws oxArticleInputException
     * @throws oxNoArticleException
     */
    protected function generateForBasket()
    {
        $cart     = oxRegistry::getSession()->getBasket();
        $cartData = $this->createCartTrackingData($cart);

        $cartData[] = [
            'trackEcommerceCartUpdate',
            $cart->getPrice()->getBruttoPrice(),
        ];

        return $cartData;
    }

    /**
     * Creates tracking data from the shopping cart. Used for cart updates and the order.
     *
     * @param oxBasket $cart
     *
     * @return array
     * @throws oxArticleInputException
     * @throws oxNoArticleException
     */
    protected function createCartTrackingData(oxBasket $cart)
    {
        $cartData = [];

        /** @var \oxBasketItem $cartItem */
        foreach ($cart->getContents() as $cartItem) {
            $product  = $cartItem->getArticle();
            $category = $product->getCategory();

            $cartData[] = [
                'addEcommerceItem',
                $product->oxarticles__oxartnum->value,
                $cartItem->getTitle(),
                $category->oxcategories__oxtitle->value,
                $cartItem->getUnitPrice()->getBruttoPrice(),
                $cartItem->getAmount(),
            ];
        }

        return $cartData;
    }

    /**
     * Returns the URL to Makaira's tracking tool.
     *
     * @return string
     */
    public function getTrackerUrl()
    {
        return rtrim(self::TRACKER_URL, '/');
    }

    /**
     * Generates tracking data for a customer search.
     *
     * @return array
     */
    protected function generateForSearch()
    {
        /** @var \Search $oxidController */
        $oxidController = oxRegistry::getConfig()->getTopActiveView();

        return [
            [
                'trackSiteSearch',
                $oxidController->getSearchParamForHtml(),
                false,
                $oxidController->getArticleCount(),
            ],
        ];
    }

    /**
     * Generates tracking data for a completed order.
     *
     * @return array
     * @throws oxArticleInputException
     * @throws oxNoArticleException
     */
    protected function generateForThankyou()
    {
        /** @var \Thankyou $oxidController */
        $oxidController = oxRegistry::getConfig()->getTopActiveView();

        $cart     = $oxidController->getBasket();
        $cartData = $this->createCartTrackingData($cart);
        $order    = $oxidController->getOrder();

        $cartData[] = [
            'trackEcommerceOrder',
            $order->oxorder__oxordernr->value,
            $order->getTotalOrderSum(),
            $cart->getDiscountedProductsBruttoPrice(),
            ($order->oxorder__oxartvatprice1->value + $order->oxorder__oxartvatprice2->value),
            ($order->getOrderDeliveryPrice()->getBruttoPrice() +
                $order->getOrderPaymentPrice()->getBruttoPrice() +
                $order->getOrderWrappingPrice()->getBruttoPrice()),
            $order->oxorder__oxdiscount->value,
        ];

        return $cartData;
    }

    /**
     * Generates tracking data if the requested page was not found.
     *
     * @return array
     */
    protected function generateForUBase()
    {
        $queryString = ('' != $_SERVER['QUERY_STRING']) ? "?{$_SERVER['QUERY_STRING']}" : '';
        $url         = urlencode(ltrim($_SERVER['REQUEST_URI'], '/') . $queryString);
        $referer     = urlencode($_SERVER['HTTP_REFERER']);

        return [
            [
                'setDocumentTitle',
                "404/URL = {$url}/From = {$referer}",
            ],
        ];
    }

    /**
     * Generates the tracking data for a product details page.
     *
     * @return array
     */
    protected function generateForDetails()
    {
        /** @var oxwArticleDetails $oxidDetailsWidget */
        $oxidDetailsWidget = oxNew('oxwarticledetails');

        $product  = $oxidDetailsWidget->getProduct();
        $category = $product->getCategory();

        return [
            [
                'setEcommerceView',
                $product->oxarticles__oxartnum->value,
                $product->oxarticles__oxtitle->value,
                $category->oxcategories__oxtitle->value,
            ],
            ['trackPageView']
        ];
    }

    /**
     * generates the tracking data for a category page.
     *
     * @return array
     */
    protected function generateForAlist()
    {
        /** @var \aList $listController */
        $listController = oxRegistry::getConfig()->getTopActiveView();
        if (null === $listController) {
            $listController = oxRegistry::get('alist');
        }

        return [
            ['setEcommerceView', false, false, $listController->getTitle()],
            ['trackPageView']
        ];
    }

}
