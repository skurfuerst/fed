<?php

$extensionClassesPath = t3lib_extMgm::extPath('fed', 'Classes/');
$extbaseClassesPath = t3lib_extMgm::extPath('extbase', 'Classes/');
return array(
	'tx_extbase_service_cacheservice' => $extbaseClassesPath . "Service/CacheService.php",

	'tx_fed_core_bootstrap' => $extensionClassesPath . 'Core/Bootstrap.php',
	'tx_fed_utility_pdf' => $extensionClassesPath . 'Utility/PDF.php',
	'tx_fed_utility_propertymapper' => $extensionClassesPath . 'Utility/PropertyMapper.php',
	'tx_fed_view_flexiblecontentelementview' => $extensionClassesPath . 'View/FlexibleContentElementView.php',
	'tx_fed_backend_preview' => $extensionClassesPath . 'Backend/Preview.php',
	'tx_fed_backend_fceparser' => $extensionClassesPath . 'Backend/FCEParser.php',
	'tx_fed_backend_contentsavehook' => $extensionClassesPath . 'Backend/ContentSaveHook.php',
	'tx_fed_persistence_repository' => $extensionClassesPath . 'Persistence/Repository.php',
	'tx_fed_domain_model_fce' => $extensionClassesPath . 'Domain/Model/Fce.php',
	'tx_fed_domain_repository_fcerepository' => $extensionClassesPath . 'Domain/Repository/FceRepository.php',

);


?>