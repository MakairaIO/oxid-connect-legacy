<?php

class makaira_connect_oxarticle extends makaira_connect_oxarticle_parent
{

    public function save()
    {
        $result = parent::save();
        $this->touch();
        return $result;
    }

    public function touch()
    {
        if ($this->getParentId()) {
            $this->getProductRepo()->touch($this->getParentId());
            $this->getVariantRepo()->touch($this->getId());
        } else {
            $this->getProductRepo()->touch($this->getId());
            /** @var \Makaira\Connect\DatabaseInterface $db */
            $db = oxRegistry::get('yamm_dic')['makaira.database'];
            $variants = $db->query(
                'SELECT OXID FROM oxarticles WHERE OXPARENTID = :parentId',
                ['parentId' => $this->getId()]
            );
            foreach ($variants as $variant) {
                $this->getVariantRepo()->touch($variant['OXID']);
            }
        }
    }

    /**
     * @return \Makaira\Connect\Repository\RepositoryInterface
     */
    private function getProductRepo()
    {
        return oxRegistry::get('yamm_dic')['makaira.connect.repository.product'];
    }

    /**
     * @return \Makaira\Connect\Repository\RepositoryInterface
     */
    private function getVariantRepo()
    {
        return oxRegistry::get('yamm_dic')['makaira.connect.repository.variant'];
    }

}
