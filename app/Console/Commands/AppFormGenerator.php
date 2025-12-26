<?php

namespace App\Console\Commands;

use App\Managers\Form\Field;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ReflectionClass;


class AppFormGenerator {
	private const BUILTIN_TYPES_MAP = [
		Types::ARRAY                => 'Field::TEXTAREA',
		Types::ASCII_STRING         => 'Field::TEXT',
		Types::BIGINT               => 'Field::TEXT',
		Types::BINARY               => 'Field::TEXT',
		Types::BLOB                 => 'Field::TEXTAREA',
		Types::BOOLEAN              => 'Field::SWITCH',
		Types::DATE_MUTABLE         => 'Field::DATE',
		Types::DATE_IMMUTABLE       => 'Field::DATE',
		Types::DATEINTERVAL         => 'Field::DATE',
		Types::DATETIME_MUTABLE     => 'Field::DATE',
		Types::DATETIME_IMMUTABLE   => 'Field::DATE',
		Types::DATETIMETZ_MUTABLE   => 'Field::DATE',
		Types::DATETIMETZ_IMMUTABLE => 'Field::DATE',
		Types::DECIMAL              => 'Field::TEXT',
		Types::FLOAT                => 'Field::TEXT',
		Types::GUID                 => 'Field::TEXT',
		Types::INTEGER              => 'Field::TEXT',
		Types::JSON                 => 'Field::TEXTAREA',
		Types::OBJECT               => 'Field::TEXTAREA',
		Types::SIMPLE_ARRAY         => 'Field::TEXTAREA',
		Types::SMALLINT             => 'Field::TEXT',
		Types::STRING               => 'Field::TEXT',
		Types::TEXT                 => 'Field::TEXT',
		Types::TIME_MUTABLE         => 'Field::TIME',
		Types::TIME_IMMUTABLE       => 'Field::TIME',
	];

	private ?Table $table = null;

	/**
	 * @var ForeignKeyConstraint[]
	 */
	private Collection $foreignKeys;

	private Collection $imports;

	public function __construct() {
		$this->imports = collect([]);
	}

	/**
	 * Get fields from options and create add methods from it.
	 *
	 * @param string|null $fields
	 * @param string|null $tableName
	 *
	 * @return string
	 * @throws \App\Exceptions\SchemaNotFoundException
	 * @throws \Doctrine\DBAL\Exception
	 */
	public function getFieldsVariable(string $fields = null, string $tableName = null): string {
		$table = null;
		$conn = DB::connection(connection('master'))
		          ->getSchemaBuilder()
		          ->getConnection()
		          ->getDoctrineConnection();

		if (!empty($tableName)) {
			$tables = collect($conn->getSchemaManager()->listTables())
				->mapToDictionary(function (Table $table, $key) {
					return [$table->getName() => $table];
				});

			/**
			 * @var $table Table
			 */
			$this->table = $tables->offsetGet($tableName)[0];
			$this->foreignKeys = collect($this->table->getForeignKeys());

			if (empty($fields)) {
				$fields = collect($this->table->getColumns())->mapToDictionary(function (Column $column, $key) {
					return [$column->getName()];
				});
				$fields = implode(',', $fields->get(0));
			}
		}

		if (!empty($fields)) {
			return $this->parseFields($fields);
		}

		return '// Add fields here...';
	}

	/**
	 * Parse fields from string.
	 *
	 * @param string $fields
	 *
	 * @return string
	 */
	protected function parseFields(string $fields): string {
		$fieldsArray = explode(',', $fields);
		$text = '$this' . "\n";

		foreach ($fieldsArray as $field) {
			$text .= $this->prepareAdd($field, end($fieldsArray) == $field);
		}

		$text .= <<<DOC
	        ->add('submit',
		        Field::BUTTON_SUBMIT,
		        [
			        'label' => '<i class="fad fa-save mr-1"></i> Submit',
			        'attr'  => ['class' => 'btn-falcon-danger'],
		        ])
DOC;

		return $text . ';';
	}

	/**
	 * Prepare template for single add field.
	 *
	 * @param string $field
	 * @param bool   $isLast
	 *
	 * @return string
	 */
	protected function prepareAdd($field, $isLast = false) {
		$field = Str::contains($field, ':') ? $field : "$field:";
		$field = trim($field);
		$fields = (new ReflectionClass(Field::class))->getConstants();

		$textArr = [];

		list($name, $type) = explode(':', $field);
		$isValue = in_array($type, $fields);
		$isKey = array_key_exists($type, $fields);

		$textArr = [
			sprintf("            ->add('%s'", $name),
		];

		if ($isValue || $isKey) {
			$key = $isValue ? array_search($type, $fields) : $type;
			$textArr[] = ", Field::$key";
		}
		else {
			$column = $this->table->getColumn($name);
			$foreignKey = $this->foreignKeys->filter(function (ForeignKeyConstraint $key) use ($name) {
				return collect($key->getColumns())->search($name) !== false;
			});

			if ($foreignKey->count()) {
				$foreign = $foreignKey->first();
				list($class, $fullname) = $this->getModelClassname($foreign->getForeignTableName());
				$this->imports->add("use $fullname;");
				$type = $foreign->getLocalTable()->getColumn($name)->getType();
				$textArr[] = sprintf(", Field::ENTITY, ['class' => %s::class,]", $class);
			}
			else {
				$type = $this->table->getColumn($name)->getType();
				$textArr[] = sprintf(", %s", self::BUILTIN_TYPES_MAP[$type->getName()]);
			}
		}

		$textArr[] = ")";
		$textArr[] = ($isLast) ? "" : "\n";

		return join('', $textArr);
	}

	public function getImports(): Collection {
		return $this->imports;
	}

	private function getModelClassname(string $tableName): ?array {
		$className = 'App\\Models\\' . Str::studly(Str::singular($tableName));

		if (class_exists($className)) {
			return [Str::studly(Str::singular($tableName)), $className];
		}

		return null;
	}

	public function getTableFieldsVariable(string $table = null) {
		if ($table) {
			$fields = '';

			return $this->parseFields($fields);
		}

		return '// Add fields here...';
	}

	/**
	 * @param string $name
	 *
	 * @return object
	 */
	public function getClassInfo($name): object {
		$explodedClassNamespace = explode('\\', $name);
		$className = array_pop($explodedClassNamespace);
		$fullNamespace = join('\\', $explodedClassNamespace);

		return (object)[
			'namespace' => $fullNamespace,
			'className' => $className,
		];
	}
}
