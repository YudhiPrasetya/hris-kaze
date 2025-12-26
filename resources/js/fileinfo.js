/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   fileinfo.js
 * @date   17/08/2020 03.46
 */

import fs from 'fs';
import mime from 'mime';
import path from 'path';


export default class FileInfo {
	stats = null;

	path = null;

	name = null;

	basename = null;

	ext = null;

	mimeType = null;

	constructor(file) {
		if (fs.existsSync(file)) {
			this.path = file;
			this.ext = path.extname(file);
			this.stats = fs.statSync(file);
			this.name = path.basename(file);
			this.mimeType = mime.getType(file);
		}
	}
}