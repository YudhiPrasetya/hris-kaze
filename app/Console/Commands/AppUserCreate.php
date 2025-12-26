<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Faker\Factory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Question\Question;


class AppUserCreate extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:user:create';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create user';

	/**
	 * @var UserRepositoryInterface
	 */
	protected $repository;

	protected $alay = [
		'a' => '@4',
		'b' => '8',
		'e' => '3',
		'g' => '6',
		'i' => '1!',
		'o' => '0',
		'q' => '9',
		's' => '5',
		't' => '7'
	];

	/**
	 * AppUserCreate constructor.
	 *
	 * @param \App\Repositories\UserRepositoryInterface $repository
	 */
	public function __construct(UserRepositoryInterface $repository) {
		parent::__construct();

		$this->repository = $repository;
	}

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle() {
		$faker = Factory::create();
		$name = $this->ask("You name?", $faker->name());

		$username = $this->ask("Desired username?", $faker->userName);
		while (!empty($this->repository->findOneBy(['username' => $username]))) {
			$this->error("$username already used. Please choose another username.");
			$username = $this->ask("Desired username?", $faker->userName);
		}

		$email = $this->ask("Email address?");
		$_roles = [
			'ROLE_USER',
			'ROLE_SUPER_ADMIN',
			'ROLE_ADMIN',
			'ROLE_MERCHANT',
			'ROLE_OPERATOR',
			'ROLE_TECHNICIAN',
			'ROLE_SALES'
		];
		$roles = ['ROLE_USER'];

		while (($role = $this->askWithCompletion("Role?", $_roles, null))) {
			if (empty($role)) break;
			$roles[] = $role;
		}

		while (!$this->checkPasswordStrength(($password = $faker->password()))) ;

		$this->info('Adding user ' . $username);
		$names = explode(' ', $name);
		$user = new User();
		$user->newInstance(
			[
				'username'    => $username,
				'first_name'  => $names[0],
				'middle_name' => $names[1],
				'last_name'   => $names[2],
				'email'       => $email,
				'password'    => Hash::make($password),
				'roles'       => $roles,
				'config'      => '{"theme":"falcon"}'
			]
		)->save();

		return 0;
	}

	/**
	 * @param string|null $password
	 *
	 * @return bool
	 */
	private function checkPasswordStrength(?string $password): bool {
		if (empty($password)) return false;

		// the password must be at least six characters
		if (strlen($password) < 6) {
			$this->error("The password is too short.");

			return false;
		}

		// count how many lowercase, uppercase, and digits are in the password
		$uc = 0;
		$lc = 0;
		$num = 0;
		$punct = 0;
		$other = 0;

		for ($i = 0, $j = strlen($password); $i < $j; $i++) {
			$c = substr($password, $i, 1);

			if (preg_match('/^[[:upper:]]$/', $c)) $uc++;
			elseif (preg_match('/^[[:lower:]]$/', $c)) $lc++;
			elseif (preg_match('/^[[:digit:]]$/', $c)) $num++;
			elseif (preg_match('/^[[:punct:]]$/', $c)) $punct++;
			else $other++;
		}

		// the password must have more than two characters of at least
		// two different kinds
		$max = $j - 2;

		if ($uc > $max) {
			$this->error("The password has too many upper case characters.");

			return false;
		}
		if ($lc > $max) {
			$this->error("The password has too many lower case characters.");

			return false;
		}
		if ($num > $max) {
			$this->error("The password has too many numeral characters.");

			return false;
		}
		if ($punct > $max) {
			$this->error("The password has too many punctuation characters.");

			return false;
		}
		if ($other > $max) {
			$this->error("The password has too many special characters.");

			return false;
		}

		return true;
	}

	public function secret($question, $fallback = true, $default = null) {
		$question = new Question($question, $default);
		$question->setHidden(true)->setHiddenFallback($fallback);

		return $this->output->askQuestion($question);
	}

	private function generateStrongPassword($length = 10, $add_dashes = false, $available_sets = 'luds') {
		$sets = array();
		if (strpos($available_sets, 'l') !== false)
			$sets[] = 'abcdefghjkmnpqrstuvwxyz';
		if (strpos($available_sets, 'u') !== false)
			$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
		if (strpos($available_sets, 'd') !== false)
			$sets[] = '1234567890';
		if (strpos($available_sets, 's') !== false)
			$sets[] = '!@#$%&*?_-+=(){}[]:;<>';

		$all = '';
		$password = '';

		foreach ($sets as $set) {
			$password .= $set[array_rand(str_split($set))];
			$all .= $set;
		}

		$all = str_split($all);
		for ($i = 0; $i < $length - count($sets); $i++)
			$password .= $all[array_rand($all)];

		$password = str_shuffle($password);

		if (!$add_dashes)
			return $password;

		$dash_len = floor(sqrt($length));
		$dash_str = '';

		while (strlen($password) > $dash_len) {
			$dash_str .= substr($password, 0, $dash_len) . '-';
			$password = substr($password, $dash_len);
		}

		$dash_str .= $password;

		return $dash_str;
	}
}
