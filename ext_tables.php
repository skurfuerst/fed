<?php
if (!defined ('TYPO3_MODE')){
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Fce',
	'Fluid Flexible Content Element'
);

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Template',
	'Fluid Template Display'
);

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Datasource',
	'Data Source Display'
);

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Sandbox',
	'FED Sandbox'
);



t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'FED Fluid Extbase Development Framework');


t3lib_extMgm::addLLrefForTCAdescr('tx_fed_domain_model_datasource', 'EXT:fed/Resources/Private/Language/locallang_csh_tx_fed_domain_model_datasource.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_fed_domain_model_datasource');
$TCA['tx_fed_domain_model_datasource'] = array(
	'ctrl' => array(
		'title'				=> 'LLL:EXT:fed/Resources/Private/Language/locallang_db.xml:tx_fed_domain_model_datasource',
		'label' 			=> 'name',
		'tstamp' 			=> 'tstamp',
		'crdate' 			=> 'crdate',
		'dividers2tabs' => true,
		'versioningWS' 		=> 2,
		'versioning_followPages'	=> TRUE,
		'origUid' 			=> 't3_origuid',
		'languageField' 	=> 'sys_language_uid',
		'transOrigPointerField' 	=> 'l10n_parent',
		'transOrigDiffSourceField' 	=> 'l10n_diffsource',
		'delete' 			=> 'deleted',
		'enablecolumns' 	=> array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/DataSource.php',
		'iconfile' 			=> t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_fed_domain_model_datasource.gif'
	),
);

t3lib_extMgm::addLLrefForTCAdescr('tx_fed_domain_model_fce', 'EXT:fed/Resources/Private/Language/locallang_csh_tx_fed_domain_model_fce.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_fed_domain_model_fce');
$TCA['tx_fed_domain_model_fce'] = array(
	'ctrl' => array(
		'title'				=> 'LLL:EXT:fed/Resources/Private/Language/locallang_db.xml:tx_fed_domain_model_fce',
		'label' 			=> 'filename',
		'tstamp' 			=> 'tstamp',
		'crdate' 			=> 'crdate',
		'dividers2tabs' => true,
		'versioningWS' 		=> 2,
		'versioning_followPages'	=> TRUE,
		'origUid' 			=> 't3_origuid',
		'languageField' 	=> 'sys_language_uid',
		'transOrigPointerField' 	=> 'l10n_parent',
		'transOrigDiffSourceField' 	=> 'l10n_diffsource',
		'delete' 			=> 'deleted',
		'enablecolumns' 	=> array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Fce.php',
		'iconfile' 			=> t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_fed_domain_model_fce.gif'
	),
);

## KICKSTARTER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the kickstarter

t3lib_extMgm::addTCAcolumns('tt_content', array(
	'tx_fed_fcecontentarea' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:fed/locallang_db.xml:tt_content.tx_fed_fcecontentarea',
		'config' => Array (
			'type' => 'passthrough',
		)
	),
	'tx_fed_fceuid' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:fed/locallang_db.xml:tt_content.tx_fed_fceuid',
		'config' => Array (
			'type' => 'select',
			'foreign_table' => 'tx_fed_domain_model_fce',
			'minItems' => 1,
			'maxItems' => 1
		)
	),
), 1);

require_once t3lib_extMgm::extPath($_EXTKEY , 'Configuration/Wizard/FlexFormCodeEditor.php');

t3lib_extMgm::addPlugin(array('FED Flexible Content Element', 'fed_fce'), 'CType');
$TCA['tt_content']['types']['fed_fce']['showitem'] = 'CType;;4;button;1-1-1, header,tx_fed_fceuid,pi_flexform';


$TCA['tt_content']['types']['list']['subtypes_addlist']['fed_datasource'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue('fed_datasource', 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForms/DataSource.xml');

if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_fed_configuration_wizard_datasource'] = t3lib_extMgm::extPath($_EXTKEY, 'Configuration/Wizard/DataSource.php');
}


$TCA['tt_content']['types']['list']['subtypes_addlist']['fed_template'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue('fed_template', 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForms/Template.xml');

if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_fed_configuration_wizard_template'] = t3lib_extMgm::extPath($_EXTKEY, 'Configuration/Wizard/Template.php');
}

$TCA['tt_content']['types']['list']['subtypes_addlist']['fed_sandbox'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue('fed_sandbox', 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForms/Sandbox.xml');

?>