<?php

class makaira_connect_oxcategory extends makaira_connect_oxcategory_parent
{

    /**
     * @return \Makaira\Connect\Repository
     */
    private function getRepository()
    {
        return oxRegistry::get('yamm_dic')['makaira.connect.repository'];
    }

    public function save()
    {
        $result = parent::save();
        if ($result) {
            $this->getRepository()->touch('category', $this->getId());
        }
        return $result;
    }

    public function delete($sOXID = null)
    {
        $result = parent::delete($sOXID);
        if ($result) {
            $this->getRepository()->delete('category', $sOXID ?: $this->getId());
        }
        return $result;
    }

}
