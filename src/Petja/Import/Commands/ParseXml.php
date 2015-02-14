<?php namespace Petja\Import\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Petja\Import\Products\ImportXml;
use Petja\Import\Products\Test;

/**
 * Class ParseXml
 * Запуск: php artisan parse:xml 'import/xmls/sphygmomanometers-ch.xml'
 *
 * @package Petja\Import\Commands
 */

class ParseXml extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'parse:xml';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Parse XML files by name';

	/**
	 * Create a new command instance.
	 *
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return bool|string
	 */
	public function fire()
	{
		if(php_sapi_name() != 'cli') return 'Скрипт запускать только из командной строки';

		if($test = $this->option('test'))
		{
			$this->comment('тестируем...');

			$test = new Test($test, $this);

			return true;
		}
		elseif($xmlFile = $this->argument('filename'))
		{

			/*$this->error('парсинг пока выключен');
			die;*/

			$this->comment('парсим...');

			$route = new ImportXml();
			$route->route($xmlFile, $this)->run();
			return true;
		}

		$this->error('Нет аргументов, нет опций');

		return false;


	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('filename', InputArgument::OPTIONAL, 'sphygmomanometers-ch.xml'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('test', null, InputOption::VALUE_REQUIRED, 'тестируемый объект', null),
		);
	}

}
