<?php

/**
 * This file is part of a Makaira GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Martin Schnabel <ms@marmalade.group>
 * Author URI: https://www.makaira.io/
 */

namespace Makaira\Connect\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Makaira\Connect\Repository;
use Makaira\Connect\Connect;

class TouchAllCommand extends Command
{
    protected function configure()
    {
        $this->setName('makaira:touch-all')
        ->setDescription('Touch all')
        ->setHelp('Trigger update of everything');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Touch all');
        $container = Connect::getContainerFactory()->getContainer();

        $repo = $container->get(Repository::class);
        $repo->touchAll();

        $output->writeln('Done.');
    }
}
