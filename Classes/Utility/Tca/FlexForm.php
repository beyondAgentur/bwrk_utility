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
            'name' => 'T3DataStructure', // "name" required, all else optional
            array_merge(array(
                'name' => 'sheets'
            ),
                $this->formSheetsForXml($this->sheets)
            ),
        );

        $doc = new \DOMDocument();
        $child = $this->generateXmlElement($doc, $data);
        if ($child)
            $doc->appendChild($child);
        $doc->formatOutput = true; // Add whitespace to make easier to read XML
        $xml = $doc->saveXML();

        return $xml;
    }

    /**
     * @param $sheets
     * @return array
     */
    private function formSheetsForXml($sheets)
    {
        $sheetsArray = array();
        foreach ($sheets as $key => $value) {
            $sheetsArray[] = array(
                'name' => $key,
                array(
                    'name' => 'ROOT',
                    array(
                        'name' => 'TCEforms',
                        array(
                            'name' => 'sheetTitle',
                            'value' => $this->conf->getLl() . '.' . $key
                        ),
                    ),
                    array(
                        'name' => 'type',
                        'value' => 'array'
                    ),
                    array_merge(array(
                        'name' => 'el'
                    ),
                        $this->formFieldsForXml($value, $key)
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
                $fieldsArray[$fieldsArrayCounter] = array(
                    'name' => 'settings.' . $key,
                    array_merge(array(
                        'name' => 'TCEforms',
                    ),
                        $this->formValuesForXml($value)
                    )
                );

                foreach ($fieldsArray[$fieldsArrayCounter][0] as $key1 => $value1) {
                    if (is_array($value1)) {
                        if ($value1['name'] == 'label') {
                            $fieldsArray[$fieldsArrayCounter][0][$key1]['value'] = $this->conf->getLl() . '.' . $sheetTitle . '.' . $key;
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
                $valuesTmpArray = array();
                foreach ($value as $key1 => $value1) {
                    $valuesTmpArray[] = array(
                        'name' => $key1,
                        'value' => $value1
                    );
                }
                $valuesArray[] = array_merge(array(
                    'name' => $key,
                ),
                    $valuesTmpArray
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
     * @param \DOMDocument $dom
     * @param $data
     * @return bool|\DOMElement
     */
    private function generateXmlElement(\DOMDocument $dom, $data)
    {
        if (empty($data['name']))
            return false;

        $element_value = (!empty($data['value'])) ? $data['value'] : null;
        $element = $dom->createElement($data['name'], $element_value);

        if (!empty($data['attributes']) && is_array($data['attributes'])) {
            foreach ($data['attributes'] as $attribute_key => $attribute_value) {
                $element->setAttribute($attribute_key, $attribute_value);
            }
        }
        foreach ($data as $data_key => $child_data) {
            if (!is_numeric($data_key))
                continue;

            $child = $this->generateXmlElement($dom, $child_data);
            if ($child)
                $element->appendChild($child);
        }

        return $element;
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