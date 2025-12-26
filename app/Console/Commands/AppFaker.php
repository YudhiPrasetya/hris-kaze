<?php

namespace App\Console\Commands;

use Faker\Factory;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;


class AppFaker extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:faker:person {gender}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Fake it!';

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle() {
		$self = $this;
		$locales = [
			"ar_JO",
			"ar_SA",
			"at_AT",
			"bg_BG",
			"bn_BD",
			"cs_CZ",
			"da_DK",
			"de_AT",
			"de_CH",
			"de_DE",
			"el_CY",
			"el_GR",
			"en_AU",
			"en_CA",
			"en_GB",
			"en_HK",
			"en_IN",
			"en_NG",
			"en_NZ",
			"en_PH",
			"en_SG",
			"en_UG",
			"en_US",
			"en_ZA",
			"es_AR",
			"es_ES",
			"es_PE",
			"es_VE",
			"et_EE",
			"fa_IR",
			"fi_FI",
			"fr_BE",
			"fr_CA",
			"fr_CH",
			"fr_FR",
			"he_IL",
			"hr_HR",
			"hu_HU",
			"hy_AM",
			"id_ID",
			"is_IS",
			"it_CH",
			"it_IT",
			"ja_JP",
			"ka_GE",
			"kk_KZ",
			"ko_KR",
			"lt_LT",
			"lv_LV",
			"me_ME",
			"mn_MN",
			"ms_MY",
			"nb_NO",
			"ne_NP",
			"nl_BE",
			"nl_NL",
			"pl_PL",
			"pt_BR",
			"pt_PT",
			"ro_MD",
			"ro_RO",
			"ru_RU",
			"sk_SK",
			"sl_SI",
			"sr_Cyrl_RS",
			"sr_Latn_RS",
			"sr_RS",
			"sv_SE",
			"th_TH",
			"tr_TR",
			"uk_UA",
			"vi_VN",
			"zh_CN",
			"zh_TW",
		];
		$failed = false;

		while (!$failed) {
			try {
				$rand = $locales[rand(0, count($locales))];
				$faker = Factory::create($rand);
				$person = collect([
					'locale'            => $rand,
					'company'           => $faker->company,
					'companyEmail'      => $faker->companyEmail,
					'name'              => $faker->name($this->argument('gender')),
					'gender'            => Str::ucfirst($this->argument('gender')),
					'email'             => $faker->email,
					'country'           => $faker->country,
					'state'             => $faker->state ?? '',
					'city'              => $faker->city,
					'postcode'          => $faker->postcode,
					'street'            => $faker->streetAddress,
					'numberBetween'     => $faker->numberBetween(),
					'uuid'              => $faker->uuid,
					'creditCardDetails' => collect($faker->creditCardDetails)->join(', '),
				]);
				$failed = true;
			}
			catch (\Exception $e) {

			}
		}

		$person->each(function ($value, $key) use ($self) {
			$self->info(Str::ucfirst($key) . ": $value");
		});

		return 0;
	}

	protected function getArguments() {
		return array(
			array('gender', InputArgument::REQUIRED, 'A person gender.'),
		);
	}
}
