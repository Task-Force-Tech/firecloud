<?php
namespace AppBundle\Service;

use \AppBundle\Entity\Location;
use \AppBundle\Entity\Photo;
use \AppBundle\Entity\User;

class CodeManager {

	public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
	{
		$this->doctrine = $doctrine;
	}

	public function checkCode($code)
	{

		$paymentRepo = $this->doctrine->getRepository('AppBundle:Payment');
		$payment = $paymentRepo->findOneBy(array('promoCode' => $code));
		if ($payment) {
			return array('success' => false, 'error' => 'Sorry, that code has already been used.');
		}

		$chunks = explode('-', $code);
		$keyChecksum = array_pop($chunks);

		// Do the checksum first since it's the least intensive and can be a quick
		// way to validate that at least things *look* right.
		$code = substr($code, 0, -6);
		$code = str_replace('-', '^', $code);
		$hash = md5($code);
		$checkSum = substr($hash, 4, 5);
		if ($checkSum != $keyChecksum) {
			return array('success' => false, 'error' => 'Sorry, that code is invalid.');
		}

		$keyChunk = array_shift($chunks);
		$key = str_pad(base_convert($keyChunk, 36, 10), 8, '0', STR_PAD_LEFT);

		foreach ($chunks as $k => $chunk) {
			switch ($k) {
				default:
				case 0:
					$val = str_pad(base_convert(substr($chunk, 0, 2), 16, 10), 2, '0', STR_PAD_LEFT);
					break;
				case 1:
					$val = str_pad(base_convert(substr($chunk, 3, 2), 16, 10), 2, '0', STR_PAD_LEFT);
					break;
			}

			if (strval($val) !== strval(substr($key, 2 * $k, 2))) {
				return array('success' => false, 'error' => 'Sorry, that code is invalid.');
			}

		}

		return array('success' => true);

	}


	public function generateCodes($numCodes = 1, $chunks = 4)
	{
		$codes = array();
		for ($i = 1; $i <= $numCodes; $i++) {
			$rand = rand(0, 60466175);
			$key = str_pad(base_convert($rand, 10, 36), 5, '0', STR_PAD_LEFT);
			$baseIntval = str_pad( base_convert($key, 36, 10), 8, '0', STR_PAD_LEFT );

			$code = $key . '-';

			// Generate blocks based on the total # of chunks. We do two less than specified
			// since the first is the key and the last is the checksum.
			for ($x = 1; $x <= $chunks - 2; $x++) {
				$code .= $this->getChunk($baseIntval, $x) . '-';
			}

			// Add a checksum to the end so we can easily detect a couple spoofing
			// attempts. Also this gives us a fast method to check for key validity
			// before we start doing more intensive checking.
			$tempCode = rtrim($code, '-');
			$replaced = str_replace('-', '^', $tempCode);
			$hash = md5($replaced);
			$checkSum = substr($hash, 4, 5);
			$code .= $checkSum;

			$codes[] = $code;
		}

		return $codes;
	}


	private function getChunk($baseIntval, $position)
	{
		// Gets different 2-digit portions of the key based on position. So the first
		// position gets the first two digits, the second position gets the next two, etc.
		$valueAtPosition = substr($baseIntval, ($position - 1) * 2, 2);

		$val = str_pad(base_convert($valueAtPosition, 10, 16), 2, 0, STR_PAD_LEFT);
		$padding = str_pad(base_convert(rand(255,4096), 10, 16), 3, 0, STR_PAD_LEFT);

		switch ($position) {
			default:
			case 1: // xx000
				return $val . $padding;
				break;
			case 2: // 000xx
				return $padding . $val;
				break;
		}
	}
}