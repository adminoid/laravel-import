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

                $sizes = explode('x', $size);

                $w = $sizes[0];
                $h = $sizes[1];

                //echo "$w x $h";

                $url = parse_url($image->path);

                $filename = basename($image->path);
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                $fName = basename($filename, ".jpg");

                $filePath = 'public' . $url['path'];

                $fileDir = base_path() . '/' . dirname($filePath);

                if (!file_exists("$fileDir/thumbs")) {
                    mkdir("$fileDir/thumbs", 0777, true);
                }

                $thumbFile = "$fileDir/thumbs/$fName-{$w}x$h.$ext";


                var_dump($filePath);
                var_dump($thumbFile);

                $this->comment($image->product()->select('id')->first()->id);

                /*if(!file_exists($thumbFile)){

                    GDImage::make($filePath)->resize($w,$h)->save($thumbFile);
                }else{
                    $this->info("Файл $thumbFile уже существует");
                }*/

                $GDImage = GDImage::make($filePath);

                /*$callback = function ($constraint) { $constraint->upsize(); };
                $GDImage->widen($w, $callback)->heighten($h, $callback);*/

                $GDImage->resize($w, $h, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->resizeCanvas($w, $h)->save($thumbFile);

                /*$thumb = new \Thumb(array(
                    'pixel_size' => "{$w}x$h",
                    'path' => $thumbFile
                ));

                $image->thumbs()->save($thumb);*/


            }

            //die;

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
