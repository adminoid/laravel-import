<?php namespace Petja\Import;

use Illuminate\Support\ServiceProvider;
//use Illuminate\Foundation\AliasLoader;

use Illuminate\Console\Command;


class ImportServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	public function boot()
	{
		$this->package('petja/import');

		include __DIR__.'/../../routes.php';

		//AliasLoader::getInstance()->alias('TC', 'Petja\Import\TestClass');

		$this->app->bind('petja::command.parse.pdf', function() {
			//return new Commands\ParsePdfInstructions();
			return new Commands\ParsePdf();
		});

		$this->app->bind('petja::command.parse.xml', function() {
			return new Commands\ParseXml();
		});

		$this->app->bind('petja::command.uri.cats', function() {
			return new Commands\GenerateUriForCategories();
		});

        $this->app->bind('petja::command.parse.dl', function() {
            return new Commands\ParseDownload();
        });

        $this->app->bind('petja::command.fixes.run', function() {
            return new Commands\FixesRun();
        });

		$this->commands(array(
			'petja::command.parse.pdf',
			'petja::command.parse.xml',
			'petja::command.uri.cats',
			'petja::command.parse.dl',
			'petja::command.fixes.run',
		));
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
