<?php

class makaira_connect_oxmanufacturer extends makaira_connect_oxmanufacturer_parent
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
            $this->touch($this->getId());
        }
        return $result;
    }

    public function delete($sOXID = null)
    {
        $result = parent::delete($sOXID);
        if ($result) {
            $this->touch($sOXID ?: $this->getId());
        }
        return $result;
    }

    public function touch($oxid = null)
    {
        $id = $oxid ?: $this->getId();
        $this->getRepository()->touch('manufacturer', $id);
    }

}
