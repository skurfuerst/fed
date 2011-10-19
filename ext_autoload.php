<?php

$extensionClassesPath = t3lib_extMgm::extPath('fed', 'Classes/');
$extensionPath = t3lib_extMgm::extPath('fed');
$extbaseClassesPath = t3lib_extMgm::extPath('extbase', 'Classes/');
return array(
	'tx_extbase_service_cacheservice' => $extbaseClassesPath . "Service/CacheService.php",
	'tx_extbase_service_typoscriptservice' => $extbaseClassesPath . "Service/TypoScriptService.php",

	'tx_fed_tests_unit_basetestcase' => $extensionPath . 'Tests/Unit/BaseTestCase.php',
	'tx_fed_core' => $extensionClassesPath . 'Core.php',
	'tx_fed_debug' => $extensionClassesPath . 'Debug.php',
	'tx_fed_error_debugger' => $extensionClassesPath . 'Error/Debugger.php',
	'tx_fed_core_bootstrap' => $extensionClassesPath . 'Core/Bootstrap.php',
	'tx_fed_configuration_configurationmanager' => $extensionClassesPath . 'Configuration/ConfigurationManager.php',
	'tx_fed_core_abstractcontroller' => $extensionClassesPath . 'Core/AbstractController.php',
	'tx_fed_core_viewhelper_abstractfceviewhelper' => $extensionClassesPath . 'Core/ViewHelper/AbstractFceViewHelper.php',
	'tx_fed_core_viewhelper_abstractviewhelper' => $extensionClassesPath . 'Core/ViewHelper/AbstractViewHelper.php',
	'tx_fed_controller_pagecontroller' => $extensionClassesPath . 'Controller/PageController.php',
	'tx_fed_service_content' => $extensionClassesPath . 'Service/Content.php',
	'tx_fed_service_page' => $extensionClassesPath . 'Service/Page.php',
	'tx_fed_utility_pdf' => $extensionClassesPath . 'Utility/PDF.php',
	'tx_fed_utility_extjs' => $extensionClassesPath . 'Utility/ExtJS.php',
	'tx_fed_utility_recursionhandler' => $extensionClassesPath . 'Utility/RecursionHandler.php',
	'tx_fed_utility_domainobjectinfo' => $extensionClassesPath . 'Utility/DomainObjectInfo.php',
	'tx_fed_utility_flexform' => $extensionClassesPath . 'Utility/FlexForm.php',
	'tx_fed_utility_json' => $extensionClassesPath . 'Utility/JSON.php',
	'tx_fed_utility_documenthead' => $extensionClassesPath . 'Utility/DocumentHead.php',
	'tx_fed_utility_debug' => $extensionClassesPath . 'Utility/Debug.php',
	'tx_fed_utility_datacomparison' => $extensionClassesPath . 'Utility/DataComparison.php',
	'tx_fed_utility_propertymapper' => $extensionClassesPath . 'Utility/PropertyMapper.php',
	'tx_fed_extjs_modelgenerator' => $extensionClassesPath . 'ExtJS/ModelGenerator.php',
	'tx_fed_mvc_view_exposedtemplateview' => $extensionClassesPath . 'MVC/View/ExposedTemplateView.php',
	'tx_fed_persistence_repository' => $extensionClassesPath . 'Persistence/Repository.php',
	'tx_fed_domain_model_contentelement' => $extensionClassesPath . 'Domain/Model/ContentElement.php',
	'tx_fed_domain_model_backendlayout' => $extensionClassesPath . 'Domain/Model/BackendLayout.php',
	'tx_fed_domain_model_datasource' => $extensionClassesPath . 'Domain/Model/DataSource.php',
	'tx_fed_domain_model_page' => $extensionClassesPath . 'Domain/Model/Page.php',
	'tx_fed_domain_model_address' => $extensionClassesPath . 'Domain/Model/Address.php',
	'tx_fed_domain_repository_contentelementrepository' => $extensionClassesPath . 'Domain/Repository/ContentElementRepository.php',
	'tx_fed_domain_repository_backendlayoutrepository' => $extensionClassesPath . 'Domain/Repository/BackendLayoutRepository.php',
	'tx_fed_domain_repository_datasourcerepository' => $extensionClassesPath . 'Domain/Repository/DataSourceRepository.php',
	'tx_fed_domain_repository_pagerepository' => $extensionClassesPath . 'Domain/Repository/PageRepository.php',
	'tx_fed_domain_repository_addressrepository' => $extensionClassesPath . 'Domain/Repository/AddressRepository.php',
	'tx_fed_backend_tcemain' => $extensionClassesPath . 'Backend/TCEMain.php',
	'tx_fed_backend_pagelayoutselector' => $extensionClassesPath . 'Backend/PageLayoutSelector.php',
	'tx_fed_backend_fceselector' => $extensionClassesPath . 'Backend/FCESelector.php',
	'tx_fed_backend_preview' => $extensionClassesPath . 'Backend/Preview.php',
	'tx_fed_backend_templavoilapreview' => $extensionClassesPath . 'Backend/TemplaVoilaPreview.php',
	'tx_fed_backend_fceparser' => $extensionClassesPath . 'Backend/FCEParser.php',
	'tx_fed_backend_hiddenfield' => $extensionClassesPath . 'Backend/HiddenField.php',
	'tx_fed_viewhelpers_pageviewhelper' => $extensionClassesPath . 'ViewHelpers/PageViewHelper.php',
	'tx_fed_viewhelpers_fceviewhelper' => $extensionClassesPath . 'ViewHelpers/FceViewHelper.php',
	'tx_fed_viewhelpers_page_fieldviewhelper' => $extensionClassesPath . 'ViewHelpers/Page/FieldViewHelper.php',
	'tx_fed_viewhelpers_page_field_groupviewhelper' => $extensionClassesPath . 'ViewHelpers/Page/Field/GroupViewHelper.php',
	'tx_fed_viewhelpers_fce_field_groupviewhelper' => $extensionClassesPath . 'ViewHelpers/Fce/Field/GroupViewHelper.php',
	'tx_fed_viewhelpers_fce_field_selectviewhelper' => $extensionClassesPath . 'ViewHelpers/Fce/Field/SelectViewHelper.php',
	'tx_fed_viewhelpers_fce_fieldviewhelper' => $extensionClassesPath . 'ViewHelpers/Fce/FieldViewHelper.php',

);


?>
