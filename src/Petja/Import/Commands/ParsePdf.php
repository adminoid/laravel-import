<?php namespace Petja\Import\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Petja\Import\Products\ParsePdfFromContent;

class ParsePdf extends Command {



	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'parse:pdf';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Parsing pdf poverka';

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

		/*$this->error('Уже спарсил - пока отключено');
		die;*/

		$this->info('Старт...');

		//$import = new ImportSphygmomanometers($this);
		$import = new ParsePdfFromContent($this);

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

				$response = $import->processOneItem($item);

				switch($response){
					case 'not_array':
						$this->error('preg_replace_callback не нашел ссылок на pdf в content');
						break;
					default:
						$this->info('Good');
				}

			}

			if($ret = $import->saveNewXml()){
				$this->info("новый файл: {$ret} создан");
				$this->info("выполнение завершено!");
			}else{
				$this->error('Новый файл почему-то не создался...');
			}
		}

		//echo 'ds ' . \Config::get('import::test.value') . PHP_EOL;

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			//array('example33', InputArgument::OPTIONAL, 'An example argument.'),
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
			//array('example22', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

	/**
	 * расцветку новую генерю
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 *
	 * @return void
	 */
	public function run( InputInterface $input, OutputInterface $output )
	{
		// Set extra colors.
		// The most problem is $output->getFormatter() don't work...
		// So create new formatter to add extra color.

		$formatter = new OutputFormatter( $output->isDecorated() );
		$formatter->setStyle( 'red', new OutputFormatterStyle( 'red', 'black' ) );
		$formatter->setStyle( 'green', new OutputFormatterStyle( 'green', 'black' ) );
		$formatter->setStyle( 'yellow', new OutputFormatterStyle( 'yellow', 'black' ) );
		$formatter->setStyle( 'blue', new OutputFormatterStyle( 'blue', 'black' ) );
		$formatter->setStyle( 'magenta', new OutputFormatterStyle( 'magenta', 'black' ) );
		$formatter->setStyle( 'yellow-blue', new OutputFormatterStyle( 'yellow', 'blue' ) );
		$output->setFormatter( $formatter );

		parent::run( $input, $output );
	}

}
