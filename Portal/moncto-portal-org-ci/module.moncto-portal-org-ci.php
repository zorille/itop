<?php
//
// iTop module definition file
//

SetupWebPage::AddModule(
    __FILE__,
    'moncto-portal-org-ci/1.0.0',
    array(
        'label' => 'Enhanced Portal - CI par organisation sur les tickets',
        'category' => 'business',
        'dependencies' => array(
            'itop-portal/3.2.0',
            'itop-portal-base/3.2.0',
            'itop-tickets/3.2.0',
            'itop-config-mgmt/3.2.0',
        ),
        'mandatory' => false,
        'visible' => true,
        'datamodel' => array(
            'model.moncto-portal-org-ci.php',
            'datamodel.moncto-portal-org-ci.xml',
        ),
        'webservice' => array(),
        'data.struct' => array(),
        'data.sample' => array(),
        'doc.manual_setup' => '',
        'doc.more_information' => '',
        'settings' => array(),
    )
);
