<?php

namespace ErrorReportingHelper;

class Controller
{
	/**
	 * Page controller.
	 *
	 * @return void
	 */
	public function run()
	{
		$errorLevel = Input::getErrorLevel();

		$data['errorlevel'] = $errorLevel;
		$data['results'] = Logic::getBreakdown($errorLevel);
		$data['collated'] = Logic::buildCollatedText($errorLevel);
		$data['reverseCollated'] = Logic::buildReverseCollatedText($errorLevel);
		$data['action'] = 'index.php?recalc=1';

		$v = new View();
		$v->render($data);
	}
}
