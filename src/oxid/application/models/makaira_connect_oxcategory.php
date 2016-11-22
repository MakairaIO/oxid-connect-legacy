<?php

class makaira_connect_oxcategory extends makaira_connect_oxcategory_parent
{

    /**
     * @return \Makaira\Connect\Repository\RepositoryInterface
     */
    private function getCategoryRepo()
    {
        return oxRegistry::get('yamm_dic')['makaira.connect.repository.category'];
    }

    public function save()
    {
        $result = parent::save();
        if ($result) {
            $this->getCategoryRepo()->touch($this->getId());
        }
        return $result;
    }

    public function delete($sOXID = null)
    {
        $result = parent::delete($sOXID);
        if ($result) {
            $this->getCategoryRepo()->delete($sOXID ?: $this->getId());
        }
        return $result;
    }

}
