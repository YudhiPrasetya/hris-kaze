<?php
/*
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   ImagePlaceholder.php
 * @date   2021-07-8 14:2:39
 */

namespace App\Libraries\Placeholder;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use JetBrains\PhpStorm\ArrayShape;


class ImagePlaceholder {
	/** @param string */
	protected string $text;

	protected string $fontFamily;

	/** @param int */
	protected int $fontSize;

	protected string $type = "regular";

	protected int $iconWidth;

	protected int $iconHeight;

	/**
	 * ImagePlaceholder constructor.
	 *
	 * @param int|null    $fontSize
	 * @param string|null $fontFamily
	 */
	public function __construct(?string $fontFamily = null, string $type = "regular") {
		$this->type = strtolower($type);
		$this->fontFamily = empty($fontFamily) ? match ($this->type) {
			"light" => resource_path("fonts/fontawesome/FontAwesome-Light.ttf"),
			"solid" => resource_path("fonts/fontawesome/FontAwesome-Solid.ttf"),
			"duotone" => resource_path("fonts/fontawesome/FontAwesome-Duotone.ttf"),
			default => resource_path("fonts/fontawesome/FontAwesome-Regular.ttf"),
		} : resource_path("fonts/" . $fontFamily);
	}

	/**
	 * @param             $width
	 * @param             $height
	 * @param string|null $strHex
	 * @param string      $hexBgcolor
	 * @param string      $hexColor
	 *
	 * @return \GdImage
	 */
	public function createIcon($width, $height, string $strHex = null, string $hexBgcolor = '#cdcdcd', string $hexColor = '#fafafa'): \GdImage {
		$this->iconWidth = $width;
		$this->iconHeight = $height;
		$imageBase = imagecreatetruecolor($width, $height);
		imagesavealpha($imageBase, true);
		imageantialias($imageBase, true);
		$image = $this->paintImage($imageBase, $hexBgcolor);
		$color = $this->paintImageText($image, $hexColor);
		// $colorLayer = $this->paintLayerImageText($image, $hexColor);

		// Check whether there is globally set font size or not.
		//$text = mb_convert_encoding($this->code2utf8($strHex), 'UTF-8');
		// Convert string hex to unicode. The fastest way :)
		$textUtf = json_decode('"&#x' . strtoupper($strHex) . ';"');
		$fontSize = $this->setIconSize($width, $height);

		return $this->typeOnImage($image, $fontSize, $color, $textUtf);
	}

	/**
	 * Fill image with given colour.
	 *
	 * @param resource $image
	 * @param string   $color
	 *
	 * @return resource
	 */
	public function paintImage($image, string $color) {
		$backgroundColor = $this->hexToRgb($color);
		$backgroundColor = imagecolorallocatealpha(
			$image,
			$backgroundColor['red'],
			$backgroundColor['green'],
			$backgroundColor['blue'],
			round(0)
		);

		imagefill($image, 0, 0, $backgroundColor);

		return $image;
	}

	/**
	 * Convert HEX string into RGB array.
	 *
	 * @param string $hexColor
	 *
	 * @return array
	 */
	#[ArrayShape(['red' => "float|int", 'green' => "float|int", 'blue' => "float|int"])]
	public function hexToRgb(string $hexColor): array {
		$hexColor = str_replace('#', '', $hexColor);
		$hexColor = strlen($hexColor) <= 3 ? $hexColor[0] . $hexColor[0] . $hexColor[1] . $hexColor[1] . $hexColor[2] . $hexColor[2] : $hexColor;

		return [
			'red'   => hexdec(substr($hexColor, 0, 2)),
			'green' => hexdec(substr($hexColor, 2, 2)),
			'blue'  => hexdec(substr($hexColor, 4, 2)),
		];
	}

	/**
	 * Set text colour.
	 *
	 * @param resource $image - gd resource.
	 * @param string   $color - hex encoded colour.
	 *
	 * @return int
	 */
	public function paintImageText($image, string $color): int {
		$textColor = $this->hexToRgb($color);

		return imagecolorallocatealpha(
			$image,
			$textColor['red'],
			$textColor['green'],
			$textColor['blue'],
			0
		);
	}

	/**
	 * @param        $image
	 * @param string $color
	 *
	 * @return int
	 */
	public function paintLayerImageText($image, string $color): int {
		$textColor = $this->hexToRgb($color);

		return imagecolorallocatealpha(
			$image,
			$textColor['red'],
			$textColor['green'],
			$textColor['blue'],
			round(127 * 0.4)
		);
	}

	/**
	 * Set font size based on image width and height.
	 *
	 * @param int $width
	 * @param int $height
	 *
	 * @return int
	 */
	public function setIconSize(int $width, int $height): int {
		return $width > $height ? $height / 1.65 : $width / 1.65;
	}

	/**
	 * Write size or given text on image.
	 *
	 * @param resource $image    - gd resource.
	 * @param int      $fontSize - size of the text.
	 * @param int      $color    - allocated colour with imagecolorallocate.
	 * @param string   $text     - text that should be writteon image.
	 *
	 * @return resource
	 */
	public function typeOnImage($image, int $fontSize, int $color, string $text = null): \GdImage {
		$width = imagesx($image);
		$height = imagesy($image);
		$text = $text ?? "$width x $height";

		$position1 = $this->calculateTextBox($fontSize, 0, $text);
		$position = $this->getTextPosition($image, $fontSize, $text);

		imagettftext(
			$image,
			$fontSize,
			0,
			$position['x'],
			$position['y'],
			$color,
			$this->fontFamily,
			$text
		);

		return $image;
	}

	/**
	 * simple function that calculates the *exact* bounding box (single pixel precision).
	 * The function returns an associative array with these keys:
	 * left, top:  coordinates you will pass to imagettftext
	 * width, height: dimension of the image you have to create
	 *
	 * @param float  $fontSize
	 * @param float  $font_angle
	 * @param string $text
	 *
	 * @return array
	 */
	#[ArrayShape(["left" => "float|int", "top" => "float|int", "width" => "mixed", "height" => "mixed", "box" => "array|false"])]
	function calculateTextBox(float $fontSize, float $font_angle, string $text
	): array {
		$rect = imagettfbbox($fontSize, $font_angle, $this->fontFamily, $text);
		$minX = min(array($rect[0], $rect[2], $rect[4], $rect[6]));
		$maxX = max(array($rect[0], $rect[2], $rect[4], $rect[6]));
		$minY = min(array($rect[1], $rect[3], $rect[5], $rect[7]));
		$maxY = max(array($rect[1], $rect[3], $rect[5], $rect[7]));

		return array(
			"left"   => abs($minX),
			"top"    => abs($minY),
			"width"  => $maxX - $minX,
			"height" => $maxY - $minY,
			"box"    => $rect,
		);
	}

	/**
	 * @param        $image
	 * @param int    $fontSize
	 * @param string $text
	 *
	 * @return float[]|int[]
	 */
	#[ArrayShape(['x' => "float|int", 'y' => "float|int"])]
	public function getTextPosition($image, int $fontSize, string $text
	): array {
		$textBox = imagettfbbox($fontSize, 0, $this->fontFamily, $text);

		// $rect = $this->calculateTextBox($fontSize, 0, $this->fontFamily, $text);
		$textWidth = abs($textBox[2]) - abs($textBox[0]);
		$textHeight = abs($textBox[5]) - abs($textBox[3]);
		$imageWidth = imagesx($image);
		$imageHeight = imagesy($image);

		return [
			'x' => ($imageWidth - $textWidth) / 2,
			'y' => ($imageHeight + $textHeight) / 2,
		];
	}

	/**
	 * For converting UTF-8 to hex
	 *
	 * @param string $num
	 *
	 * @return string
	 */
	function code2utf8(string $num): string {
		$num = hexdec($num);
		$result = '';

		if ($num < 128) {
			$result = chr($num);
		}
		else if ($num < 2048) {
			$result = chr(($num >> 6) + 192) .
						chr(($num & 63) + 128);
		}
		else if ($num < 65536) {
			$result = chr(($num >> 12) + 224) .
						chr((($num >> 6) & 63) + 128) .
						chr(($num & 63) + 128);
		}
		else if ($num < 2097152) {
			$result = chr(($num >> 18) + 240) .
						chr((($num >> 12) & 63) + 128) .
						chr((($num >> 6) & 63) + 128) .
						chr(($num & 63) + 128);
		}

		return $result;
	}

	/**
	 * @param        $image
	 * @param string $color
	 *
	 * @return mixed
	 */
	public function paintTransparentBgImage($image, string $color) {
		$backgroundColor = $this->hexToRgb($color);
		$backgroundColor = imagecolorallocatealpha(
			$image,
			$backgroundColor['red'],
			$backgroundColor['green'],
			$backgroundColor['blue'],
			127
		);

		imagefill($image, 0, 0, $backgroundColor);

		return $image;
	}

	/**
	 * @param        $image
	 * @param string $color
	 *
	 * @return mixed
	 */
	public function paintLayerImage($image, string $color): mixed {
		$backgroundColor = $this->hexToRgb($color);
		$backgroundColor = imagecolorallocatealpha(
			$image,
			$backgroundColor['red'],
			$backgroundColor['green'],
			$backgroundColor['blue'],
			0
		);

		imagefill($image, 0, 0, $backgroundColor);

		return $image;
	}

	/**
	 * Render an image with given width, height and colours.
	 *
	 * @param int         $width   - set image width in pixels
	 * @param int         $height  - set image height in pixels
	 * @param string|null $text    - set text
	 * @param string      $bgcolor - set background color, accepted is hex string
	 * @param string      $color   - set text color, accepted is hex string
	 *
	 * @return resource
	 */
	public function create(int $width, int $height, string $text = null, string $bgcolor = '#cdcdcd', string $color = '#fafafa'): \GdImage {
		$image = imagecreatetruecolor($width, $height);
		$image = $this->paintImage($image, $bgcolor);
		$color = $this->paintImageText($image, $color);

		// Check whether there is globally set font size or not.
		$fontSize = $this->fontSize ?? $this->setFontSize($width, $height);

		return $this->typeOnImage($image, $fontSize, $color, $text);
	}

	public function setFontSize(int $width, int $height): int {
		return $width > $height ? $height / 8 : $width / 8;
	}

	/**
	 * Output image in browser
	 *
	 * @param null      $image
	 * @param bool|null $base64
	 *
	 * @return Application|ResponseFactory|Response
	 */
	public function output($image = null, ?bool $base64 = false): Response|Application|ResponseFactory {
		// Output in browser
		ob_start();
		imagepng($image);
		$contents = ob_get_clean();
		// Free memory
		imagedestroy($image);

		return response($base64 ? base64_encode($contents) : $contents)->withHeaders(
			[
				"Cache-Control" => "no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0",
				"Pragma"        => "no-cache",
				'Content-Type'  => $base64 ? 'text/html; charset=UTF-8' : 'image/png',
			]
		);
	}

	/**
	 * @param             $image
	 * @param string|null $filename
	 *
	 * @return Response|Application|ResponseFactory
	 */
	public function forceDownload(\GdImage $image, ?string $filename = null): Response|Application|ResponseFactory {
		ob_start();
		imagepng($image, $filename);
		$contents = ob_get_clean();
		// Free memory
		imagedestroy($image);

		return response($contents)->withHeaders(
			[
				'Content-Type' => 'image/png',
				'Content-disposition',
				'attachment; filename=' . $filename // RFC2183: http://www.ietf.org/rfc/rfc2183.txt
			]
		);
	}
}
