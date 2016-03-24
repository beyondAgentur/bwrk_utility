<?php

namespace BERGWERK\BwrkUtility\Utility\Tca;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AbstractClass
 * @package BERGWERK\BwrkUtility\Utility\Tca
 */
class AbstractTca
{

	/**
	 *
	 */
	const ExtensionsImage = 'jpg,jpeg,png,gif,tiff,bmp';

	/**
	 * @var array
	 */
	protected $fields = array();
	/**
	 * @var \BERGWERK\BwrkUtility\Utility\Tca\Configuration
	 */
	protected $conf;

	/**
	 * @return string
	 */
	public function getSearchFields()
	{
		return implode(',', $this->conf->getSearchFields());
	}

	/**
	 *
	 */
	public function setDefaultFields()
	{
		if (!array_key_exists('sys_language_uid', $this->fields)) {
			$this->addRawField('sys_language_uid', array(
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
				'config' => array(
					'type' => 'select',
					'foreign_table' => 'sys_language',
					'foreign_table_where' => 'ORDER BY sys_language.title',
					'items' => array(
						array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
						array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
					),
				),
			));
		}

		if (!array_key_exists('l10n_parent', $this->fields)) {
			$this->addRawField('l10n_parent', array(
				'displayCond' => 'FIELD:sys_language_uid:>:0',
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
				'config' => array(
					'type' => 'select',
					'items' => array(
						array('', 0),
					),
					'foreign_table' => $this->conf->getModel(),
					'foreign_table_where' =>
						'AND ' . $this->conf->getModel() . '.pid = ###CURRENT_PID### AND ' . $this->conf->getModel() . '.sys_language_uid IN (-1,0)',
				),
			));
		}

		if (!array_key_exists('l10n_diffsource', $this->fields)) {
			$this->addRawField('l10n_diffsource', array(
				'config' => array(
					'type' => 'passthrough',
				),
			));
		}

		if (!array_key_exists('t3ver_label', $this->fields)) {
			$this->addInputField('t3ver_label', 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel');
		}

		if (!array_key_exists('hidden', $this->fields)) {
			$this->addCheckField('hidden', 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden');
		}
	}

	/**
	 * @param $fieldName
	 * @return string
	 */
	public function getFieldLabel($fieldName)
	{
		return $this->conf->getLl() . '.' . $fieldName;
	}

	/**
	 * @return string
	 */
	public function getShowItems()
	{
		$fieldArr = array();
		foreach ($this->fields as $key => $value) {
			if (substr($key, 0, 4) === 'tab_') {
				$fieldArr[$value] = '';
			} else {
				$fieldArr[$key] = $value;
			}
		}
		return implode(',', array_keys($fieldArr));
	}

	/**
	 * @return string
	 */
	public function getRecordsFieldList()
	{
		return implode(',', array_keys($this->removeTabs($this->fields)));
	}

	/**
	 * @param $fields
	 * @return array
	 */
	public function removeTabs($fields)
	{
		$fieldArr = array();

		foreach ($fields as $key => $value) {
			if (substr($key, 0, 4) !== 'tab_') {
				$fieldArr[$key] = $value;
			}
		}

		return $fieldArr;
	}

	/**
	 * @return array
	 */
	public function getColumns()
	{
		return $this->removeTabs($this->fields);
	}

	/*
	 * Public
	 */

	/**
	 * @param $string
	 */
	public function addConfigSearchField($string)
	{
		array_push($this->conf->getSearchFields(), $string);
	}


	/**
	 * @param $fieldName
	 * @param string $label
	 * @param int $exclude
	 * @param string $default
	 * @return array
	 */
	public function addCheckField($fieldName, $label = '', $exclude = 0, $default = '')
	{
		if (empty($label)) {
			$label = $this->getFieldLabel($fieldName);
		}

		$this->fields[$fieldName] = array(
			'exclude' => $exclude,
			'label' => $label,
			'config' => array(
				'type' => 'check',
			),
		);
		if (!empty($default)) {
			$this->fields[$fieldName]['config']['default'] = $default;
		}

		return array($fieldName => $this->fields[$fieldName]);
	}


	/**
	 * @param $fieldName
	 * @param string $label
	 * @param int $exclude
	 * @param int $size
	 * @param int $max
	 * @param int $readOnly
	 * @param string $eval
	 * @return array
	 */
	public function addInputField($fieldName, $label = '', $exclude = 0, $size = 30, $max = 255, $readOnly = 0, $eval = 'trim', $displayCond = null)
	{
		if (empty($label)) {
			$label = $this->getFieldLabel($fieldName);
		}

		$this->fields[$fieldName] = array(
			'exclude' => $exclude,
			'label' => $label,
			'config' => array(
				'type' => 'input',
				'size' => $size,
				'max' => $max,
				'eval' => $eval,
				'readOnly' => $readOnly
			)
		);

		if (!empty($displayCond)) {
			$this->fields[$fieldName]['displayCond'] = $displayCond;
		}

		return array($fieldName => $this->fields[$fieldName]);
	}

	public function addTypoLink($fieldName, $label = '', $exclude = 0, $size = 30, $max = 255, $readOnly = 0, $eval = 'trim', $displayCond = null, $mode = 'wizard')
	{
		if (empty($label)) {
			$label = $this->getFieldLabel($fieldName);
		}

		$this->fields[$fieldName] = array(
			'exclude' => $exclude,
			'label' => $label,
			'config' => array(
				'type' => 'input',
				'size' => $size,
				'max' => $max,
				'eval' => $eval,
				'readOnly' => $readOnly,
				'wizards' => array(
					'_PADDING' => '2',
					'link' => array(
						'type' => 'popup',
						'title' => 'Link',
						'icon' => 'link_popup.gif',
						'module' => array(
							'name' => 'wizard_element_browser',
							'urlParameters' => array(
								'mode' => $mode
							)
						),
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
					)
				)
			)
		);

		if (!empty($displayCond)) {
			$this->fields[$fieldName]['displayCond'] = $displayCond;
		}

		return array($fieldName => $this->fields[$fieldName]);
	}

	public function addTypoLinkFile($fieldName, $label = '', $exclude = 0, $size = 30, $max = 255, $readOnly = 0, $eval = 'trim', $displayCond = null)
	{
		return $this->addTypoLink($fieldName, $label, $exclude, $size, $max, $readOnly, $eval, $displayCond, 'file');
	}

	/**
	 * @param $fieldName
	 * @param bool|false $rte
	 * @param string $label
	 * @param int $exclude
	 * @param int $cols
	 * @param int $rows
	 * @return array
	 */
	public function addTextField($fieldName, $rte = false, $label = '', $exclude = 0, $cols = 40, $rows = 6, $displayCond = null)
	{
		if (empty($label)) {
			$label = $this->getFieldLabel($fieldName);
		}

		$this->fields[$fieldName] = array(
			'exclude' => $exclude,
			'label' => $label,
			'config' => array(
				'type' => 'text',
				'cols' => $cols,
				'rows' => $rows
			)
		);

		if (!empty($displayCond)) {
			$this->fields[$fieldName]['displayCond'] = $displayCond;
		}

		if ($rte) {
			$this->fields[$fieldName]['defaultExtras'] = 'richtext[]:rte_transform[mode=ts_css]';
		}

		return array($fieldName => $this->fields[$fieldName]);
	}


	/**
	 * @param $fieldName
	 * @param $config
	 * @return array
	 */
	public function addRawField($fieldName, $config)
	{
		$this->fields[$fieldName] = $config;
		return array($fieldName => $this->fields[$fieldName]);
	}

	/**
	 * @param $tabName
	 * @param string $label
	 */
	public function addTab($tabName, $label = '')
	{
		if (empty($label)) {
			$label = $this->conf->getLl() . '.' . $tabName;
		}
		$this->fields['tab_' . $tabName] = '--div--;' . $label;
	}

	public function addSelectFieldFunc($fieldName, $itemsProcFunc, $label = '', $exclude = 0)
	{
		if (empty($label)) {
			$label = $this->getFieldLabel($fieldName);
		}

		$this->fields[$fieldName] = array(
			'exclude' => $exclude,
			'label' => $label,
			'config' => array(
				'type' => 'select',
				'itemsProcFunc' => $itemsProcFunc
			)
		);

		return array($fieldName => $this->fields[$fieldName]);
	}

	/**
	 * @param string $fieldName
	 * @return array
	 */
	public function addPassThrough($fieldName)
	{
		$this->fields[$fieldName] = array(
			'config' => array(
				'type' => 'passthrough'
			)
		);
		return array($fieldName => $this->fields[$fieldName]);
	}

	/**
	 * @param $fieldName
	 * @param array $items
	 * @param string $label
	 * @param string $itemsLabelPath
	 * @param int $exclude
	 * @return array
	 */
	public function addSelectField($fieldName, $items = array(), $label = '', $itemsLabelPath = '', $exclude = 0)
	{
		if (empty($itemsLabelPath)) {
			$itemsLabelPath = $this->conf->getLl();
		}
		if (empty($label)) {
			$label = $this->getFieldLabel($fieldName);
		}

		$itemsArr = array();

		foreach ($items as $item) {
			$itemsArr[] = array(
				$itemsLabelPath . '.' . $item['name'],
				$item['value']
			);
		}

		$this->fields[$fieldName] = array(
			'exclude' => $exclude,
			'label' => $label,
			'config' => array(
				'type' => 'select',
				'items' => $itemsArr
			)
		);
		return array($fieldName => $this->fields[$fieldName]);
	}

	/**
	 * @param $fieldName
	 * @param $foreignTable
	 * @param $foreignField
	 * @param $foreignSortby
	 * @param $foreignTableField
	 * @param array $foreignMatchFields
	 * @param $foreignLabel
	 * @param $foreignSelector
	 * @param int $exclude
	 * @param int $maxitems
	 * @param int $minitems
	 * @param string $label
	 * @param array $overwriteConfig
	 * @return array
	 */
	public function addReferenceField(
		$fieldName,
		$foreignTable,
		$foreignField,
		$foreignSortby,
		$foreignTableField = null,
		$foreignMatchFields = array(),
		$foreignLabel = null,
		$foreignSelector = null,
		$exclude = 0,
		$maxitems = 999,
		$minitems = 0,
		$label = '',
		$overwriteConfig = array(),
		$displayCond = null)
	{
		if (empty($label)) {
			$label = $this->getFieldLabel($fieldName);
		}

		$config = array(
			'type' => 'inline',
			'maxitems' => $maxitems,
			'minitems' => $minitems,
			'foreign_table' => $foreignTable,
			'foreign_field' => $foreignField,
			'foreign_sortby' => $foreignSortby,
			'foreign_table_field' => $foreignTableField,
			'foreign_match_fields' => $foreignMatchFields,
			'foreign_label' => $foreignLabel,
			'foreign_selector' => $foreignSelector,

		);

		$this->fields[$fieldName] = array(
			'exclude' => $exclude,
			'label' => $label,
			'config' => GeneralUtility::array_merge($config, $overwriteConfig)
		);

		if (!empty($displayCond)) {
			$this->fields[$fieldName]['displayCond'] = $displayCond;
		}

		return array($fieldName => $this->fields[$fieldName]);
	}

	/**
	 * @param $fieldName
	 * @param $foreignTable
	 * @param int $size
	 * @param int $maxItems
	 * @param int $exclude
	 * @param string $label
	 * @return array
	 */
	public function addSingleRelationField($fieldName, $foreignTable, $size = 1, $maxItems = 1, $exclude = 0, $label = '')
	{
		if (empty($label)) {
			$label = $this->getFieldLabel($fieldName);
		}

		$this->fields[$fieldName] = array(
			'exclude' => $exclude,
			'label' => $label,
			'config' => array(
				'type' => 'select',
				'foreign_table' => $foreignTable,
				'size' => $size,
				'maxitems' => $maxItems
			)
		);
		return array($fieldName => $this->fields[$fieldName]);
	}

	/**
	 * @param $fieldName
	 * @param $foreignTable
	 * @param $mmRelationTable
	 * @param $mmOppositeField
	 * @param string $foreignTableWhere
	 * @param array $mmMatchFields
	 * @param int $exclude
	 * @param int $size
	 * @param int $minitems
	 * @param int $maxItems
	 * @param string $label
	 * @param array $overwriteConfig
	 * @return array
	 */
	public function addMultipleRelationField($fieldName, $foreignTable, $mmRelationTable, $mmOppositeField, $foreignTableWhere = '', $mmMatchFields = array(), $exclude = 0, $size = 10, $minitems = 0, $maxItems = 999, $label = '', $overwriteConfig = array())
	{
		if (empty($label)) {
			$label = $this->getFieldLabel($fieldName);
		}

		$config = array(
			'type' => 'select',
			'allowed' => $foreignTable,
			'foreign_table' => $foreignTable,
			'MM' => $mmRelationTable,
			'MM_opposite_field' => $mmOppositeField,
			'size' => $size,
			'maxitems' => $maxItems,
			'minitems' => $minitems
		);

		$mergedConfig = array_merge($config, $overwriteConfig);

		$this->fields[$fieldName] = array(
			'exclude' => $exclude,
			'label' => $label,
			'config' => $mergedConfig
		);

		if (!empty($foreignTableWhere)) {
			$this->fields[$fieldName]['config']['foreign_table_where'] = $foreignTableWhere;
		}
		if (count($mmMatchFields) > 0) {
			$this->fields[$fieldName]['config']['MM_match_fields'] = $mmMatchFields;
		}
		return array($fieldName => $this->fields[$fieldName]);
	}

	/**
	 * @param $fieldName
	 * @param int $exclude
	 * @param int $size
	 * @param int $minitems
	 * @param int $maxitems
	 * @param string $label
	 * @param string $extbaseType
	 * @return array
	 */
	public function addSysCategoryReferences($fieldName, $exclude = 0, $size = 10, $minitems = 0, $maxitems = 999, $label = '', $extbaseType = '')
	{
		$foreignTableWhere = " AND sys_category.sys_language_uid IN (-1, 0) ORDER BY sys_category.sorting ASC";

		if (!empty($extbaseType)) {
			$foreignTableWhere = " AND sys_category.tx_extbase_type = '" . $extbaseType . "' " . $foreignTableWhere;
		}

		$this->addMultipleRelationField(
			$fieldName,
			'sys_category',
			'sys_category_record_mm',
			'items',
			$foreignTableWhere,
			array(
				'tablenames' => $this->conf->getModel(),
				'fieldname' => $fieldName
			),
			$exclude,
			$size,
			$minitems,
			$maxitems,
			$label,
			array(
				'renderMode' => 'tree',
				'treeConfig' => array(
					'appearance' => array('expandAll' => 1, 'showHeader' => 1),
					'parentField' => 'parent',
				)
			)
		);
		return array($fieldName => $this->fields[$fieldName]);
	}

	/**
	 * @param $fieldName
	 * @param int $exclude
	 * @param int $size
	 * @param int $minitems
	 * @param int $maxitems
	 * @param string $label
	 * @return array
	 */
	public function addSysCategoryReferencesFlexForm(
		$fieldName,
		$exclude = 0,
		$size = 10,
		$minitems = 0,
		$maxitems = 999,
		$label = '')
	{
		$foreignTableWhere = " AND sys_category.sys_language_uid IN (-1, 0) ORDER BY sys_category.sorting ASC";

		$this->addMultipleRelationField(
			$fieldName,
			'sys_category',
			null,
			null,
			$foreignTableWhere,
			array(),
			$exclude,
			$size,
			$minitems,
			$maxitems,
			$label,
			array(
				'renderMode' => 'tree',
				'treeConfig' => array(
					'appearance' => array('expandAll' => 1, 'showHeader' => 1),
					'parentField' => 'parent'
				)
			)
		);
		return array($fieldName => $this->fields[$fieldName]);
	}

	public function addFalImageReference($fieldName, $exclude = 0, $minitems = 0, $maxitems = 999, $label = '', $displayCond = null)
	{
		$baseConfig = ExtensionManagementUtility::getFileFieldTCAConfig($fieldName);

		if (empty($label)) {
			$label = $this->getFieldLabel($fieldName);
		}

		$config = array_merge($baseConfig, array(
			'minitems' => $minitems,
			'maxitems' => $maxitems
		));

		$this->fields[$fieldName] = array(
			'exclude' => $exclude,
			'label' => $label,
			'config' => $config
		);

		if (!empty($displayCond)) {
			$this->fields[$fieldName]['displayCond'] = $displayCond;
		}

		return array($fieldName => $this->fields[$fieldName]);
	}

	/**
	 * @param $fieldName
	 * @param int $exclude
	 * @param int $minitems
	 * @param int $maxitems
	 * @param string $label
	 * @param string $allowedExtensions
	 * @return array
	 */
	public function addSysFileReference($fieldName, $exclude = null, $minitems = null, $maxitems = null, $label = null, $allowedExtensions = null, $displayCond = null)
	{
		if (empty($exclude)) {
			$exclude = 0;
		}

		if (empty($allowedExtensions)) {
			$allowedExtensions = '*';
		}

		$this->addReferenceField(
			$fieldName,
			'sys_file_reference',
			'uid_foreign',
			'sorting_foreign',
			'tablenames',
			array(
				'fieldname' => $fieldName
			),
			'uid_local',
			'uid_local',
			$exclude,
			$maxitems,
			$minitems,
			$label,
			array(
				'appearance' => array(
					'newRecordLinkAddTitle' => 1,
					'headerThumbnail' => array(
						'field' => 'uid_local',
						'height' => 64,
						'width' => 64
					)
				),
				'foreign_selector_fieldTcaOverride' => array(
					'config' => array(
						'appearance' => array(
							'elementBrowserType' => 'file',
							'elementBrowserAllowed' => $allowedExtensions
						)
					)
				)
			),
			$displayCond
		);

		return array($fieldName => $this->fields[$fieldName]);
	}

	public function addPageReference($fieldName, $allowed = 'pages', $size = 1, $maxitems = 999, $minitems = 0, $exclude = 0, $label = '')
	{
		if (empty($label)) {
			$label = $this->getFieldLabel($fieldName);
		}

		$config = array(
			'type' => 'group',
			'internal_type' => 'db',
			'allowed' => $allowed,
			'size' => $size,
			'maxitems' => $maxitems,
			'minitems' => $minitems,
			'show_thumbs' => 1
		);

		$this->fields[$fieldName] = array(
			'exclude' => $exclude,
			'label' => $label,
			'config' => $config
		);
		return array($fieldName => $this->fields[$fieldName]);
	}
}