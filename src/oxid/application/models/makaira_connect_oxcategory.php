<?php

class makaira_connect_oxcategory extends makaira_connect_oxcategory_parent
{
    /**
     * @var bool
     */
    protected static $disableMakairaTouch = false;

    /**
     * @param bool $disableTouch
     */
    public function disableMakairaTouch($disableTouch = true)
    {
        self::$disableMakairaTouch = $disableTouch;
    }

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
        if (self::$disableMakairaTouch) {
            return;
        }
        $id = $oxid ?: $this->getId();
        $this->getRepository()->touch('category', $id);
    }

}
