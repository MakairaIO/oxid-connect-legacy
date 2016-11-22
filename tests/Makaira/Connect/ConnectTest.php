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
        $this->assertInstanceOf(
            ProductRepository::class,
            $container['makaira.connect.repository.product']
        );
    }

    public function testGetVariantRepositoryWithModifierConfiguration()
    {
        $container = $this->getContainer();
        $this->assertInstanceOf(
            VariantRepository::class,
            $container['makaira.connect.repository.variant']
        );
    }

    public function testGetCategoryRepositoryWithModifierConfiguration()
    {
        $container = $this->getContainer();
        $this->assertInstanceOf(
            CategoryRepository::class,
            $container['makaira.connect.repository.category']
        );
    }
}
