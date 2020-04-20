<?php

namespace Makaira\Connect;

use Makaira\Connect\Repository\ProductRepository;
use Makaira\Connect\Repository\VariantRepository;
use Makaira\Connect\Repository\CategoryRepository;

/**
 * @group integration
 */
class ConnectTest extends IntegrationTest
{
    public function testGetProductRepositoryWithModifierConfiguration()
    {
        $container = $this->getContainer();
        // $e = $container->get('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        // var_dump($e); die;

        $this->assertInstanceOf(
            ProductRepository::class,
            $container->get(ProductRepository::class)
        );
    }

    public function testGetVariantRepositoryWithModifierConfiguration()
    {
        $container = $this->getContainer();
        $this->assertInstanceOf(
            VariantRepository::class,
            $container->get(VariantRepository::class)
        );
    }

    public function testGetCategoryRepositoryWithModifierConfiguration()
    {
        $container = $this->getContainer();
        $this->assertInstanceOf(
            CategoryRepository::class,
            $container->get(CategoryRepository::class)
        );
    }
}
