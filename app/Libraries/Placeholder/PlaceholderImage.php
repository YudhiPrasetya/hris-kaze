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
 * @file   PlaceholderImage.php
 * @date   2021-07-8 14:2:39
 */

namespace App\Libraries\Placeholder;


/**
 * Class PlaceholderImage
 * Create image FPO (For Position Only)
 *
 * @package Libraries\Placeholder
 */
class PlaceholderImage {
	/**
	 * @var int|mixed
	 */
	private $width = 250;

	/**
	 * @var int|mixed
	 */
	private $height = 250;

	/**
	 * @var mixed|string
	 */
	private $bgColor = 'CCCCCC';

	/**
	 * @var mixed|string
	 */
	private $textColor = '';

	/**
	 * @var mixed|string
	 */
	private $text = null;

	/**
	 * @var mixed|string|null
	 */
	private $icon = null;

	/**
	 * @var mixed|string|null
	 */
	private $variant = 'solid';

	/**
	 * @var string
	 */
	private $fontFile = 'Arvo-Regular.ttf';

	/**
	 * @var resource
	 */
	private $image;

	/**
	 * PlaceholderImage constructor.
	 *
	 * @param array $config
	 */
	public function __construct(array $config) {
		$this->width = $config['width'] ?? $this->width;
		$this->height = $config['height'] ?? $this->height;
		$this->bgColor = $config['bgColor'] ?? $this->bgColor;
		$this->textColor = $config['textColor'] ?? $this->textColor;
		$this->text = $config['text'] ?? $this->text ?? config('app.short_name');
		$this->icon = $config['icon'] ?? $this->icon;
		$this->variant = $config['variant'] ?? $this->variant;
		$this->fontFile = $config['fontFile'] ?? $this->fontFile;

		$this->createPlaceholder();
	}

	/**
	 * Create image placeholder
	 */
	public function createPlaceholder() {
		// Configuration
		$w = intval($this->width);
		$h = intval($this->height);

		$bgColorRgb = $this->hex2rgb($this->bgColor);
		$textColorRgb = !empty($this->textColor) ? $this->hex2rgb($this->textColor) : false;

		$text = $this->text ?? ($w . "x" . $h);
		$bgPadding = 5;

		$fontFile = $this->fontFile ?: 'Arvo-Regular.ttf';
		$fontFile = base_path("libraries/Placeholder/fonts/" . $fontFile);
		$fontSize = $w > $h ? $h / 10 : $w / 10;

		$lineThickness = 1;

		// Create image
		$path = base_path(sprintf('resources/fonts/fontawesome/icons/%s/%s.svg', 'solid', 'alien'));
		$this->image = imagecreatetruecolor($w, $h);
		imageantialias($this->image, true);

		// Colors
		$bgColor = imagecolorallocate($this->image, $bgColorRgb[0], $bgColorRgb[1], $bgColorRgb[2]);
		$lineColor = imagecolorallocatealpha($this->image, 30, 30, 30, 100);
		$textColor = ($textColorRgb == false) ? imagecolorallocatealpha($this->image, 30, 30, 30, 60) :
			imagecolorallocate($this->image, $textColorRgb[0], $textColorRgb[1], $textColorRgb[2]);

		// Draw background
		imagefill($this->image, 0, 0, $bgColor);

		// Add cross
		// $this->imagelinethick($this->image, 0, 0, $w, $h, $lineColor, $lineThickness);
		// $this->imagelinethick($this->image, $w, 0, 0, $h, $lineColor, $lineThickness);

		// Write text
		list($x, $y, $textWidth, $textHeight) = $this->imageTTFCenter($this->image, $text, $fontFile, $fontSize);

		$bgx1 = $x - $bgPadding;
		$bgx2 = $bgx1 + $textWidth + ($bgPadding * 2);

		$bgy1 = $y - $textHeight - $bgPadding;
		$bgy2 = $bgy1 + $textHeight + ($bgPadding * 2);

		imagefilledrectangle($this->image, $bgx1, $bgy1, $bgx2, $bgy2, $bgColor); // Draw text background
		imagettftext($this->image, $fontSize, 0, $x, $y, $textColor, $fontFile, $text);

		// Generate filename
		$this->filename = ($w . "x" . $h) . '_' . substr(md5(uniqid(rand(), true)), 0, 5) . '.png';
	}

	/**
	 * Convert hexadecimal color '#abc123' or 'abc123' to RGB values
	 * support 3-chars hex colors '#aaa' or 'aaa'
	 *
	 * @param string Color in hexadecimal format
	 *
	 * @return array
	 */
	public function hex2rgb($color) {
		$color = str_replace("#", "", $color);

		if (strlen($color) == 3) {
			$r = hexdec(substr($color, 0, 1) . substr($color, 0, 1));
			$g = hexdec(substr($color, 1, 1) . substr($color, 1, 1));
			$b = hexdec(substr($color, 2, 1) . substr($color, 2, 1));
		}
		else {
			$r = hexdec(substr($color, 0, 2));
			$g = hexdec(substr($color, 2, 2));
			$b = hexdec(substr($color, 4, 2));
		}

		$rgb = array($r, $g, $b);

		return $rgb;
	}

	/**
	 * Return text block position (both horizontally/vertically centered)
	 *
	 * @param resource $image
	 * @param string   $text
	 * @param string   $font
	 * @param float    $size
	 *
	 * @return array
	 */
	public function imageTTFCenter($image, $text, $font, $size) {
		// Find the size of the image
		$imageWidth = imagesx($image);
		$imageHeight = imagesy($image);

		// Get the bounding box of the text
		$box = imagettfbbox($size, 0, $font, $text);

		// Calculate its dimensions
		$textWidth = abs($box[6]) + abs($box[4]);
		$textHeight = abs($box[7]) + abs($box[1]);

		// Compute centering
		$x = ($imageWidth - $textWidth) / 2;
		$y = ($imageHeight + $textHeight) / 2;

		//$y -= $textHeight; // Y-ordinate sets the position of the font baseline

		return array($x, $y, $textWidth, $textHeight);
	}

	/**
	 * Drawing a thick line
	 *
	 * @param     $image
	 * @param     $x1
	 * @param     $y1
	 * @param     $x2
	 * @param     $y2
	 * @param     $color
	 * @param int $thick
	 *
	 * @return bool
	 */
	public function imagelinethick($image, $x1, $y1, $x2, $y2, $color, $thick = 1) {
		$t = $thick / 2 - 0.5;
		$k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
		$a = $t / sqrt(1 + pow($k, 2));

		$points = array(
			round($x1 - (1 + $k) * $a),
			round($y1 + (1 - $k) * $a),
			round($x1 - (1 - $k) * $a),
			round($y1 - (1 + $k) * $a),
			round($x2 + (1 + $k) * $a),
			round($y2 - (1 - $k) * $a),
			round($x2 + (1 - $k) * $a),
			round($y2 + (1 + $k) * $a),
		);

		return imagefilledpolygon($image, $points, 4, $color);
	}

	/**
	 * Output image in browser
	 *
	 * @param null $filename
	 * @param bool $base64
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
	 */
	public function output($filename = null, bool $base64 = false) {
		// Output in browser
		ob_start();
		imagepng($this->image, $filename);
		$contents = ob_get_clean();
		// Free memory
		imagedestroy($this->image);

		return response($base64 ? base64_encode($contents) : $contents)->withHeaders(
			[
				'Content-Type' => $base64 ? 'text/html; charset=UTF-8' : 'image/png',
			]
		);
	}

	/**
	 * @param $filename string
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
	 */
	public function forceDownload($filename) {
		ob_start();
		imagepng($this->image, $filename);
		$contents = ob_get_clean();
		// Free memory
		imagedestroy($this->image);

		return response($contents)->withHeaders(
			[
				'Content-Type' => 'image/png',
				'Content-disposition',
				'attachment; filename=' . $filename // RFC2183: http://www.ietf.org/rfc/rfc2183.txt
			]
		);
	}
}
