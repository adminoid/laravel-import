<?php namespace Petja\Import\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Petja\Import\Fixes\Fixes;

class FixesRun extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'fixes:run';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Quick fixes.';

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
		$fix = new Fixes($this);
        $fix->run($this->argument('name'));
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('name', InputArgument::REQUIRED, 'Name of execute method'),
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
