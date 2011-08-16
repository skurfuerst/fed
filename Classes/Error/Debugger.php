<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Extbase Team
*  All rights reserved
*
*  This class is a backport of the corresponding class of FLOW3.
*  All credits go to the v5 team.
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * A debugging utility class
 *
 * @version $Id: $
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @author Robert Lemke <robert@typo3.org>
 * @author Felix Oertel <oertel@networkteam.com>
 */
class Tx_Fed_Error_Debugger {
	/**
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage
	 */
	static protected $renderedObjects;

	/**
	 * Clear the state of the debugger
	 *
	 * @return void
	 */
	static public function clearState() {
		self::$renderedObjects = t3lib_div::makeInstance('Tx_Extbase_Persistence_ObjectStorage');
	}

	/**
	 * Renders a dump of the given variable
	 *
	 * @param mixed $variable
	 * @param integer $level the currrent recursion level.
	 * @return string
	 */
	static public function renderDump($variable, $level) {
		if ($level > 50) {
			return 'RECURSION ... ' . chr(10);
		}
		if (is_string($variable)) {
			$dump = sprintf('\'<span class="debug-string">%s</span>\' (%s)', htmlspecialchars((strlen($variable) > 2000) ? substr($variable, 0, 2000) . '…' : $variable), strlen($variable));
		} elseif (is_numeric($variable)) {
			$dump = sprintf('%s %s', gettype($variable), $variable);
		} elseif (is_array($variable)) {
			$dump = self::renderArrayDump($variable, $level + 1);
		} elseif (is_object($variable)) {
			$dump = self::renderObjectDump($variable, $level + 1);
		} elseif (is_bool($variable)) {
			$dump = $variable ? 'TRUE' : 'FALSE';
		} elseif (is_null($variable) || is_resource($variable)) {
			$dump = gettype($variable);
		}
		return $dump;
	}

	/**
	 * Renders a dump of the given array
	 *
	 * @param array $array
	 * @param integer $level
	 * @return string
	 */
	static protected function renderArrayDump($array, $level) {
		$type = is_array($array) ? 'array' : get_class($array);
		$dump = $type . (count($array) ? '(' . count($array) .')' . chr(10) : '(empty)');
		foreach ($array as $key => $value) {
			$dump .= str_repeat(' ', $level) . self::renderDump($key, 0) . ' => ';
			$dump .= self::renderDump($value, $level + 1) . chr(10);
		}
		return $dump;
	}

	/**
	 * Renders a dump of the given object
	 *
	 * @param object $object
	 * @param integer $level
	 * @param boolean $renderProperties
	 * @return string
	 */
	static protected function renderObjectDump($object, $level, $renderProperties = TRUE) {
		$dump = '';
		$scope = '';
		$additionalAttributes = '';

		if ($object instanceof Tx_Extbase_Persistence_LazyLoadingProxy) {
			$object = $object->_loadRealInstance();
		}

		$classReflection = new Tx_Extbase_Reflection_ClassReflection(get_class($object));

		if ($object instanceof t3lib_Singleton) {
			$scope = 'singleton';
		} else {
			$scope = 'prototype';
		}

		if (self::$renderedObjects->contains($object)) {
			$renderProperties = FALSE;
		} elseif ($renderProperties === TRUE) {
			$dump .= '<a id="' . spl_object_hash($object) . '"></a>';
			self::$renderedObjects->attach($object);
		}

		$className = get_class($object);

		$dump .= '<span class="debug-object' . $additionalAttributes . '" title="' . spl_object_hash($object) . '">' . $className . '</span>';

		$dump .= ($scope ? '<span class="debug-scope">' . $scope .'</span>' : '');

		if ($object instanceof Tx_Extbase_DomainObject_AbstractEntity) {
			$persistenceType = 'entity';
		} elseif ($object instanceof Tx_Extbase_DomainObject_AbstractValueObject) {
			$persistenceType = 'valueobject';
		}
		if ($persistenceType) {
			$dump .= '<span class="debug-ptype">' . $persistenceType . '</span>';
		}

		if ($renderProperties === TRUE) {

			if ($object instanceof Tx_Extbase_Persistence_ObjectStorage) {
				$dump .= ' (' . (count($object) ? count($object) : 'empty') . ')' . chr(10);
				foreach ($object as $value) {
					$dump .= str_repeat(' ', $level);
					$dump .= self::renderDump($value, $level + 1) . chr(10);
				}
			} else {
				$dump .= chr(10);
				foreach ($classReflection->getProperties() as $property) {
					$dump .= str_repeat(' ', $level) . '<span class="debug-property">' . $property->getName() . '</span> => ';
					// @todo this only works PHP > 5.3 for now ... we have to fix this in extbase's propertyReflection
					$property->setAccessible(TRUE);
					$value = $property->getValue($object);
					if (is_array($value)) {
						$dump .= self::renderDump($value, $level + 1) . chr(10);
					} elseif (is_object($value)) {
						$dump .= self::renderDump($value, $level + 1) . chr(10);
					} else {
						$dump .= self::renderDump($value, $level) . chr(10);
					}
				}
			}
		} elseif (self::$renderedObjects->contains($object)) {
			$dump = '<a href="#' . spl_object_hash($object) . '" class="debug-seeabove" title="see above">' . $dump . '</a>';
		}
		return $dump;
	}

	/**
	 * A var_dump function optimized for extbase's object structures
	 *
	 * @param mixed $variable The variable to display a dump of
	 * @param boolean $return If TRUE, returns output instead of echoing
	 * @return void
	 */
	static public function var_export($variable, $return=FALSE) {
		self::clearState();
		$dump = '
			<style>
.Tx-Fed-Error-Debugger-VarDump {
	display: block;
	float: left;
	background: #b9b9b9;
	border: 10px solid #b9b9b9;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
	border-radius: 10px;
	-moz-box-shadow: 0px 0px 20px #333;
	-webkit-box-shadow: 0px 0px 20px #333;
	box-shadow: 0px 0px 20px #333;
	position: relative;
	left: 150px;
	z-index: 999;
	margin: 20px 0 0 0;
	width: 80%;
}

.Tx-Fed-Error-Debugger-VarDump-Top {
	background: #eee;
	font: normal bold 12px "Lucida Grande", sans-serif;
	padding: 5px;
}

.Tx-Fed-Error-Debugger-VarDump-Center {
	background: #b9b9b9 url("LinesBackground.png") 0 18px repeat;
	font: normal normal 11px/18px Monospaced, "Lucida Console", monospace;
	padding: 18px 10px;
}

.Tx-Fed-Error-Debugger-VarDump-Center pre {
	margin: 0;
}

.Tx-Fed-Error-Debugger-VarDump-Center, .Tx-ExtDebug-Error-Debugger-VarDump-Center pre, .Tx-ExtDebug-Error-Debugger-VarDump-Center p, .Tx-ExtDebug-Error-Debugger-VarDump-Center a, .Tx-ExtDebug-Error-Debugger-VarDump-Center strong, .Tx-ExtDebug-Error-Debugger-VarDump-Center .debug-string{
	font: normal normal 11px/18px Monospaced, "Lucida Console", monospace;
}

.Tx-Fed-Error-Debugger-VarDump-Center .debug-string {
	color: black;
}

.Tx-Fed-Error-Debugger-VarDump-Center .debug-object {
	color: #004fb0;
	padding: 0px 4px;
}

.Tx-Fed-Error-Debugger-VarDump-Center .debug-unregistered {
	background-color: #dce1e8;
}

.Tx-Fed-Error-Debugger-VarDump-Center .debug-scope, .Tx-ExtDebug-Error-Debugger-VarDump-Center .debug-ptype, .Tx-ExtDebug-Error-Debugger-VarDump-Center .debug-proxy, .Tx-ExtDebug-Error-Debugger-VarDump-Center .debug-filtered {
	color: white;
	font-size: 10px;
	line-height: 16px;
	padding: 1px 4px;
}

.Tx-Fed-Error-Debugger-VarDump-Center .debug-scope {
	background-color: #3e7fe1;
}

.Tx-Fed-Error-Debugger-VarDump-Center .debug-ptype {
	background-color: #87cd3b;
}

.Tx-Fed-Error-Debugger-VarDump-Center .debug-proxy {
	background-color: #b0000a;
}

.Tx-Fed-Error-Debugger-VarDump-Center .debug-filtered {
	background-color: #8c8c8c;
}

.Tx-Fed-Error-Debugger-VarDump-Center .debug-seeabove {
	text-decoration: none;
	font-style: italic;
	font-weight: normal;
}

.Tx-Fed-Error-Debugger-VarDump-Center .debug-property {
	color: #555555;
	line-height: 16px;
	padding: 1px 2px;
}
			</style>
			<div class="Tx-Fed-Error-Debugger-VarDump">
				<div class="Tx-Fed-Error-Debugger-VarDump-Top">
					Extbase Variable Dump
				</div>
				<div class="Tx-Fed-Error-Debugger-VarDump-Center">
					<pre dir="ltr">' . self::renderDump($variable, 0) . '</pre>
				</div>
			</div>
		';
		if ($return === TRUE) {
			return $dump;
		} else {
			echo $dump;
		}
	}

	/**
	 * A var_dump function optimized for extbase's object structures
	 *
	 * @param mixed $variable The variable to display a dump of
	 * @return void
	 */
	static public function var_dump($variable) {
		return self::var_export($variable, FALSE);
	}
}
?>
