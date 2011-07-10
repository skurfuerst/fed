<?php

$extensionClassesPath = t3lib_extMgm::extPath('fed', 'Classes/');
$extensionPath = t3lib_extMgm::extPath('fed');
$extbaseClassesPath = t3lib_extMgm::extPath('extbase', 'Classes/');
return array(
	'tx_extbase_service_cacheservice' => $extbaseClassesPath . "Service/CacheService.php",
	'tx_extbase_service_typoscriptservice' => $extbaseClassesPath . "Service/TypoScriptService.php",

	'tx_fed_tests_unit_basetestcase' => $extensionPath . 'Tests/Unit/BaseTestCase.php',
	'tx_fed_core_bootstrap' => $extensionClassesPath . 'Core/Bootstrap.php',
	'tx_fed_utility_pdf' => $extensionClassesPath . 'Utility/PDF.php',
	'tx_fed_utility_recursionhandler' => $extensionClassesPath . 'Utility/RecursionHandler.php',
	'tx_fed_utility_domainobjectinfo' => $extensionClassesPath . 'Utility/DomainObjectInfo.php',
	'tx_fed_utility_flexform' => $extensionClassesPath . 'Utility/FlexForm.php',
	'tx_fed_utility_json' => $extensionClassesPath . 'Utility/JSON.php',
	'tx_fed_utility_propertymapper' => $extensionClassesPath . 'Utility/PropertyMapper.php',
	'tx_fed_view_flexiblecontentelementview' => $extensionClassesPath . 'View/FlexibleContentElementView.php',
	'tx_fed_view_exposedtemplateview' => $extensionClassesPath . 'View/ExposedTemplateView.php',
	'tx_fed_backend_fceselector' => $extensionClassesPath . 'Backend/FCESelector.php',
	'tx_fed_backend_preview' => $extensionClassesPath . 'Backend/Preview.php',
	'tx_fed_backend_fceparser' => $extensionClassesPath . 'Backend/FCEParser.php',
	'tx_fed_backend_hiddenfield' => $extensionClassesPath . 'Backend/HiddenField.php',
	'tx_fed_backend_contentsavehook' => $extensionClassesPath . 'Backend/ContentSaveHook.php',
	'tx_fed_persistence_repository' => $extensionClassesPath . 'Persistence/Repository.php',

);


?>