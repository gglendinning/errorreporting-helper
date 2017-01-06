<?php

namespace ErrorReportingHelper;

class Input
{
	/**
	 * Build the requested error level, either from the form input, the checkboxes, or the default.
	 *
	 * @return integer
	 */
	public static function getErrorLevel()
	{
		// Form field?
		$level = $_REQUEST['level'] ?: 0;
		if ($level) {
			return \intval($level);
		}

		// Checkboxes?
		$recalc = $_REQUEST['recalc'] ?: 0;
		if ($recalc) {
			$errLvl = 0;

			foreach ($_REQUEST as $key => $var) {
				if(\preg_match('/^chk_(\d+)$/', $key, $matches)) {
					$errLvl |= $matches[1];
				}
			}

			return $errLvl;
		}

		// Default - server's current value
		return \error_reporting();
	}
}
