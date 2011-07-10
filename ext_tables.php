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

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Hash',
	'FED Hasher'
);

t3lib_extMgm::addPlugin(array('FED Flexible Content Element', 'fed_fce'), 'CType');
t3lib_extMgm::addPlugin(array('FED Template Display', 'fed_template'), 'CType');
t3lib_extMgm::addPlugin(array('FED DataSource Display', 'fed_datasource'), 'CType');

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_addlist']['fed_fce'] = 'pi_flexform';
$TCA['tt_content']['types']['list']['subtypes_addlist']['fed_sandbox'] = 'pi_flexform';

t3lib_extMgm::addToAllTCAtypes('tt_content', 'tx_fed_fcecontentarea;;;1-1-1');
t3lib_extMgm::addPiFlexFormValue('fed_sandbox', 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForms/Sandbox.xml');
t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'FED Fluid Extbase Development Framework');

$TCA['tt_content']['types']['fed_fce']['showitem'] = '
--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.general;general,
--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.header;header,
--div--;Flexible Content Element, tx_fed_fcefile, pi_flexform;Flexible Content Element Fields,
--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.appearance,
--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.frames;frames,
--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.visibility;visibility,
--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.access;access,
--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.extended,tx_fed_fcecontentarea
 ';
$TCA['tt_content']['types']['fed_template']['showitem'] = '
--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.general;general,
--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.header;header,
--div--;Fluid Template, pi_flexform;Fluid Template settings,
--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.appearance,
--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.frames;frames,
--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.visibility;visibility,
--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.access;access,
--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.extended,tx_fed_fcecontentarea
';
$TCA['tt_content']['types']['fed_datasource']['showitem'] = '
--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.general;general,
--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.header;header,
--div--;DataSource Display, pi_flexform;DataSource Display settings,
--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.appearance,
--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.frames;frames,
--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.visibility;visibility,
--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.access;access,
--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.extended,tx_fed_fcecontentarea
';


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


t3lib_extMgm::addTCAcolumns('tt_content', array(
	'tx_fed_fcecontentarea' => Array (
		'exclude' => 1,
		#'label' => 'LLL:EXT:fed/Resources/Private/Language/locallang_db.xml:tt_content.tx_fed_fcecontentarea',
		'config' => Array (
			'type' => 'user',
			'userFunc' => 'Tx_Fed_Backend_HiddenField->renderField',
		)
	),
	'tx_fed_fcefile' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:fed/Resources/Private/Language/locallang_db.xml:tt_content.tx_fed_fcefile',
		'config' => Array (
			'type' => 'user',
			'userFunc' => 'Tx_Fed_Backend_FCESelector->renderField',
		)
	),
), 1);


require_once t3lib_extMgm::extPath($_EXTKEY , 'Configuration/Wizard/FlexFormCodeEditor.php');



?>