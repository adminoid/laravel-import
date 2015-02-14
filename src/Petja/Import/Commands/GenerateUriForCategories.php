<?php namespace Petja\Import\Commands;

/**
 * Обновить uri у товаров: "php artisan uri:cats products"
 */

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Mascame\Urlify;

class GenerateUriForCategories extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'uri:cats';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate categories uri';

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
		$this->comment('запуск');

		switch($this->argument('who')){
			case 'cats':

				$allCats = \Category::all();
				foreach ($allCats as $cat) {

					$uri = Urlify::filter($cat->name);

					$cat->uri = $uri;
					if($cat->save()){
						$this->line("сохранено: $uri");
					}

				}

				break;
			case 'products':

				$allProducts = \Product::all();

				foreach ($allProducts as $product) {

					$name = $product->name;
					$id = $product->id;

					//if($id == 65){

						$subName = trim(mb_strtolower(mb_substr($name, 0, 17)));
						$this->comment("$subName");

						$this->info("$subName");


						$uri = Urlify::filter($subName) . '-' . $product->id;

						$product->uri = $uri;
						if($product->save()){

							$this->line("$uri: $name");

						}else{
							$this->error('ошибка q123367');
						}
					//}

				}


				break;
			default:
				$this->error('Что парсим-то?');
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
			array('who', InputArgument::REQUIRED, 'кого парсим'),
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
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
