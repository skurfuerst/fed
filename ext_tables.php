<?php
if (!defined ('TYPO3_MODE')){
	die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions']['fed']['plugins']['fed_fce']['pluginType'] = 'CType';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions']['fed']['plugins']['fed_template']['pluginType'] = 'CType';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions']['fed']['plugins']['fed_datasource']['pluginType'] = 'CType';


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


t3lib_extMgm::addPlugin(array('FED Flexible Content Element', 'fed_fce'), 'CType');
t3lib_extMgm::addPlugin(array('FED Template Display', 'fed_template'), 'CType');
t3lib_extMgm::addPlugin(array('FED DataSource Display', 'fed_datasource'), 'CType');

$TCA['tt_content']['types']['fed_datasource']['showitem'] = 'CType;;4;button;1-1-1, header,pi_flexform';
$TCA['tt_content']['types']['fed_fce']['showitem'] = 'CType;;4;button;1-1-1, header,tx_fed_fcefile,pi_flexform';
$TCA['tt_content']['types']['fed_template']['showitem'] = 'CType;;4;button;1-1-1, header,pi_flexform';
$TCA['tt_content']['types']['fed_datasource']['showitem'] = 'CType;;4;button;1-1-1, header,pi_flexform';
$TCA['tt_content']['types']['list']['subtypes_addlist']['fed_fce'] = 'pi_flexform';
$TCA['tt_content']['types']['list']['subtypes_addlist']['fed_template'] = 'pi_flexform';
$TCA['tt_content']['types']['list']['subtypes_addlist']['fed_datasource'] = 'pi_flexform';
$TCA['tt_content']['types']['list']['subtypes_addlist']['fed_sandbox'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue('fed_sandbox', 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForms/Sandbox.xml');
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


## KICKSTARTER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the kickstarter

t3lib_extMgm::addTCAcolumns('tt_content', array(
	'tx_fed_fcecontentarea' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:fed/locallang_db.xml:tt_content.tx_fed_fcecontentarea',
		'config' => Array (
			'type' => 'passthrough',
		)
	),
	'tx_fed_fcefile' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:fed/locallang_db.xml:tt_content.tx_fed_fcefile',
		'config' => Array (
			'type' => 'user',
			'userFunc' => 'Tx_Fed_Backend_FCESelector->renderField'
		)
	),
), 1);

require_once t3lib_extMgm::extPath($_EXTKEY , 'Configuration/Wizard/FlexFormCodeEditor.php');



?>