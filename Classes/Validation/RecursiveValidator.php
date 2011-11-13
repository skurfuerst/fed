<?php



class Tx_Fed_Validation_RecursiveValidator implements t3lib_Singleton {

	/**
	 * @var Tx_Extbase_Property_MappingResults
	 */
	protected $mappingResults;

	/**
	 * @var Tx_Extbase_Reflection_Service
	 */
	protected $reflectionService;

	/**
	 * @var Tx_Extbase_Validation_ValidatorResolver
	 */
	protected $validatorResolver;

	/**
	 * @var Tx_Extbase_Property_Mapper
	 */
	protected $propertyMapper;

	/**
	 * @var Tx_Fed_Utility_DomainObjectInfo
	 */
	protected $infoService;

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 * @param Tx_Extbase_Reflection_Service $reflectionService
	 */
	public function injectReflectionService(Tx_Extbase_Reflection_Service $reflectionService) {
		$this->reflectionService = $reflectionService;
	}

	/**
	 * @param Tx_Extbase_Validation_ValidatorResolver $validatorResolver
	 */
	public function injectValidatorResolver(Tx_Extbase_Validation_ValidatorResolver $validatorResolver) {
		$this->validatorResolver = $validatorResolver;
	}

	/**
	 * @param Tx_Extbase_Property_Mapper $propertyMapper
	 */
	public function injectPropertyMapper(Tx_Extbase_Property_Mapper $propertyMapper) {
		$this->propertyMapper = $propertyMapper;
	}

	/**
	 * @param Tx_Fed_Utility_DomainObjectInfo $infoService
	 */
	public function injectInfoService(Tx_Fed_Utility_DomainObjectInfo $infoService) {
		$this->infoService = $infoService;
	}

	/**
	 * @param Tx_Extbase_Object_ObjectManager $objectManager
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
		$this->mappingResults = $this->objectManager->get('Tx_Extbase_Property_MappingResults');
	}

	/**
	 * Validates an object recursively, adding errors along the way (indexed by
	 * their respective object paths)
	 *
	 * @param Tx_Extbase_DomainObject_AbstractDomainObject $object
	 * @param array $array
	 * @return Tx_Extbase_Property_MappingResults
	 */
	public function validate(Tx_Extbase_DomainObject_AbstractDomainObject $object, array $path=array()) {
		$className = get_class($object);
		$properties = $this->infoService->getValuesByAnnotation($object, 'var');
		$validator = $this->objectManager->get('Tx_Extbase_Validation_Validator_GenericObjectValidator');
		#var_dump(get_class($object));
		foreach ($properties as $propertyName=>$value) {
			if (substr($propertyName, 0, 1) == '_' || $propertyName == 'uid' || $propertyName == 'pid') {
				unset($properties[$propertyName]);
				continue;
			}
			$value = Tx_Extbase_Reflection_ObjectAccess::getProperty($object, $propertyName);
			if ($value instanceof Tx_Extbase_Persistence_ObjectStorage) {
				array_push($path, $propertyName);
				foreach ($value as $subObject) {
					array_push($path, $iteration);
					$this->validate($subObject, $path);
					array_pop($path);
				}
				array_pop($path);
			} else if ($value instanceof Tx_Extbase_DomainObject_AbstractDomainObject) {
				array_push($path, $propertyName);
				$this->validate($value, $path);
				array_pop($path);
			} else {
				#$validator = $this->validatorResolver->
				$method = "get" . ucfirst($propertyName);
				var_dump($propertyName);
				var_dump($object->$method());
				$isValid = $validator->isPropertyValid($object, $propertyName);
				#var_dump($validator->getErrors());
				var_dump($isValid);
			}
		}
		#$dummy = $this->objectManager->get($className);
		#$this->propertyMapper->map(array_keys($properties), $properties, $dummy);
		#if (method_exists($validator, 'validate')) {
		#	$result = $validator->validate($object);
		#} else {
		#	$result = $validator->isValid($object);
		#}
		#var_dump($result);
		#foreach ($subResult as $error) {
			#$error->
		#	$this->mappingResults->addError($error, implode('.', $path));
		#}
		return $this->mappingResults;
	}

}

?>