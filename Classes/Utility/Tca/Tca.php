<?php
namespace BERGWERK\BwrkUtility\Utility\Tca;

/**
 * Class Tca
 * @package BERGWERK\BwrkUtility\Utility\Tca
 */
class Tca extends AbstractTca
{
    /**
     * @param Configuration $configuration
     */
    public function init(Configuration $configuration)
    {
        $this->conf = $configuration;
        $ll = $this->conf->getLl();
        $iconFile = $this->conf->getIconFile();
        $enableColumns = $this->conf->getEnableColumns();

        if (empty($ll)) {
            $this->conf->setLl('LLL:EXT:' . $this->conf->getExt() . '/Resources/Private/Language/locallang_db.xlf:' . $this->conf->getModel());
        }

        if (empty($iconFile)) {
            $this->iconFile = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($this->conf->getExt()) . 'Resources/Public/Icons/' . $this->conf->getModel() . '.gif';
        }

        if (count($enableColumns) == 0) {
            $this->conf->setEnableColumns(array(
                'disabled' => 'hidden',
                'starttime' => 'starttime',
                'endtime' => 'endtime',
            ));
        }
    }

    /**
     * @return array
     */
    public function createTca()
    {
        $this->addTab('default_typo3_conf', 'LLL:EXT:bwrk_utility/Resources/Private/Language/locallang_db.xlf:tx_bwrkutility.default_typo3_conf');
        $this->setDefaultFields();

        $tca = array(
            'ctrl' => array(
                'hideTable' => $this->conf->isHideTable(),
                'title' => $this->conf->getLl(),
                'label' => $this->conf->getLabelField(),
                'tstamp' => 'tstamp',
                'crdate' => 'crdate',
                'cruser_id' => 'cruser_id',
                'dividers2tabs' => TRUE,
                'sortby' => 'sorting',
                'versioningWS' => 2,
                'versioning_followPages' => TRUE,
                'origUid' => 't3_origuid',
                'languageField' => 'sys_language_uid',
                'transOrigPointerField' => 'l10n_parent',
                'transOrigDiffSourceField' => 'l10n_diffsource',
                'delete' => 'deleted',
                'enablecolumns' => $this->conf->getEnableColumns(),
                'iconfile' => $this->conf->getIconFile(),
                'searchFields' => $this->getSearchFields()
            ),
            'interface' => array(
                'showRecordFieldList' => $this->getRecordsFieldList(),
            ),
            'columns' => $this->getColumns(),
            'types' => array(
                '1' => array('showitem' => $this->getShowItems())
            ),
            'palettes' => array(
                '1' => array('showitem' => ''),
            ),
        );

        $labelUserFunc = $this->conf->getLabelUserFunc();

        if (!empty($labelUserFunc))
        {
            $tca['ctrl']['label_userFunc'] = $labelUserFunc;
        }

        return $tca;
    }
}