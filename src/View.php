<?php

namespace ErrorReportingHelper;

class View
{

	/**
	 * Render the view.
	 *
	 * @param array $data Form data.
	 *
	 * @return void
	 */
	public function render(array $data)
	{
		$loader = new \Twig_Loader_Filesystem(__DIR__ . '/../templates');
		$twig = new \Twig_Environment($loader, array(
			//	'cache' => '/tmp',
		    'debug' => true,
		));
		$twig->addExtension(new \Twig_Extension_Debug());

		echo $twig->render('index.twig', $data);
	}
}
