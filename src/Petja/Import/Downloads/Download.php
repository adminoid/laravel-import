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

        switch($who){
            case 'image':
                $this->who = 'image';
                break;
            case 'pdf':
                $this->who = 'pdf';
                break;
            case 'flv':
                $this->who = 'flv';
                break;
        }

    }

    public function run()
    {

        switch($this->who){
            case 'image':

                $images = \Image::all();
                foreach ($images as $image) {
                    $to = $this->makeToPath($image);
                    $toFullPath = public_path() . '/' . $to;
                    if($this->fileDownload($image->src, $toFullPath)){
                        $this->cmd->info("Путь в БД: http://ikmed.ru/$to");
                        $image->path = "http://ikmed.ru/$to";
                        $image->downloaded = true;
                        $image->save();
                    }
                }
                $this->cmd->error('test778');

                break;
            case 'pdf':

                $products = \Product::all();
                foreach ($products as $product) {
                    if($product->instruction_pdf_src){
                        $to = $this->makeToPath($product);
                        $toFullPath = public_path() . '/' . $to;

                        if($this->fileDownload($product->instruction_pdf_src, $toFullPath)){
                            $this->cmd->info("Путь в БД: http://ikmed.ru/$to");
                            $product->instruction_pdf = "http://ikmed.ru/$to";
                            $product->save();
                        }
                    }
                }


                break;
            case 'flv':

                $products = \Product::all();
                foreach ($products as $product) {
                    if($product->instruction_flv_src){
                        $to = $this->makeToPath($product);
                        $toFullPath = public_path() . '/' . $to;

                        if($this->fileDownload($product->instruction_flv_src, $toFullPath)){
                            $this->cmd->info("Путь в БД: http://ikmed.ru/$to");
                            $product->instruction_flv = "http://ikmed.ru/$to";
                            $product->save();
                        }
                    }
                }

                break;
        }


    }

    public function fileDownload($from, $toFullPath)
    {

        $toFullPath = trim($toFullPath);

        if(strpos($from, 'http://www.csmedica.ru/upload/iblock/362/3629454eac8df0c0e18545020b253a62.pdf') !== false){
            $from = 'http://www.csmedica.ru/upload/iblock/d0d/d0d03990859c6defd824570eba8b00f9.pdf';
        }

        if(strpos($from, 'http://www.csmedica.ru/upload/iblock/d65/d658349c9c3279818f32f863ac617dc7.pdf') !== false){
            $from = 'http://www.csmedica.ru/upload/iblock/f62/f6265c6ac3eeb1b9b6f2cafbeb601305.pdf';
        }

        if(strpos($from, '/upload/video/u22.flv') !== false){
            $from = 'http://www.csmedica.ru/upload/video/u22.flv';
        }

        //

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
            try{
                $raw = file_get_contents($from);
                return file_put_contents($toFullPath, $raw);
            }
            catch(\ErrorException $e)
            {
                echo $e->getMessage() .  PHP_EOL;
            }
        }

        return false;


    }

    public function makeToPath($item)
    {

        switch($this->who){
            case 'image':
                $product = $item->product()->with('categories')->first();
                if(count($product->categories) != 1){
                    die('ОПАСНОСТЕ!!!!!');
                }
                $test = $product->categories[0]->ancestorsAndSelf()->get();
                unset($test[0]);
                $to = 'images/products/';
                foreach ($test as $t) {
                    $to .= "{$t->id}/";
                }
                $ext = pathinfo($item->src, PATHINFO_EXTENSION);
                $to .= "{$product->uri}-{$item->id}.$ext\n";
                return $to;
                break;
            case 'pdf':

                if(count($item->categories) != 1){
                    die('ОПАСНОСТЕ!!!!!');
                }
                $test = $item->categories[0]->ancestorsAndSelf()->get();
                unset($test[0]);
                $to = 'files/pdf/products/';
                foreach ($test as $t) {
                    $to .= "{$t->id}/";
                }
                $ext = pathinfo($item->instruction_pdf_src, PATHINFO_EXTENSION);
                $to .= "{$item->uri}.$ext\n";
                return $to;

                break;
            case 'flv':

                if(count($item->categories) != 1){
                    die('ОПАСНОСТЕ!!!!!');
                }
                $test = $item->categories[0]->ancestorsAndSelf()->get();
                unset($test[0]);
                $to = 'files/flv/products/';
                foreach ($test as $t) {
                    $to .= "{$t->id}/";
                }
                $ext = pathinfo($item->instruction_flv_src, PATHINFO_EXTENSION);
                $to .= "{$item->uri}.$ext\n";
                return $to;

                break;
        }
        return false;
    }


}