<?php namespace Petja\Import\Categories;

use Category;

class ImportCategories {

    public static function test()
    {
        foreach (Category::allLeaves()->get() as $leaf) {
            echo "{$leaf->name}<br>\n";
        }
    }

    public static function import()
    {


//		\Iseed::generateSeed('categories');
//		\Iseed::generateSeed('category_types');

        die;

        /**
         * + 1) Перезалить категории. Сделать выборку. Сделать сида
         * - 2) Сделать доп таблицу под товар. Сделать закачку картинок. Залить товары с картинками и доп. таблицей
         * + 3) Сделать выборку - протестировать. Сделать сида на товар
         * todo 4) Залить остальные товары. Сделать сиды, выборки
         * todo 5) Приступить к админке
         */

        /*$CT_manufacturer = new CategoryType(array('name' => 'manufacturer'));
        $CT_manufacturer->save();*/
        //$CT_manufacturer = CategoryType::where('name','manufacturer')->first();

        //$CT_manufacturer->categories()->save($testCat);


        $products_xml = simplexml_load_file(base_path() . '/import/xmls/stucture.xml');

        foreach ($products_xml->manufacter as $manufacter) {
            echo $manufacter;
            echo "<hr>\n";

            $cat_name = trim((string) $manufacter);
            // создать мануфактера, если небыло и обновить если был

            $root = Category::firstOrCreate(array('name'=>'root node'));

            $ct = CategoryType::firstOrCreate(array('name'=>'manufacturer'));
            $cat = Category::firstOrCreate(array('name'=>$cat_name));



            $cat->makeChildOf($root);
            $ct->categories()->save($cat);

            foreach ($manufacter as $category) {

                $cat_name_2 = trim((string) $category);

                $ct_2 = CategoryType::firstOrCreate(array('name'=>'product category'));
                $cat_2 = Category::firstOrCreate(array('name'=>$cat_name_2, 'import_id'=>$category['id']));
                $ct_2->categories()->save($cat_2);
                $cat_2->makeChildOf($cat);

                echo "$category ~~ {$category['id']}";
                echo "<br>\n";

                foreach ($category as $sub_category) {

                    $cat_name_3 = trim((string) $sub_category);

                    $cat_3 = Category::firstOrCreate(array('name'=>$cat_name_3, 'import_id'=>$sub_category['id']));
                    $ct_2->categories()->save($cat_3);
                    $cat_3->makeChildOf($cat_2);

                    echo "$sub_category -- {$sub_category['id']}";
                    echo "<br>\n";

                }

            }

        }

    }

}