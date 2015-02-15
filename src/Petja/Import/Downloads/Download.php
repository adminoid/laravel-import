<?php namespace Petja\Import\Downloads;
use Intervention\Image\Facades\Image;

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
                //echo $image->path . "\n";
                $product = $image->product()->first();

                //echo $product->id . "\n";

                $from = $this->makeFrom($image, $product);
                //var_dump($from);

                /*$to = $this->makeTo($image, $product);
                var_dump($to);*/
            }


            $this->cmd->error('test778');
        }
    }

    public function makeFrom($image, $product)
    {
        if($this->who == 'image'){

            $path = trim($image->path);
            $extension = pathinfo($path, PATHINFO_EXTENSION);

            if(!$extension){

                //$image->product()->detach();

                //$product->save();



                echo "({$image->id}) {$image->path}\n";

                $image->delete();

            }


            //return trim($image->path);

        }

        return false;
    }

    public function makeTo($image, $product)
    {
        if($this->who == 'image'){
            return basename($image->path);
        }
    }
}