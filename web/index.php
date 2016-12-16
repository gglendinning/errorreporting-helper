<?php
require_once __DIR__ . '/../vendor/autoload.php';

class Model
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
	 * Build an array where only the set bits are populated.
	 *
	 * @param integer $errorLevel Error level value.
	 *
	 * @return array
	 */
	static public function buildCollatedText($errorLevel)
	{
		$collated = array();
		$reverseCollated = array('E_ALL');

		$data = self::getBreakdown($errorLevel);

		for ($i = 0; $i < 15;  $i++ ) {
			$pow = pow(2, $i);
			if ($data[$pow]['bit']) {
				$collated[$i] = $data[$pow]['label'];
			} else {
				$reverseCollated[$i] = $data[$pow]['label'];
			}
		}

		return array(implode(' | ', $collated), implode(' & ~', $reverseCollated));
	}

}

class View
{
	public function render($data)
	{
		$loader = new \Twig_Loader_Filesystem(__DIR__ . '/../templates');
		$twig = new \Twig_Environment($loader, array(
			//	'cache' => '/tmp',
		    'debug' => true,
		));
		$twig->addExtension(new Twig_Extension_Debug());

		echo $twig->render('index.twig', $data);
	}
}

class Controller
{
	public function getErrorLevel()
	{
		$level = $_REQUEST['level'] ?: 0;
		$recalc = $_REQUEST['recalc'] ?: 0;

		if ($level) {
			$errLvl = \intval($level);
		} elseif ($recalc) {
			$errLvl = 0;

			foreach ($_REQUEST as $key => $var) {
				if(\preg_match('/^chk_(\d+)$/', $key, $matches)) {
					$errLvl |= $matches[1];
				}
			}
		} else {
			$errLvl = \error_reporting();
		}

		return $errLvl;
	}

	public function run()
	{
		$errorLevel = $this->getErrorLevel();
		list($collated, $reverseCollated) = \Model::buildCollatedText($errorLevel);

		$data['errorlevel'] = $errorLevel;
		$data['results'] = \Model::getBreakdown($errorLevel);
		$data['collated'] = $collated;
		$data['reverseCollated'] = $reverseCollated;
		$data['action'] = 'index.php?recalc=1';

		$v = new View();
		$v->render($data);
	}
}

$c = new Controller();
$c->run();
