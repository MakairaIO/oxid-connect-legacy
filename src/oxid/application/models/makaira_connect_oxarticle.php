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
        $this->touch($sOXID);
        return parent::delete($sOXID);
    }

    public function getParentId($oxid = null)
    {
        if (!isset($oxid)) {
            return parent::getParentId();
        } else {
            /** @var \Makaira\Connect\DatabaseInterface $db */
            $db = oxRegistry::get('yamm_dic')['oxid.database'];
            $parentId = $db->query('SELECT OXPARENTID FROM oxarticles WHERE OXID = :id', ['id' => $oxid]);
            return empty($parentId) ? null : $parentId[0]['OXPARENTID'];
        }
    }

    public function touch($oxid = null)
    {
        $id = $oxid ?: $this->getId();
        if ($parentId = $this->getParentId($oxid)) {
            $this->getRepository()->touch('product', $parentId);
            $this->getRepository()->touch('variant', $id);
        } else {
            $this->getRepository()->touch('product', $id);
            /** @var \Makaira\Connect\DatabaseInterface $db */
            $db = oxRegistry::get('yamm_dic')['oxid.database'];
            $variants = $db->query(
                'SELECT OXID FROM oxarticles WHERE OXPARENTID = :parentId',
                ['parentId' => $id]
            );
            foreach ($variants as $variant) {
                $this->getRepository()->touch('variant', $variant['OXID']);
            }
        }
    }

    public function updateSoldAmount($dAmount = 0)
    {
        $result = parent::updateSoldAmount($dAmount);
        if (!$dAmount) {
            return $result;
        }
        $this->touch();

        return $result;
    }

    /**
     * @return \Makaira\Connect\Repository
     */
    private function getRepository()
    {
        return oxRegistry::get('yamm_dic')['makaira.connect.repository'];
    }

    public function executeDependencyEvent($iDependencyEvent = null)
    {
        $this->touch($this->getId());
        return parent::executeDependencyEvent($iDependencyEvent);
    }
}
