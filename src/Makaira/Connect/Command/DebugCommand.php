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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Makaira\Connect\Repository;
use Makaira\Connect\Connect;

class DebugChangeCommand extends Command
{
    protected function configure()
    {
        $this->setName('makaira:debug-change')
        ->setDescription('Debug change')
        ->setHelp('Clean up changes list older then 1 day')
        ->addArgument('type', InputArgument::REQUIRED, 'product|variant|category|manufacturer')
        ->addArgument('id', InputArgument::REQUIRED, 'ID of entity');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = Connect::getContainerFactory()->getContainer();

        $repo = $container->get(\Makaira\Connect\Repository::class);
        /** @var $repo \Makaira\Connect\Repository */
        $list = [
            [
                'type' => $input->getArgument('type'),
                'id' => $input->getArgument('id'),
                'sequence' => 0
            ]
        ];
        $changes = $repo->getChangesFromList($list, 0, 1);
        if (function_exists('dump')) {
            dump($changes);
        } else {
            var_dump($changes);
        }
    }
}
