<?php

namespace BERGWERK\BwrkUtility\Utility\Tca;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FlexForm
 * @package BERGWERK\BwrkUtility\Utility\Tca
 */
abstract class FlexForm extends AbstractTca
{

    /**
     * @var \BERGWERK\BwrkUtility\Utility\Tca\Configuration
     */
    protected $conf;

    /**
     * @var array
     */
    protected $sheets = array();

    /**
     * @param Configuration $configuration
     */
    public function init(Configuration $configuration)
    {
        $this->conf = $configuration;
        $ll = $this->conf->getLl();

        if (empty($ll)) {
            $this->conf->setLl('LLL:EXT:' . $this->conf->getExt() . '/Resources/Private/Language/locallang_db.xlf:' . 'flexform' . '.' . $this->conf->getPlugin());
        }
    }

    /**
     * @param $sheetName
     * @param $fields
     */
    public function addSheet($sheetName, $fields)
    {
        $this->sheets[$sheetName] = $fields;
    }

    /**
     *
     */
    public function renderFlexForm()
    {
        $data = array(
            array(
                'name' => 'sheets',
                'value' => $this->formSheetsForXml($this->sheets)
            )
        );

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><T3DataStructure/>');

        $this->generateXmlElement($xml, $data);

        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;

        return $dom->saveXML();
    }

    /**
     * @param $sheets
     * @return array
     */
    private function formSheetsForXml($sheets)
    {
        $sheetsArray = array();

        foreach ($sheets as $sheet => $fields) {
            $sheetsArray[] = array(
                'name' => $sheet,
                'value' => array(
                    array(
                        'name' => 'ROOT',
                        'value' => array(
                            array(
                                'name' => 'TCEforms',
                                'value' => array(
                                    array(
                                        'name' => 'sheetTitle',
                                        'value' => $this->conf->getLl() . '.' . $sheet
                                    )
                                )
                            ),
                            array(
                                'name' => 'type',
                                'value' => 'array'
                            ),
                            array(
                                'name' => 'el',
                                'value' => $this->formFieldsForXml($fields, $sheet)
                            )
                        )
                    )
                )
            );
        }

        return $sheetsArray;
    }

    /**
     * @param $fields
     * @param $sheetTitle
     * @return array
     */
    private function formFieldsForXml($fields, $sheetTitle)
    {
        $fieldsArrayCounter = 0;
        $fieldsArray = array();
        foreach ($fields as $field) {
            foreach ($field as $key => $value) {

                $formValuesForXml = $this->formValuesForXml($value);

                $fieldsArray[$fieldsArrayCounter] = array(
                    'name' => 'settings.' . $key,
                    'value' => array(
                        array(
                            'name' => 'TCEforms',
                            'value' => $formValuesForXml
                        )
                    )
                );

                foreach ($fieldsArray[$fieldsArrayCounter]['value'] as $key1 => $value1) {
                    if (is_array($value1)) {
                        if ($value1['name'] == 'label') {
                            $fieldsArray[$fieldsArrayCounter]['value'][$key1]['value'] = $this->conf->getLl() . '.' . $sheetTitle . '.' . $key;
                        }
                    }
                }
                $fieldsArrayCounter++;
            }
        }

        return $fieldsArray;
    }

    /**
     * @param $values
     * @return array
     */
    private function formValuesForXml($values)
    {
        $valuesArray = array();

        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $valuesArray[] = array(
                    'name' => $key,
                    'value' => $this->formValuesForXml($value)
                );
            } else {
                $valuesArray[] = array(
                    'name' => $key,
                    'value' => $value
                );
            }
        }

        return $valuesArray;
    }

    /**
     * @param \SimpleXMLElement $xmlElement
     * @param mixed $data
     */
    private function generateXmlElement($xmlElement, $data)
    {
        foreach ($data as $element)
        {
            $elementName = $element['name'];
            $elementValue = $element['value'];

            if (empty($elementName) || empty($elementValue))
            {
                continue;
            }

            if (!is_array($elementValue))
            {
                $xmlElement->addChild($elementName, $elementValue);
            }
            else
            {
                $xmlChild = $xmlElement->addChild($elementName);

                $this->generateXmlElement($xmlChild, $elementValue);
            }
        }
    }

    /**
     * @return string
     */
    abstract public function render();

    public static function renderStatic()
    {
        $calledClass = get_called_class();

        /** @var self $instance */
        $instance = new $calledClass();

        return $instance->render();
    }
}