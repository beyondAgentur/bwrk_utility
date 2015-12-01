<?php

namespace BERGWERK\BwrkUtility\Utility\Tca;

class Configuration
{
    /**
     * @var string
     */
    protected $ext = '';
    /**
     * @var string
     */
    protected $ll = '';
    /**
     * @var string
     */
    protected $model = '';
    /**
     * @var string
     */
    protected $plugin = '';
    /**
     * @var string
     */
    protected $labelField = '';
    /**
     * @var string
     */
    protected $iconFile = '';

    /**
     * @var array
     */
    protected $searchFields = array();

    /**
     * @var array
     */
    protected $enableColumns = array();

    /**
     * @param string $ext
     */
    public function setExt($ext)
    {
        $this->ext = $ext;
    }

    /**
     * @param string $ll
     */
    public function setLl($ll)
    {
        $this->ll = $ll;
    }

    /**
     * @param string $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @param string $labelField
     */
    public function setLabelField($labelField)
    {
        $this->labelField = $labelField;
    }

    /**
     * @param string $iconFile
     */
    public function setIconFile($iconFile)
    {
        $this->iconFile = $iconFile;
    }

    /**
     * @param array $enableColumns
     */
    public function setEnableColumns($enableColumns)
    {
        $this->enableColumns = $enableColumns;
    }

    /**
     * @return string
     */
    public function getExt()
    {
        return $this->ext;
    }

    /**
     * @return string
     */
    public function getLl()
    {
        return $this->ll;
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return string
     */
    public function getLabelField()
    {
        return $this->labelField;
    }

    /**
     * @return string
     */
    public function getIconFile()
    {
        return $this->iconFile;
    }

    /**
     * @return array
     */
    public function getSearchFields()
    {
        return $this->searchFields;
    }

    /**
     * @return array
     */
    public function getEnableColumns()
    {
        return $this->enableColumns;
    }

    /**
     * @param array $searchFields
     */
    public function setSearchFields($searchFields)
    {
        $this->searchFields = $searchFields;
    }

    /**
     * @return string
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * @param string $plugin
     */
    public function setPlugin($plugin)
    {
        $this->plugin = $plugin;
    }
}