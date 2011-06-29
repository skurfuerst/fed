<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}


Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'API',
	array(
		'FileuploadWidget' => 'upload',
		'RecordSelectorWidget' => 'search',
		'Hash' => 'request',
		'Tool' => 'clearCache,inspectCookie,removeCookie,setCookie,inspectSession,setSession,removeSession',
	),
	array(
		'FileuploadWidget' => 'upload',
		'RecordSelectorWidget' => 'search',
		'Hash' => 'request',
		'Tool' => 'clearCache,inspectCookie,removeCookie,setCookie,inspectSession,setSession,removeSession',
	)
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Fce',
	array(
		'FlexibleContentElement' => 'show',
	),
	array(
		//'Template' => 'show',
	)
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Template',
	array(
		'Template' => 'show',
	),
	array(
		//'Template' => 'show',
	)
);


Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Datasource',
	array(
		'DataSource' => 'list,show,rest',
	),
	array(
		'DataSource' => 'rest',
	)
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Sandbox',
	array(
		'Sandbox' => 'show',
	),
	array(
		//'DataSource' => 'show',
	)
);

if (TYPO3_MODE == 'BE') {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:fed/Classes/Backend/ContentSaveHook.php:Tx_Fed_Backend_ContentSaveHook';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][] = 'EXT:fed/Classes/Backend/Preview.php:Tx_Fed_Backend_Preview';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_befunc.php']['getFlexFormDSClass'][] = 'EXT:fed/Classes/Backend/DynamicFlexForm.php:Tx_Fed_Backend_DynamicFlexForm';
}

t3lib_extMgm::addTypoScript($_EXTKEY,'setup',
    '[GLOBAL]
	tt_content.fed_fce = COA
	tt_content.fed_fce.10 < lib.stdHeader
	tt_content.fed_fce.20 < tt_content.list.20.fed_fce
	tt_content.fed_template = COA
	tt_content.fed_template.10 < lib.stdHeader
	tt_content.fed_template.20 < tt_content.list.20.fed_template
	tt_content.fed_datasource = COA
	tt_content.fed_datasource.10 < lib.stdheader
	tt_content.fed_datasource.20 < tt_content.list.20.fed_datasource
	'
, TRUE);

t3lib_extMgm::addPageTSConfig('
	mod.wizards.newContentElement.wizardItems.special.elements.fed_fce {
		icon = ../typo3conf/ext/fed/Resources/Public/Icons/Plugin.png
		title = Fluid Flexible Content Element
		description = Flexible Content Element using a Fluid template
		tt_content_defValues {
			CType = fed_fce
		}
	}
	mod.wizards.newContentElement.wizardItems.special.elements.fed_template {
		icon = ../typo3conf/ext/fed/Resources/Public/Icons/Plugin.png
		title = Fluid Template Display
		description = Display a standalone Fluid template with optional template variables
		tt_content_defValues {
			CType = fed_template
		}
	}
	mod.wizards.newContentElement.wizardItems.special.elements.fed_datasource {
		icon = ../typo3conf/ext/fed/Resources/Public/Icons/Plugin.png
		title = Fluid DataSource Display
		description = Display a standalone Fluid template with attached DataSource(s)
		tt_content_defValues {
			CType = fed_datasource
		}
	}
	mod.wizards.newContentElement.wizardItems.special.show := addToList(fed_fce,fed_template,fed_datasource)
');

?>