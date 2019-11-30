<?php
namespace AppBundle\Service;

class ImageUploader {
	const JPG_IMAGE_QUALITY = 90;
	const DEFAULT_PATH = 'location_images/'; // This will be under the /web folder.

	public function processUploadedImage($file, $maxWidth = null, $maxHeight = null, $pad = false, $filename = null, $dest = null) {
		// Has to be done before the temp file is moved.
		$mimeType = $file->getMimeType();

		$dest = ($dest) ? $dest : self::DEFAULT_PATH;

		// Move the temporary file into the permanent location and make it readable by all.
		$tempFile = $file->move($dest);
		chmod($tempFile, 0755);

		// Different file types require different processing calls.
		switch($mimeType) {
			case 'image/png':
				$imageCreateFunc = 'imagecreatefrompng';
				break;

			case 'image/jpeg':
				$imageCreateFunc = 'imagecreatefromjpeg';
				break;

			case 'image/gif':
				$imageCreateFunc = 'imagecreatefromgif';
				break;

			default:
				throw new \Exception('Invalid file type. (Must be jpg, png, or gif.)');
				break;
		}

		$filename    = ($filename) ? $filename . '.jpg' : 'upload_' . uniqid() . '.jpg';
		$newFilePath = $dest . $filename;

		// Get the starting dimensions of the file that we'll use to perform resizing calculation.
		$dimensions   = getimagesize($tempFile);
		$uploadWidth  = $dimensions[0];
		$uploadHeight = $dimensions[1];

		// If they're not specifying a height/width, just return the file.
		if ((!$maxWidth && !$maxHeight) || ($uploadWidth < $maxWidth && $uploadHeight < $maxHeight && !$pad) ) {
			rename($tempFile, $dest . $filename);
			$webPath = '/' . $newFilePath;
			return array('webPath' => $webPath, 'filename' => $filename);
		}

		if ($pad) {
			if (($uploadWidth / $uploadHeight) >= ($maxWidth / $maxHeight)) {
				// by width
				if ($uploadWidth > $maxWidth) {
					$newWidth = $maxWidth - 10;
					$newHeight = $uploadHeight * (($maxWidth - 10) / $uploadWidth);
					$newX = 5;
					$newY = round(abs($maxHeight - $newHeight) / 2);
				} else {
					$newWidth = $uploadWidth;
					$newHeight = $uploadHeight;
					$newX = round(abs($maxWidth - $uploadWidth) / 2);
					$newY = round(abs($maxHeight - $uploadHeight) / 2);
				}
			} else {
				// by height
				if ($uploadHeight > $maxHeight) {
					$newWidth = $uploadWidth * (($maxHeight - 10) / $uploadHeight);
					$newHeight = $maxHeight - 10;
					$newX = round(abs($maxWidth - $newWidth) / 2);
					$newY = 5;
				} else {
					$newWidth = $uploadWidth;
					$newHeight = $uploadHeight;
					$newX = round(abs($maxWidth - $uploadWidth) / 2);
					$newY = round(abs($maxHeight - $uploadHeight) / 2);
				}
			}

			$old   = $imageCreateFunc($tempFile);
			$new   = imagecreatetruecolor($maxWidth, $maxHeight);
			$white = imagecolorallocate($new, 255, 255, 255);

			imagefill($new, 0, 0, $white);


			imagecopyresampled($new, $old, $newX, $newY, 0, 0, $newWidth, $newHeight, $uploadWidth, $uploadHeight);

			imagejpeg($new, $newFilePath, self::JPG_IMAGE_QUALITY);

			// Cleanup
			imagedestroy($new);
			imagedestroy($old);


		} else {

			if (($uploadWidth / $uploadHeight) >= ($maxWidth / $maxHeight)) {
				// by width
				$newWidth = $maxWidth;
				$newHeight = $uploadHeight * ($maxWidth / $uploadWidth);
			} else {
				// by height
				$newWidth = $uploadWidth * ($maxHeight / $uploadHeight);
				$newHeight = $maxHeight;
			}

			$new = $imageCreateFunc($tempFile);
			$new = imagescale($new, $newWidth, $newHeight);

			imagejpeg($new, $newFilePath, self::JPG_IMAGE_QUALITY);
			imagedestroy($new);
		}

		// Remove the original temp file.
		unlink($tempFile);
		chmod($newFilePath, 0755);

		$webPath = '/' . $newFilePath;

		return array('webPath' => $webPath, 'filename' => $filename);
	}
}