<?php 

$extensionClassesPath = t3lib_extMgm::extPath('fed') . 'Classes/';
return array(
        'tx_fed_core_bootstrap' => $extensionClassesPath . 'Core/Bootstrap.php',
		'tx_fed_utility_pdf' => $extensionClassesPath . 'Utility/PDF.php',
		'tx_fed_utility_propertymapper' => $extensionClassesPath . 'Utility/PropertyMapper.php',
);


?>