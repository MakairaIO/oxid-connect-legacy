<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

use Makaira\Connect\Utils\TokenGenerator;

/**
 * Class makaira_connect_single_sign_on
 */
class makaira_connect_single_sign_on extends oxAdminDetails
{
    const TOKEN_VALIDITY = '10 hours';

    public function render()
    {
        parent::render();

        /** @var \Marm\Yamm\DIC $dic */
        $dic = oxRegistry::get('yamm_dic');

        /** @var TokenGenerator $tokenGenerator */
        $tokenGenerator = $dic['makaira.connect.utils.tokengenerator'];

        $token = $tokenGenerator->generate();
        $userId = $this->getUser()->getId();

        /** @var \Makaira\Connect\Repository\UserRepository $repository */
        $repository = $dic['makaira.connect.repository.user'];

        $repository->addUserToken($userId, $token, self::TOKEN_VALIDITY);

        $applicationUrl = oxRegistry::getConfig()->getShopConfVar(
            'makaira_application_url',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );
        $applicationUrl = rtrim($applicationUrl, '/');

        $iframeUrl = "{$applicationUrl}/?token={$token}";
        $this->addTplParam('applicationUrl', $iframeUrl);
        $this->addTplParam('iframeUrl', $iframeUrl);

        return 'makaira_connect_single_sign_on.tpl';
    }
}
