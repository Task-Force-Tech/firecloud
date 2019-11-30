<?php
namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
	public function getFilters()
	{
		return array(
			new \Twig_SimpleFilter('yesno', array($this, 'yesnoFilter')),
		);
	}

	public function yesnoFilter($bool)
	{
		if (is_null($bool)) {
			return '';
		}
		return ($bool) ? 'yes' : 'no';
	}
}