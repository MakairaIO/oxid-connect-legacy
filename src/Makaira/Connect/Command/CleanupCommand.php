<?php declare(strict_types=1);

/**
 * This file is part of a Makaira GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Martin Schnabel <ms@marmalade.group>
 * Author URI: https://www.makaira.io/
 */

namespace Makaira\Connect\Command;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Console\AbstractShopAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Makaira\Connect\Repository;

class CleanupCommand extends AbstractShopAwareCommand
{
    protected function configure()
    {
        $this->setName('makaira:cleanup')
        ->setDescription('Cleanup')
        ->setHelp('Clean up changes list older then 1 day');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Cleanup');
        $container = ContainerFactory::getInstance()->getContainer();

        $repo = $container->get(Repository::class);
        $repo->cleanup();

        $output->writeln('Done.');
    }
}
