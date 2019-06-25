<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Modifier;
use Makaira\Connect\Type;

class MetaDataModifier extends Modifier
{
    /**
     * @var DatabaseInterface
     */
    private $db;

    /**
     * @var \oxLang
     */
    private $lang;

    /**
     * @var \oxUBase
     */
    private $alist;

    /**
     * @var \oxUBase
     */
    private $details;

    /**
     * @var \oxUBase
     */
    private $mlist;

    /**
     * @var \oxUBase
     */
    private $ubase;

    public function __construct(
        DatabaseInterface $database,
        \oxLang $oxLang,
        \oxUBase $oxUBaseAList,
        \oxUBase $oxUBaseDetails,
        \oxUBase $oxUBaseMList
    ) {
        $this->db      = $database;
        $this->lang    = $oxLang;
        $this->alist   = $oxUBaseAList;
        $this->details = $oxUBaseDetails;
        $this->mlist   = $oxUBaseMList;
    }

    public function apply(Type $entity)
    {
        switch ($this->getDocType()) {
            case "product":
            case "variant":
                $this->ubase = $this->details;

                $this->resetMetaData();
                $this->setCategory($entity->mainCategory);
                $this->buildCategoryTree($entity->mainCategory);
                $this->setArticle($entity->id);
                $this->setManufacturer($entity->mak_manufacturerId);
                $this->setSeoObjectId($entity->id);

                $entity = $this->getMetaKeywords($entity);
                $entity = $this->getMetaDescription($entity);
                break;

            case "category":
                $this->ubase = $this->alist;

                $this->resetMetaData();
                $this->setCategory($entity->id);
                $this->buildCategoryTree($entity->id);
                $this->setArticle(null);
                $this->setManufacturer(null);
                $this->setSeoObjectId($entity->id);

                $entity = $this->getMetaKeywords($entity);
                $entity = $this->getMetaDescription($entity);
                break;

            case "manufacturer":
                $this->ubase = $this->mlist;

                $this->resetMetaData();
                $this->setCategory(null);
                $this->resetCategoryTree();
                $this->setArticle(null);
                $this->setManufacturer($entity->id);
                $this->setSeoObjectId($entity->id);

                $entity = $this->getMetaKeywords($entity);
                $entity = $this->getMetaDescription($entity);
                break;

            default:
                break;
        }

        return $entity;
    }

    private function setCategory($oxid)
    {
        $oxCategory = \oxRegistry::get('oxCategory');
        $oxCategory->load($oxid);

        $this->ubase->setActCategory($oxCategory);
        $this->ubase->setActiveCategory($oxCategory);

        return $oxCategory;
    }

    private function setArticle($oxid)
    {
        $oxArticle = \oxRegistry::get('oxArticle');
        $oxArticle->load($oxid);

        $this->ubase->setViewProduct($oxArticle);

        return $oxArticle;
    }

    private function setManufacturer($oxid)
    {
        $oxManufacturer = \oxRegistry::get('oxManufacturer');
        $oxManufacturer->load($oxid);

        $this->ubase->setActManufacturer($oxManufacturer);

        return $oxManufacturer;
    }

    private function setSeoObjectId($oxid)
    {
        $this->ubase->setMakSeoObjectId($oxid);
    }

    private function resetMetaData()
    {
        $this->ubase->resetMetaData();
    }

    private function resetCategoryTree()
    {
        $this->ubase->resetCategoryTree();
    }

    private function buildCategoryTree($category)
    {
        $this->resetCategoryTree();
        $this->ubase->makBuildCatTree($category);
    }

    private function getMetaKeywords(Type $entity)
    {
        $entity->mak_meta_keywords = '';
        if ($metaKeywords = $this->ubase->getMetaKeywords()) {
            $entity->mak_meta_keywords = $metaKeywords;
        }

        return $entity;
    }

    private function getMetaDescription(Type $entity)
    {
        $entity->mak_meta_description = '';
        if ($metaDescription = $this->ubase->getMetaDescription()) {
            $entity->mak_meta_description = $metaDescription;
        }

        return $entity;
    }
}
