<?php
namespace Nitronet\eZORMBundle\ORM\Schema;



class AbstractMetaField
{
    /**
     * @var array
     */
    protected $ormSettings = array();

    /**
     * @return array
     */
    public function getOrmSettings()
    {
        return $this->ormSettings;
    }

    /**
     * @param array $ormSettings
     */
    public function setOrmSettings(array $ormSettings)
    {
        $this->ormSettings = array_merge($this->ormSettings, $ormSettings);
    }

    public function getOrmSetting($name, $default = false)
    {
        return array_key_exists($name, $this->ormSettings) ? $this->ormSettings[$name] : $default;
    }
}