<?php namespace Petja\Import\Commands;

use Illuminate\Console\Command;
use Petja\Import\Products\ImportSphygmomanometersInstructions;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
//use Illuminate\Support\Facades\Config;

class ParsePdfInstructions extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'parse:pdfi';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Парсим тег <pdf-instruction/>';

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
		// import/xmls/sphygmomanometers-ch.xml

		//$this->info('проверка...');

		$this->info('Временно отключено'); die;

		$filename = base_path() . '/import/xmls/' . $this->argument('filename');

		if(file_exists($filename)){
			$this->info('Старт...');

			$import = new ImportSphygmomanometersInstructions($this);

			$msg = $import->prepare();
			if($msg !== true)
			{
				$this->error($msg);
			}
			else
			{

				$this->info('xml файл загружен...');

				foreach($import->items as $item){

					$this->line("обрабатываю: {$item->title}");

					$pdfInstruction = (String) $item->{"pdf-instruction"};
					$pdfInstruction = trim($pdfInstruction);

					/*static $i = 0;
					if($i > 3) die('~');
					$i++;*/

					if(empty($pdfInstruction)){
						$this->line('Нет pdf инструкции, пропускаем...');
						continue;
					}else{
						$import->processOneItem($pdfInstruction);
					}


				}

				// todoParse1 Создать новый xml здесь

			}

		}else{
			$this->error('Такого файла нет: ' . $filename);
		}



	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('filename', InputArgument::REQUIRED, 'sphygmomanometers-ch.xml'),
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
			array('example22', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

	public function boot()
	{
		$this->package('petja/import');

		include __DIR__.'/../../routes.php';

		//AliasLoader::getInstance()->alias('TC', 'Petja\Import\TestClass');

		$this->app->bind('petja::command.parse.pdfi', function() {
			return new ParsePdfInstructions();
		});
		$this->commands(array(
			'petja::command.parse.pdf'
		));
	}
}
