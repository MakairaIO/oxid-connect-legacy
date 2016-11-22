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
            $db = oxRegistry::get('yamm_dic')['oxid.database'];
            $parentId = $db->query('SELECT OXPARENTID FROM oxarticles WHERE OXID = :id', ['id' => $sOXID]);
            return empty($parentId) ? null : $parentId[0]['OXPARENTID'];
        }
    }

    public function touch($oxid = null, $delete = false)
    {
        $id = $oxid ?: $this->getId();
        $method = $delete ? 'delete' : 'touch';
        if ($parentId = $this->getParentId($oxid)) {
            $this->getRepository()->touch('product', $parentId); // We need to touch the parent, but mustn't delete it
            $this->getRepository()->$method('variant', $id);
        } else {
            $this->getRepository()->$method('product', $id);
            /** @var \Makaira\Connect\DatabaseInterface $db */
            $db = oxRegistry::get('yamm_dic')['oxid.database'];
            $variants = $db->query(
                'SELECT OXID FROM oxarticles WHERE OXPARENTID = :parentId',
                ['parentId' => $id]
            );
            foreach ($variants as $variant) {
                $this->getRepository()->$method('variant', $variant['OXID']);
            }
        }
    }

    /**
     * @return \Makaira\Connect\Repository
     */
    private function getRepository()
    {
        return oxRegistry::get('yamm_dic')['makaira.connect.repository'];
    }

}
