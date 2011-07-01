<?php

/*                                                                        *
 * This script belongs to the FLOW3 package "Fluid".                      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * This view helper implements an if/else condition.
 * @see Tx_Fluid_Core_Parser_SyntaxTree_ViewHelperNode::convertArgumentValue() to find see how boolean arguments are evaluated
 *
 * = Conditions =
 *
 * As a condition is a boolean value, you can just use a boolean argument.
 * Alternatively, you can write a boolean expression there.
 * Boolean expressions have the following form:
 * XX Comparator YY
 * Comparator is one of: ==, !=, <, <=, >, >= and %
 * The % operator converts the result of the % operation to boolean.
 * Complex expressions are supported via && and ||
 * Parenthesis may be used to group && and || expressions
 *
 * XX and YY can be one of:
 * - number
 * - Object Accessor
 * - Array
 * - a ViewHelper
 * - singlequoted simple strings
 * - complex comparisons
 *
 * <code title="condition example">
 * <f:if condition="{rank} > 100">
 *   Will be shown if rank is > 100
 * </f:if>
 * <f:if condition="{rank} % 2">
 *   Will be shown if rank % 2 != 0.
 * </f:if>
 * <f:if condition="{rank} == {k:bar()}">
 *   Checks if rank is equal to the result of the ViewHelper "k:bar"
 * </f:if>
 * <f:if condition="'{foo}' == '{bar}'">
 *   Checks if string in {foo} equals string in var {bar}
 * </f:if>
 * <f:if condition="'{foo}' == '{bar}' && '{foo}' != 'invalidString'>
 *   CHecks if two strings match and the first string is not 'invalidString'
 * </f:if>
 * </code>
 *
 * = Examples =
 *
 * <code title="Basic usage">
 * <f:if condition="somecondition">
 *   This is being shown in case the condition matches
 * </f:if>
 * </code>
 *
 * Everything inside the <f:if> tag is being displayed if the condition evaluates to TRUE.
 *
 * <code title="If / then / else">
 * <f:if condition="somecondition">
 *   <f:then>
 *     This is being shown in case the condition matches.
 *   </f:then>
 *   <f:else>
 *     This is being displayed in case the condition evaluates to FALSE.
 *   </f:else>
 * </f:if>
 * </code>
 * <output>
 * Everything inside the "then" tag is displayed if the condition evaluates to TRUE.
 * Otherwise, everything inside the "else"-tag is displayed.
 * </output>
 *
 * <code title="inline notation">
 * {f:if(condition: someCondition, then: 'condition is met', else: 'condition is not met')}
 * </code>
 * <output>
 * The value of the "then" attribute is displayed if the condition evaluates to TRUE.
 * Otherwise, everything the value of the "else"-attribute is displayed.
 * </output>
 *
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 */
class Tx_Fed_ViewHelpers_IfViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractConditionViewHelper {

	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('condition', 'mixed', 'View helper condition expression, evaled', TRUE);
	}

	/**
	 * renders <f:then> child if $condition is true, otherwise renders <f:else> child.
	 *
	 * @return string the rendered string
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 * @author Bastian Waidelich <bastian@typo3.org>
	 * @api
	 */
	public function render() {
		$condition = $this->arguments['condition'];
		if (is_array($condition)) {
			return (count($condition) > 0);
		} else if ($condition instanceof Countable) {
			return (count($condition) > 0);
		} else if (is_string($condition) && trim($condition) === '') {
			if (trim($condition) === '') {
				return $this->renderElseChild();
			} else if (preg_match("/[a-z^']/", $condition)) {
				$condition = "'{$condition}'";
			}
		} else if (is_object($condition)) {
			if ($condition instanceof Iterator && method_exists($condition, 'count')) {
				return (call_user_method('count', $condition) > 0);
			} else if ($condition instanceof Tx_Extbase_Reflection_ObjectAccess) {
				return (count($condition->getAccessibleProperties($object)) > 0);
			} else if ($condition instanceof stdClass) {
				return $this->renderThenChild();
			} else {
				throw new Exception('Unknown object type in IfViewHelper condition: ' . get_class($condition), 1309493049);
			}
		}
		$lParenthesisCount = substr_count($condition, '(');
		$rParenthesisCount = substr_count($condition, ')');
		$singleQuoteCount = substr_count($condition, '\'');
		$escapedSingleQuoteCount = substr_count($condition, '\\\'');
		if ($rParenthesisCount !== $lParenthesisCount) {
			throw new Exception('Syntax error in IfViewHelper condition', 1309490125);
		}
		if (($singleQuoteCount-$escapedSingleQuoteCount) % 2 != 0) {
			throw new Exception('Syntax error in IfViewHelper condition', 1309490125);
		}
		$evaluation = NULL;
		$evaluationCondition = $condition;
		$evaluationCondition = trim($condition, ';');
		$evaluationExpression = "\$evaluation = (bool) ({$evaluationCondition});";
		eval($evaluationExpression);
		if ($evaluation === NULL) {
			throw new Exception('Syntax error in computed IfViewHelper expression', 1309537403);
		} else if ($evaluation === TRUE) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}
}
?>