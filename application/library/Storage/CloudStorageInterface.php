<?php


/**
 * 云存储接口
 *
 */
interface  CloudStorageInterface {
	public static function upload($picName, $picContent);
	public static function generateThumbnail($picName, $width = null, $height = null, $quality = null, $format = null, $isCrop = FALSE);
}

?>
