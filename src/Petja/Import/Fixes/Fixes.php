<?php
/**
 * Created by PhpStorm.
 * User: petja
 * Date: 16/02/15
 * Time: 03:43
 */

namespace Petja\Import\Fixes;


class Fixes {

    public $cmd;

    public function __construct($cmd)
    {
        $this->cmd = $cmd;
    }

    public function run($name)
    {
        $this->$name();
    }

    public function moveCategory()
    {
        $product = \Product::find(2);
        $currentCategory = $product->categories()->first();
        echo $currentCategory->name . PHP_EOL;

        $product->categories()->detach();
        //$product->save();

        $needCategory = \Category::where('xml_cat_id', '=', 3)->first();
        $needCategory->products()->save($product);
        echo $needCategory->name . PHP_EOL;
    }

    public function trimField()
    {

        $images = \Image::all();

        foreach ($images as $image) {
            $image->path = trim($image->path);
            $image->save();
        }


    }

}