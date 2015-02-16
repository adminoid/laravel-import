<?php
/**
 * Created by PhpStorm.
 * User: petja
 * Date: 29/01/15
 * Time: 15:42
 */

namespace Petja\Import\Products;

use \Category, \Product;

class Test {

    public $cmd;

    public function __construct($name, $cmd)
    {
        $this->cmd = $cmd;



        $this->{'test'.$name}();
    }

    public function testSeed(){

        $this->cmd->info('Делаем сиды:');
        \Iseed::generateSeed('categories');
        \Iseed::generateSeed('categorizables');
        \Iseed::generateSeed('category_types');
        \Iseed::generateSeed('imageables');
        \Iseed::generateSeed('images');

        \Iseed::generateSeed('p_nebulizers');
        \Iseed::generateSeed('p_acoustic_toothbrushes');
        \Iseed::generateSeed('p_fat_analysers_and_scales');
        \Iseed::generateSeed('p_pedometers_and_activity_analysers');
        \Iseed::generateSeed('p_phonendoscopes');
        \Iseed::generateSeed('p_thermometers');

        \Iseed::generateSeed('thumbs');
        \Iseed::generateSeed('products');
        die;

    }

    public function testThumbs()
    {
        foreach (\Image::all() as $img) {
            echo $img->path . PHP_EOL;

            foreach ($img->thumbs()->get() as $thumb) {
                echo $thumb->path . PHP_EOL;
            }

            $this->cmd->info('=====================');

        }

    }

    public function testJust()
    {
        $catId = 18;

        $category = Category::find($catId);


        $categoriesAndSubCategories = $category->descendantsAndSelf()->get();

        if(is_object($categoriesAndSubCategories)){
            foreach ($categoriesAndSubCategories as $subCategory) {

                echo $subCategory->name . PHP_EOL;

                $products = $subCategory->products()->get();
                if(is_object($products)){
                    foreach ($products as $product) {
                        $this->cmd->comment($product->name);


                        if($product->extendable_type){

                            $extended = $product->extendable()->first();

                            if(is_object($extended) && count($extended) == 1){

                                $this->cmd->info($extended->dimensions);

                            }else{
                                $this->cmd->error('Невероятно! У Product более 1 extendable, хотя связь тут "1 к 1"');
                                die;
                            }

                        }else{
                            $this->cmd->error('У акустической зубной щетки c id = ' . $product->id . ' нету характеристик');
                        }



                    }
                }

            }

        }


    }

    public function testCategories(){

        $root = Category::roots()->first();

        var_dump($root->name);

        $this->cmd->line('--------------------');

        $manufacturers = $root->children()->get();

        foreach ($manufacturers as $manufacturer) {
            //var_dump($manufacturer->name);

            $subCats1 = $manufacturer->children()->get();

            foreach ($subCats1 as $subCat1) {
                var_dump($subCat1->name);
            }

        }


    }

    public function testSphygmomanometers()
    {

        $tested = 3;

        $sphygmomanometersCategory = Category::find($tested); //

        $this->cmd->info($sphygmomanometersCategory->name);

        //$sphygmomanometers = $sphygmomanometersCategory->children()->get();
        $sphygmomanometerCategories = $sphygmomanometersCategory->descendantsAndSelf()->get();

        $catIds = array();
        foreach ($sphygmomanometerCategories as $sphygmomanometerCategory) {

            $this->cmd->info("($sphygmomanometerCategory->id) $sphygmomanometerCategory->name");

            $this->cmd->comment("Товары: ");

            $products = $sphygmomanometerCategory->products()->get();

            /*if($sphygmomanometerCategory->id == 4){
                var_dump($products); die;
            }*/

            foreach ($products as $product) {

                echo $product->name . PHP_EOL;

                foreach ($product->images as $img) {

                    echo "\t" . $img->path . PHP_EOL;

                }

            }

        }

    }

    public function testNebulizers()
    {
        $catId = 9; // Небулайзеры

        $category = Category::find($catId);


        $categoriesAndSubCategories = $category->descendantsAndSelf()->get();

        if(is_object($categoriesAndSubCategories)){
            foreach ($categoriesAndSubCategories as $subCategory) {

                echo $subCategory->name . PHP_EOL;

                $products = $subCategory->products()->get();
                if(is_object($products)){
                    foreach ($products as $product) {
                        $this->cmd->comment($product->name);


                        if($product->extendable_type){

                            $extended = $product->extendable()->first();

                            if(is_object($extended) && count($extended) == 1){

                                $this->cmd->info($extended->aerosol_size);

                            }else{
                                $this->cmd->error('Невероятно! У Product более 1 extendable, хотя связь тут "1 к 1"');
                                die;
                            }

                        }else{
                            $this->cmd->error('У небулайзера c id = ' . $product->id . ' нету характеристик');
                        }



                    }
                }

            }

        }


    }

    public function testAcousticToothbrushes()
    {
        $catId = 29; // Небулайзеры

        $category = Category::find($catId);


        $categoriesAndSubCategories = $category->descendantsAndSelf()->get();

        if(is_object($categoriesAndSubCategories)){
            foreach ($categoriesAndSubCategories as $subCategory) {

                echo $subCategory->name . PHP_EOL;

                $products = $subCategory->products()->get();
                if(is_object($products)){
                    foreach ($products as $product) {
                        $this->cmd->comment($product->name);


                        if($product->extendable_type){

                            $extended = $product->extendable()->first();

                            if(is_object($extended) && count($extended) == 1){

                                $this->cmd->info($extended->colors);

                            }else{
                                $this->cmd->error('Невероятно! У Product более 1 extendable, хотя связь тут "1 к 1"');
                                die;
                            }

                        }else{
                            $this->cmd->error('У акустической зубной щетки c id = ' . $product->id . ' нету характеристик');
                        }



                    }
                }

            }

        }


    }


    public function testElectronicMassagers()
    {
        $catId = 23; // Небулайзеры

        $category = Category::find($catId);


        $categoriesAndSubCategories = $category->descendantsAndSelf()->get();

        if(is_object($categoriesAndSubCategories)){
            foreach ($categoriesAndSubCategories as $subCategory) {

                echo $subCategory->name . PHP_EOL;

                $products = $subCategory->products()->get();
                if(is_object($products)){
                    foreach ($products as $product) {
                        $this->cmd->comment($product->name);


                        if($product->extendable_type){

                            $extended = $product->extendable()->first();

                            if(is_object($extended) && count($extended) == 1){

                                $this->cmd->info($extended->colors);

                            }else{
                                $this->cmd->error('Невероятно! У Product более 1 extendable, хотя связь тут "1 к 1"');
                                die;
                            }

                        }else{
                            $this->cmd->error('У акустической зубной щетки c id = ' . $product->id . ' нету характеристик');
                        }



                    }
                }

            }

        }


    }

}