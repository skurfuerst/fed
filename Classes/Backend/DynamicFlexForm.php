<?php







class Tx_Fed_Backend_DynamicFlexForm {

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var Tx_Fed_Domain_Repository_FceRepository
	 */
	protected $fceRepository;

	/**
	 * @var Tx_Fed_Backend_FCEParser
	 */
	protected $fceParser;

	/**
	 * CONSTRUCTOR
	 */
	public function __construct() {
		$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->fceRepository = $this->objectManager->get('Tx_Fed_Domain_Repository_FceRepository');
		$this->fceParser = $this->objectManager->get('Tx_Fed_Backend_FCEParser');
	}

	public function getFlexFormDS_postProcessDS(&$dataStructArray, $conf, &$row, $table, $fieldName) {
		if ($table == 'tt_content') {
			#var_dump($row);
			#exit();
			$uid = $row['tx_fed_fceuid'];
			if ($uid < 1) {
				return;
			}
			$fce = $this->fceRepository->findByUid($uid);
			$templateFile = $fce->getFilename();
			$config = $this->fceParser->getFceDefinitionFromTemplate(PATH_site . $templateFile);

			$flexformTemplateFile = t3lib_extMgm::extPath('fed', 'Resources/Private/Templates/FlexibleContentElement/AutoFlexForm.xml');
			$template = $this->objectManager->get('Tx_Fluid_View_StandaloneView');
			$template->setTemplatePathAndFilename($flexformTemplateFile);
			$template->assign('fce', $config);
			$flexformXml = $template->render();
			$dataStructArray = t3lib_div::xml2array($flexformXml);
		}
	}

}


?>
