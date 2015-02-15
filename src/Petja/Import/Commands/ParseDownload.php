<?php namespace Petja\Import\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ParseDownload extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'parse:dl';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Downloading files from csmedica.';

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
	 * @return mixed
	 */
	public function fire()
	{
        if(php_sapi_name() != 'cli') return 'Скрипт запускать только из командной строки';

        $who = $this->argument('who');

        var_dump($who);

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
			array('who', InputArgument::REQUIRED, 'Who is being downloaded???'),
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
			//array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
