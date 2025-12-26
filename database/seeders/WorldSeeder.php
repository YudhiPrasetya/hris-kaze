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
 * @file   WorldSeeder.php
 * @date   2020-10-29 5:31:14
 */

namespace Database\Seeders;

use App\Helpers\SeederBase;
use App\Models\World\Continent;
use App\Models\World\Country;
use App\Models\World\Currency;
use App\Models\World\Language;
use App\Models\World\Region;
use App\Models\World\TimezoneAbbreviation;
use App\Models\World\Zone;
use App\Repositories\Eloquent\World\CityRepository;
use App\Repositories\Eloquent\World\ContinentRepository;
use App\Repositories\Eloquent\World\CountryRepository;
use App\Repositories\Eloquent\World\CurrencyRepository;
use App\Repositories\Eloquent\World\DistrictRepository;
use App\Repositories\Eloquent\World\RegionRepository;
use App\Repositories\Eloquent\World\StateRepository;
use App\Repositories\Eloquent\World\TimezoneAbbreviationRepository;
use App\Repositories\Eloquent\World\ZoneRepository;
use Illuminate\Support\Facades\DB;


class WorldSeeder extends SeederBase {
	const MAX_FLUSH = 1000;

	private ContinentRepository $continentRepository;

	private RegionRepository $regionRepository;

	private CurrencyRepository $currencyRepository;

	private CountryRepository $countryRepository;

	private ZoneRepository $zoneRepository;

	private TimezoneAbbreviationRepository $abbreviationRepository;

	private StateRepository $stateRepository;

	private CityRepository $cityRepository;

	private DistrictRepository $districtRepository;

	public function __construct(ContinentRepository $continentRepository, RegionRepository $regionRepository, CurrencyRepository $currencyRepository,
		CountryRepository $countryRepository, ZoneRepository $zoneRepository, TimezoneAbbreviationRepository $abbreviationRepository,
		StateRepository $stateRepository, CityRepository $cityRepository, DistrictRepository $districtRepository
	) {
		parent::__construct();
		$this->continentRepository = $continentRepository;
		$this->regionRepository = $regionRepository;
		$this->currencyRepository = $currencyRepository;
		$this->countryRepository = $countryRepository;
		$this->zoneRepository = $zoneRepository;
		$this->abbreviationRepository = $abbreviationRepository;
		$this->stateRepository = $stateRepository;
		$this->cityRepository = $cityRepository;
		$this->districtRepository = $districtRepository;
	}

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run(): void {
		$this->call(CurrenciesSeeder::class);
		$this->call(ContinentsSeeder::class);
		$this->call(RegionsSeeder::class, false, [$this->continentRepository]);
		$this->call(CountriesSeeder::class, false, [$this->regionRepository, $this->currencyRepository]);
		$this->call(LanguagesSeeder::class, false, [$this->countryRepository]);
		$this->call(StatesSeeder::class, false, [$this->countryRepository]);
		$this->call(CitiesSeeder::class, false, [$this->countryRepository, $this->stateRepository]);
		$this->call(DistrictsSeeder::class, false, [$this->stateRepository, $this->cityRepository]);
		$this->call(VillagesSeeder::class, false, [$this->stateRepository, $this->cityRepository, $this->districtRepository]);
		$this->call(TimezoneAbbreviationsSeeder::class);
		$this->call(ZonesSeeder::class, false, [$this->countryRepository]);
		$this->call(TimezonesSeeder::class, false, [$this->abbreviationRepository, $this->zoneRepository]);
	}
}
