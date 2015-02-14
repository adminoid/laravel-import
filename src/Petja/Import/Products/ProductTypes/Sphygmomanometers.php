<?php namespace Petja\Import\Products\ProductTypes;
/**
 * Created by PhpStorm.
 * User: petja
 * Date: 25/01/15
 * Time: 18:11
 */

// todoParse1 Здесь будет заливка тонометров из XML в БД сайта


class Sphygmomanometers extends ProductImportTemplate
{

    public function run()
    {
        $products = simplexml_load_file($this->file);
        $this->items = $products->manufacter->sphygmomanometers->item;

        /*$this->cmd->info('Делаем сиды:');
        \Iseed::generateSeed('categories');
        \Iseed::generateSeed('category_types');
        die;*/

        $this->foreachXml();
    }

    public function foreachXml()
    {
        // + добавить в БД поля - старые пдф-инструкшен и новые
        // + добавить в БД старые картинки и новые
        // todoParse1 Залить поштучно в БД

        foreach ($this->items as $item) {

            // todoParse1 пробуем парсить картинки и пдф, если не полчилось - то оставляем старый урл, если получилось - новый, но сохраняем в лог - что на что поменялось
            // todoParse1 Создаем объект Sphygmomanometers, прикрепляем его к его категории

            //$this->cmd->line($item->{"pdf-instruction"});
            //$this->cmd->line($item->title);

            /*var_dump( (string) $item->description);
            continue;*/

            // todoParse1 Выписать список полей, которые надо перенести


            /**
             * $item['cat_id'] // id категории
             * $item->category // и так указан cat_id - просто проверить соответствие, это поле не нужно
             * $item->title // название
             * $item->images // подцикл с картинками
             * $item->description // короткое описание
             * $item->complect // описание комплекта
             * $item->{"pdf-instruction"} // ссылка на pdf инструкцию
             * $item->content // текст на странице
             */

            /**
             * Затем еще сделать связи с:
             * categories
             * images
             */


            // + Взять категорию по xml_cat_id
            $catId = (integer)$item->category['cat_id'];
            $category = \Category::where('xml_cat_id', '=', $catId)->first();
            $this->cmd->line("xml_cat_id is: $catId,\n name is: {$category->name}");

            // + Сделать итем
            $newProductData = $this->clearData(array(
                'name' => $item->title,
                'description' => $item->description,
                'content' => $item->content,
                'complement' => $item->complect,
                'instruction_pdf_src' => $item->{"pdf-instruction"},
                'instruction_flv_src' => $item->{"video-instruction"},
                'csmedica_url' => $item['src'],
            ));
            $newProduct = \Product::create($newProductData);

            // + в категорию положить итем

            $category->products()->save($newProduct);

            // + Сделать картинки

            foreach ($item->images->img as $img) {
                $newImg = \Image::create(array(
                    'path' => (string)$img,
                    'downloaded' => false,
                ));
                $newProduct->images()->save($newImg);
            }

        }

    }

}