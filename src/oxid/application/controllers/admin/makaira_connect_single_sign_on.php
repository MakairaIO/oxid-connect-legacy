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
use Makaira\Connect\Repository\UserRepository;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;

/**
 * Class makaira_connect_single_sign_on
 */
class makaira_connect_single_sign_on extends oxAdminDetails
{
    const TOKEN_VALIDITY = '10 hours';

    public function render()
    {
        parent::render();

        $container = ContainerFactory::getInstance()->getContainer();
        /** @var TokenGenerator $tokenGenerator */
        $tokenGenerator = $container->get(TokenGenerator::class);

        $token = $tokenGenerator->generate();
        $userId = $this->getUser()->getId();

        /** @var UserRepository $repository */
        $repository = $container->get(UserRepository::class);

        $repository->addUserToken($userId, $token, self::TOKEN_VALIDITY);

        $instanceName = oxRegistry::getConfig()->getShopConfVar(
            'makaira_instance',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );
        $applicationUrl = oxRegistry::getConfig()->getShopConfVar(
            'makaira_application_url',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );
        $applicationUrl = rtrim($applicationUrl, '/');

        $this->addTplParam('hasApplicationUrl', (bool) $applicationUrl);
        $iframeUrl = "{$applicationUrl}/?token={$token}&instance={$instanceName}";
        $this->addTplParam('applicationUrl', $iframeUrl);
        $this->addTplParam('iframeUrl', $iframeUrl);

        return 'makaira_connect_single_sign_on.tpl';
    }
}
