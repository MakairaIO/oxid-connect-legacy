<?php

class makaira_connect_oxarticle extends makaira_connect_oxarticle_parent
{

    public function save()
    {
        $result = parent::save();
        if ($result) {
            $this->touch();
        }
        return $result;
    }

    public function delete($sOXID = null)
    {
        $result = parent::delete($sOXID);
        if ($result) {
            $this->touch($sOXID, true);
        }
        return $result;
    }

    public function getParentId($oxid = null)
    {
        if (!isset($sOXID)) {
            return parent::getParentId();
        } else {
            /** @var \Makaira\Connect\DatabaseInterface $db */
            $db = oxRegistry::get('yamm_dic')['makaira.database'];
            $parentId = $db->query('SELECT OXPARENTID FROM oxarticles WHERE OXID = :id', ['id' => $sOXID]);
            return empty($parentId) ? null : $parentId[0]['OXPARENTID'];
        }
    }

    public function touch($oxid = null, $delete = false)
    {
        $id = $oxid ?: $this->getId();
        $method = $delete ? 'delete' : 'touch';
        if ($parentId = $this->getParentId($oxid)) {
            $this->getProductRepo()->touch($parentId); // We need to touch the parent, but mustn't delete it
            $this->getVariantRepo()->$method($id);
        } else {
            $this->getProductRepo()->$method($id);
            /** @var \Makaira\Connect\DatabaseInterface $db */
            $db = oxRegistry::get('yamm_dic')['makaira.database'];
            $variants = $db->query(
                'SELECT OXID FROM oxarticles WHERE OXPARENTID = :parentId',
                ['parentId' => $id]
            );
            foreach ($variants as $variant) {
                $this->getVariantRepo()->$method($variant['OXID']);
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
