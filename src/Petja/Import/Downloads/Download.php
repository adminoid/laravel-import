<?php namespace Petja\Import\Downloads;
//use Intervention\Image\Facades\Image;
use Jyggen\Curl\Request;

/**
 * Created by PhpStorm.
 * User: petja
 * Date: 15/02/15
 * Time: 12:54
 */

class Download {

    public $who;

    public function __construct($cmd, $who)
    {

        $this->cmd = $cmd;

        if($who == 'image'){
            $this->who = 'image';
        }

    }

    public function run()
    {
        if($this->who == 'image'){
            $images = \Image::all();
            foreach ($images as $image) {
                $to = $this->makeToPath($image);
                $toFullPath = public_path() . '/' . $to;
                if($this->imageDownload($image->src, $toFullPath)){
                    $this->cmd->info("Путь в БД: http://ikmed.ru/$to");
                    $image->path = "http://ikmed.ru/$to";
                    $image->downloaded = true;
                    $image->save();
                }
            }
            $this->cmd->error('test778');
        }
    }

    public function imageDownload($from, $toFullPath)
    {

        $this->cmd->comment("Закачка: $from");

        $pathInfo = pathinfo($toFullPath);
        $dir = $pathInfo['dirname'];

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        if (file_exists($toFullPath)) {
            //unlink($toFullPath);
            $this->cmd->line("Файл $toFullPath уже существует");
            return true;
        }

        $this->cmd->line("Закачиваем в $toFullPath");

        if (!file_exists($toFullPath)) {
            $raw = file_get_contents($from);
            return file_put_contents($toFullPath, $raw);
        }

        return false;


    }

    public function makeToPath($image)
    {
        if($this->who == 'image'){

            $product = $image->product()->with('categories')->first();

            if(count($product->categories) != 1){
                die('ОПАСНОСТЕ!!!!!');
            }


            $test = $product->categories[0]->ancestorsAndSelf()->get();

            unset($test[0]);

            $to = 'images/products/';

            foreach ($test as $t) {

                $to .= "{$t->id}/";
            }

            $ext = pathinfo($image->src, PATHINFO_EXTENSION);

            $to .= "{$product->uri}-{$image->id}.$ext\n";

            return $to;

        }

        return false;
    }


}