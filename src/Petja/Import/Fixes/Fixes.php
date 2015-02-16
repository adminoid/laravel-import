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

        $products = \Product::all();

        foreach ($products as $product) {
            $product->instruction_pdf = trim($product->instruction_pdf);
            $product->instruction_pdf_src = trim($product->instruction_pdf_src);
            $product->save();
        }


    }

    public function rmField()
    {
        $product = \Product::find(54);
        echo $product->instruction_pdf_src . PHP_EOL;
        $product->instruction_pdf_src = '';
        $product->save();
    }

}