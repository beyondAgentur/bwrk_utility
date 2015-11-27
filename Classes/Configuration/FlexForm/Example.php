<?php

namespace BERGWERK\BwrkUtility\Configuration\F;

use BERGWERK\BwrkUtility\Utility\Tca\Configuration;
use BERGWERK\BwrkUtility\Utility\Tca\FlexForm;

class Example extends FlexForm
{
    public function __construct()
    {
        $configuration = new Configuration();
        $configuration->setExt('bwrk_utility');
        $configuration->setPlugin('example');

        $this->init($configuration);
    }

    public function render()
    {
        $this->addInputField('header');
        $this->addInputField('subheader');
        $this->addTextField('text');

        $this->addTab('Configuration');

        $this->addCheckField('configuration_1');
        $this->addCheckField('configuration_2');
        $this->addCheckField('configuration_3');

        $this->renderFlexForm();
    }
}