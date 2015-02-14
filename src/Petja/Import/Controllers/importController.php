<?php namespace Petja\Import\Controllers;

use Petja\Import\Categories\ImportCategories;
use Petja\Import\Products\ImportSphygmomanometers;

class ImportController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /import
	 *
	 * @return Response
	 */
	public function index()
	{
		echo "Не выбран класс!";
	}

	/**
	 * @param $path
	 */
	public function import($path)
	{
		//echo "Что импортируем: " . $path;

		die('disa-bled-23234');

		switch($path){
			case 'categories':
				$this->importCategories();
				break;
			case 'categories-test':
				ImportCategories::test();

				break;
			case 'sphygmomanometers':
				//$import = new ImportSphygmomanometers;
				$import->import();

				break;
			default:
				echo "Импорт <b>{$path}</b> не предусмотрен!";
		}

	}



}