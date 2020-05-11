<?php

/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Thomas Uhlig <uhlig@marmalade.de>
 * Author URI: http://www.marmalade.de
 */
class makaira_connect_oxoutput extends makaira_connect_oxoutput_parent
{
    /**
     * @var array
     */
    private static $trackingData;

    /**
     * @param $sValue
     * @param $sClassName
     *
     * @return string|string[]
     * @throws oxArticleInputException
     * @throws oxNoArticleException
     */
    public function process($sValue, $sClassName)
    {
        $output = parent::process($sValue, $sClassName);

        if (
            false === strpos($output, '</head>') ||
            !oxRegistry::get(makaira_cookie_utils::class)->hasCookiesAccepted()
        ) {
            return $output;
        }

        $trackingData = $this->getTrackingData();

        if (empty($trackingData)) {
            return $output;
        }

        /** @var makaira_tracking_data_generator $trackingDataGenerator */
        $trackingDataGenerator = oxRegistry::get('makaira_tracking_data_generator');

        $trackerUrl = json_encode($trackingDataGenerator->getTrackerUrl());

        $trackingHtml = '<script type="text/javascript">var _paq = _paq || [];';

        foreach ($trackingData as $trackingPart) {
            $trackingHtml .= '_paq.push(' . json_encode($trackingPart) . ');';
        }

        $trackingHtml .= "var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript';";
        $trackingHtml .= "g.defer=true; g.async=true; g.src={$trackerUrl}+'/piwik.js'; s.parentNode.insertBefore(g,s);";
        $trackingHtml .= '</script>';

        $output = str_replace('</head>', "{$trackingHtml}</head>", $output);

        return $output;
    }

    /**
     * @return array
     * @throws oxArticleInputException
     * @throws oxNoArticleException
     */
    protected function getTrackingData()
    {
        if (null === self::$trackingData) {
            /** @var makaira_tracking_data_generator $trackingDataGenerator */
            $trackingDataGenerator = oxRegistry::get('makaira_tracking_data_generator');
            $oxidControllerClass   = oxRegistry::getConfig()
                ->getTopActiveView()
                ->getClassName();
            self::$trackingData = $trackingDataGenerator->generate($oxidControllerClass);
        }

        return self::$trackingData;
    }

    /**
     * output data
     *
     * @param string $sName  output name (used in json mode)
     * @param string $output output text/data
     *
     * @return null
     */
    public function output($sName, $output)
    {
        if (
            self::OUTPUT_FORMAT_HTML === $this->_sOutputFormat &&
            oxRegistry::get(makaira_cookie_utils::class)->hasCookiesAccepted()
        ) {
                $closingHead    = "</body>";
                $closingHeadNew = "<script type=\"text/javascript\">
oiOS=new Date().getTimezoneOffset();oiOS=(oiOS<0?\"+\":\"-\")+(\"00\"+parseInt((Math.abs(oiOS/60)))).slice(-2);
document.cookie= \"oiLocalTimeZone=\"+oiOS+\";path=/;\";
</script></body>";

                $output = ltrim($output);
                if (false !== ($pos = stripos($output, $closingHead))) {
                    $output = substr_replace($output, $closingHeadNew, $pos, strlen($closingHead));
                }
            }

        parent::output($sName, $output);
    }
}
