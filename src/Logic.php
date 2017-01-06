<?php

namespace ErrorReportingHelper;

class Logic
{
	static private $labels = array(
		E_ERROR => 'E_ERROR',
		E_WARNING => 'E_WARNING',
		E_PARSE => 'E_PARSE',
		E_NOTICE => 'E_NOTICE',
		E_CORE_ERROR => 'E_CORE_ERROR',
		E_CORE_WARNING => 'E_CORE_WARNING',
		E_COMPILE_ERROR => 'E_COMPILE_ERROR',
		E_COMPILE_WARNING => 'E_COMPILE_WARNING',
		E_USER_ERROR => 'E_USER_ERROR',
		E_USER_WARNING => 'E_USER_WARNING',
		E_USER_NOTICE => 'E_USER_NOTICE',
		E_STRICT => 'E_STRICT',
		E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
		E_DEPRECATED => 'E_DEPRECATED',
		E_USER_DEPRECATED => 'E_USER_DEPRECATED',
	);

	/**
	 * Turn the error_reporting level into an array of labelled bit fields.
	 *
	 * @param type $errLvl
	 * @return type
	 */
	static public function getBreakdown($errLvl)
	{
		$data = array();

		for ($i = 0; $i < 15;  $i++ ) {
			$pow = pow(2, $i);
			$label = self::$labels[$pow];
			$data[$pow]['label'] = $label;
			$data[$pow]['i'] = $i;
			$data[$pow]['bit'] = ($errLvl & $pow) ? true : false;
		}

		return $data;
	}
	/**
	 * Build a string where the set bits are ORed together.
	 *
	 * @param integer $errorLevel Error level value.
	 *
	 * @return string
	 */
	static public function buildCollatedText($errorLevel)
	{
		$collated = array();

		$data = self::getBreakdown($errorLevel);

		for ($i = 0; $i < 15; $i++ ) {
			$pow = pow(2, $i);
			if ($data[$pow]['bit']) {
				$collated[$i] = $data[$pow]['label'];
			}
		}

		return implode(' | ', $collated);
	}

	/**
	 * Build a string where the un-set bits are NANDed out of E_ALL.
	 *
	 * @param integer $errorLevel Error level value.
	 *
	 * @return string
	 */
	static public function buildReverseCollatedText($errorLevel)
	{
		$result = array('E_ALL');

		$data = self::getBreakdown($errorLevel);

		for ($i = 0; $i < 15; $i++ ) {
			$pow = pow(2, $i);
			if (!$data[$pow]['bit']) {
				$result[$i] = $data[$pow]['label'];
			}
		}

		return implode(' & ~', $result);
	}

}
