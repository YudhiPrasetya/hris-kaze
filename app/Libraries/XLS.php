<?php
/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   XLS.php
 * @date   2020-10-30 17:11:51
 */

namespace App\Libraries;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use ReflectionClass;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


/**
 * Class XLS
 *
 * @package App\Helpers
 */
class XLS {
	/**
	 * IDR Format
	 */
	const FORMAT_CURRENCY_IDR_SIMPLE = '"RP"#,##0.00_-';

	/**
	 * @var XLS|null
	 */
	private static ?XLS $instance = null;

	/**
	 * @var Spreadsheet|null
	 */
	private ?Spreadsheet $spreedsheet = null;

	/**
	 * @var Worksheet|null
	 */
	private ?Worksheet $worksheet = null;

	/**
	 * @var =Collection
	 */
	private Collection $columns;

	/**
	 * @var int
	 */
	private int $row = 0;

	/**
	 * @var string
	 */
	private string $filename;

	/**
	 * XLS constructor.
	 *
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 */
	public function __construct() {
		$this->spreedsheet = new Spreadsheet();
		$this->setActiveSheetIndex();
		$uniq = uniq_string();
		$this->filename = "public/xls/{$uniq}.xls";
		$this->columns = collect([]);

		// From A..ZZ
		for ($i = 65; $i < (65 + 26); $i++) {
			$this->columns->put($i - 65, chr($i));

			for ($j = 65; $j < (65 + 26); $j++) {
				$this->columns->put(($i + $j) - 65, chr($i) . chr($j));
			}
		}
	}

	/**
	 * @param int $index
	 *
	 * @return $this
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 */
	public function setActiveSheetIndex(int $index = 0): self {
		$this->worksheet = $this->spreedsheet->setActiveSheetIndex($index);

		return $this;
	}

	/**
	 * @return static
	 */
	public static function getInstance(): self {
		if (!self::$instance instanceof XLS) {
			self::$instance = new XLS();
		}

		return self::$instance;
	}

	/**
	 * @param $name
	 *
	 * @return mixed
	 * @throws \ReflectionException
	 */
	public function __get($name) {
		$ref = new ReflectionClass($this->spreedsheet);
		if ($ref->hasProperty($name)) return $ref->getProperty($name)->getValue();

		return $this->$name;
	}

	/**
	 * @param $name
	 * @param $arguments
	 *
	 * @return mixed
	 * @throws \ReflectionException
	 */
	public function __call($name, $arguments) {
		$ref = new ReflectionClass($this->spreedsheet);
		if ($ref->hasMethod($name)) return $this->spreedsheet->$name(...$arguments);

		return $this->$name(...$arguments);
	}

	/**
	 * @param string $col
	 * @param int    $row
	 * @param array  $titles
	 *
	 * @return Collection
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 */
	public function setColHeaders(string $col, int $row, array $titles): Collection {
		$self = $this;

		return $this->addRow($titles,
			$col,
			$row,
			function ($value) use ($self) {
				return $self->prepareRow($value);
			});
	}

	/**
	 * @param array         $data
	 * @param string|null   $col
	 * @param int|null      $row
	 * @param callable|null $callback
	 *
	 * @return Collection
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 */
	public function addRow(array $data, ?string $col = 'A', ?int $row = null, ?callable $callback = null) {
		$self = $this;
		$cells = [];
		$col = $col ?? 'A';

		if (($index = $this->columns->indexOf($col))) {
			$col = $index;
		}
		else {
			$col = 0;
		}

		$row = $row ?? $this->getActiveRow();
		$this->setActiveRow($row);
		$sheet = $self->getActiveWorksheet();

		collect($data)->each(function ($value, $key) use ($self, $callback, $sheet, &$col, &$cells) {
			$value = is_callable($callback) ? $callback($value, $key) : $value;
			$coord = $self->columns->get($col) . $self->getActiveRow();
			$self->getActiveWorksheet()->getColumnDimension($self->columns->get($col))->setAutoSize(true);
			$sheet->getStyle($coord)
			      ->getNumberFormat()
			      ->setFormatCode($value['format'] ?? NumberFormat::FORMAT_TEXT);
			$sheet->setCellValue($coord, $value['value']);
			$cells[$coord] = $sheet->getCell($coord);
			$col++;
		});

		$this->getActiveWorksheet()->getRowDimension($row)->setRowHeight(15.84);
		$this->nextRow();

		return collect($cells);
	}

	public function getActiveRow(): int {
		return $this->row;
	}

	public function setActiveRow(int $row): self {
		$this->row = $row;

		return $this;
	}

	public function getActiveWorksheet(): ?Worksheet {
		return $this->worksheet;
	}

	public function nextRow(): self {
		$this->row++;

		return $this;
	}

	public function prepareRow($value, string $format = NumberFormat::FORMAT_TEXT) {
		return [
			'value'  => $value,
			'format' => $format,
		];
	}

	/**
	 * @param string $cell
	 * @param        $value
	 * @param array  $styles
	 *
	 * @return \PhpOffice\PhpSpreadsheet\Cell\Cell
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 */
	public function setCell(string $cell, $value, array $styles = []): Cell {
		$cell = $this->getCell($cell)->setValue($value);
		$this->setStyles($cell, $styles);

		return $cell;
	}

	/**
	 * @param string $cell
	 *
	 * @return \PhpOffice\PhpSpreadsheet\Cell\Cell
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 */
	public function getCell(string $cell): Cell {
		return $this->getActiveWorksheet()->getCell($cell);
	}

	public function setStyles(string $cell, array $styles = []): Style {
		return $this->getStyle($cell)->applyFromArray($styles);
	}

	/**
	 * @param string $cell
	 *
	 * @return \PhpOffice\PhpSpreadsheet\Style\Style
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 */
	public function getStyle(string $cell): Style {
		return $this->getCell($cell)->getStyle();
	}

	/**
	 * @param string $cell
	 * @param array  $fontStyle
	 *
	 * @return \PhpOffice\PhpSpreadsheet\Style\Style
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 */
	public function setFont(string $cell, array $fontStyle) {
		return $this->getStyle($cell)->applyFromArray([
			'font' => $fontStyle,
		]);
	}

	/**
	 * @param string $cell
	 * @param array  $alignment
	 *
	 * @return \PhpOffice\PhpSpreadsheet\Style\Style
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 */
	public function setAlignments(string $cell, array $alignment) {
		return $this->getStyle($cell)->applyFromArray([
			'alignment' => $alignment,
		]);
	}

	/**
	 * @param string $cell
	 * @param array  $style
	 *
	 * @return \PhpOffice\PhpSpreadsheet\Style\Style
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 */
	public function setBorders(string $cell, array $style) {
		return $this->getStyle($cell)->applyFromArray([
			'borders' => $style,
		]);
	}

	/**
	 * @param string $cell
	 * @param array  $style
	 *
	 * @return \PhpOffice\PhpSpreadsheet\Style\Style
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 */
	public function setFill(string $cell, array $style) {
		return $this->getStyle($cell)->applyFromArray([
			'fill' => $style,
		]);
	}

	/**
	 * @param string $coord
	 *
	 * @return \PhpOffice\PhpSpreadsheet\Cell\Cell
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 */
	public function mergeCells(string $coord): Cell {
		$coords = explode(':', $coord);
		$this->getActiveWorksheet()->mergeCells($coord);
		$part = Str::separate($coords[0], 1);
		$this->getActiveWorksheet()->getRowDimension($part[1])->setRowHeight(15.84);

		return $this->getCell($coords[0]);
	}

	/**
	 * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	public function download(): BinaryFileResponse {
		// $contentType = 'application/vnd.ms-excel';
		$date = DateTimeImmutable::createFromMutable(new DateTime());
		$date = $date->setTimezone(new DateTimeZone('UTC'));

		$this->save();
		$filename = storage_path("app/{$this->filename}");
		$title = $this->spreedsheet->getProperties()->getTitle();

		return response()->download(
			$filename,
			sprintf("%s - %s.xls", $title, (new DateTime())->format('Y-m-d')),
			[
				[
					'Content-Transfer-Encoding' => 'binary',
					'Cache-Control'             => 'max-age=0', // HTTP/1.1
					'Cache-Control'             => 'max-age=1', // HTTP/1.1
					'Cache-Control'             => 'cache, must-revalidate',
					'Pragma'                    => 'public',
					'Expires'                   => 'Sat, 12 Nov 1977 23:50:00 GMT', // Date in the past
					'Last-Modified'             => $date->format('D, d M Y H:i:s') . ' GMT',
				],
			])->deleteFileAfterSend(true);
	}

	/**
	 * @param string|null $filename
	 *
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	public function save(string $filename = null) {
		$filename = $filename ?? $this->filename;
		$localDisk = Storage::disk('local');

		if (!$localDisk->exists('public/xls')) $localDisk->makeDirectory('public/xls');
		$localDisk->put($filename, '');

		$writer = IOFactory::createWriter($this->spreedsheet, 'Xls');
		$writer->save(storage_path("app/{$filename}"));
	}

	public function setTitle(string $title) {
		$this->properties()->setTitle($title);

		return $this;
	}

	public function properties(): Properties {
		return $this->spreedsheet->getProperties();
	}

	public function setSubject(string $subject) {
		$this->properties()->setSubject($subject);

		return $this;
	}

	public function setDescription(string $description) {
		$this->properties()->setDescription($description);

		return $this;
	}
}