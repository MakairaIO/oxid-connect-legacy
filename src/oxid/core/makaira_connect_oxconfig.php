<?php

class makaira_connect_oxconfig extends makaira_connect_oxconfig_parent
{
    public function setEcondaThemeParam()
    {
        if (isset($this->_aThemeConfigParams) && is_array($this->_aThemeConfigParams)) {
            $this->_aThemeConfigParams['sEcondaRecommendationsAID'] = 'makaira/connect';
        }
    }
}
