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
 * @file   ImagePlaceholderController.php
 * @date   2021-07-8 14:0:45
 */

namespace App\Http\Controllers;

use App\Libraries\Placeholder\ImagePlaceholder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class ImagePlaceholderController extends Controller {
	public function __construct() {
		// $this->middleware('placeholder');
	}

	public function placeholder(Request $request, $size = null, string $bgColor = null, string $textColor = null, string $ext = null) {
		$size = explode('x', $size);
		$bgColor = (Str::startsWith($bgColor, '#') ? '' : '#') . $bgColor;
		$textColor = (Str::startsWith($bgColor, '#') ? '' : '#') . $textColor;

		/*
		$placeholder = new PlaceholderImage(
			[
				'text'      => $request->get('text'),
				'icon'      => $request->get('icon'),
				'variant'   => $request->get('variant'),
				'width'     => $size[0],
				'height'    => count($size) == 2 ? $size[1] : $size[0],
				'bgColor'   => $bgColor,
				'textColor' => $textColor,
				//'quality'   => $quality,
				'ext'       => $ext,
				'fontFile'  => 'Roboto.ttf',
			]
		);
		*/
		$placeholder = new ImagePlaceholder("Roboto.ttf");
		$image = $placeholder->create(
			$size[0],
			count($size) == 2 ? $size[1] : $size[0],
			$request->has('notext') ? ' ' : $request->get('text'),
			$bgColor,
			$textColor
		);

		if ($request->get('forceDownload'))
			return $placeholder->forceDownload($image);
		else
			return $placeholder->output($image);
	}

	public function icon(Request $request, $size = null, string $bgColor = null, string $textColor = null, string $ext = null) {
		$size = explode('x', $size);
		$bgColor = (Str::startsWith($bgColor, '#') ? '' : '#') . $bgColor;
		$textColor = (Str::startsWith($bgColor, '#') ? '' : '#') . $textColor;

		$placeholder = new ImagePlaceholder($request->get('name', null), $request->get('type', 'regular'));
		$image = $placeholder->createIcon(
			$size[0],
			count($size) == 2 ? $size[1] : $size[0],
			$request->get('code'),
			$bgColor,
			$textColor
		);

		if ($request->get('forceDownload'))
			return $placeholder->forceDownload($image);
		else
			return $placeholder->output($image);
	}
}
