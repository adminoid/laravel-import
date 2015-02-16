<?php namespace Petja\Import\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use \GDImage;

class ThumbGen extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'thumb:gen';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate thumbnails.';

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
		$this->line('start...');

        $sizes = json_encode(['270*220','95*70','170*170']);


        $images = \Image::all();
        foreach ($images as $image) {

            foreach (json_decode($image->pixel_sizes) as $size) {

                $sizes = explode('*', $size);

                $w = $sizes[0];
                $h = $sizes[1];

                echo "$w x $h";
                echo "\n";
                echo $image->path;
                echo "\n";

                $url = parse_url($image->path);
                $filePath = 'public' . $url['path'];

                echo "$filePath\n";

                GDImage::make($filePath)->resize('200','200')->save($filePath . '.200x200');



                //$img = GDImage::make($filePath);


                /*$img->resize($w, $h);
                //$img->insert('public/watermark.png');
                $img->save('public/bar.jpg');*/

            }

            die;


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
			array('example', InputArgument::OPTIONAL, 'An example argument.'),
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
