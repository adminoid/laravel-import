<?php namespace Petja\Import\Products\ProductTypes;
/**
 * Created by PhpStorm.
 * User: petja
 * Date: 25/01/15
 * Time: 18:11
 */

// todoParse1 Здесь будет заливка тонометров из XML в БД сайта


class Nebulizers extends ProductImportTemplate
{

    public function run()
    {
        $products = simplexml_load_file($this->file);
        $this->items = $products->manufacter->nebulizers->item;

        /*$this->cmd->info('Делаем сиды:');
        \Iseed::generateSeed('categories');
        \Iseed::generateSeed('category_types');
        die;*/

        $this->foreachXml();
    }

    public function listCharacteristics($item)
    {
        if($item->characteristics->Count() < 1){
            $this->cmd->error("Нет характеристик у $item->title");
            return true;
        }
        $subCharacteristics = $item->characteristics->children();

        $this->cmd->comment("обход характеристик");


        foreach ($subCharacteristics as $characteristic) {

            $characteristicName = trim($characteristic->getName());

            $characteristicFields[] = $characteristicName;
            echo "\t" . $characteristicName . PHP_EOL;

        }

        return true;
    }

    public function processCharacteristics($item)
    {


        $fieldsMap = [
            'the-average-particle-size-of-the-aerosol' => 'aerosol_size',
            'capacity-of-medicines' => 'capacity',
            'the-residual-amount-of-medication' => 'residual',
            'noise-level' => 'noise',
            'aerosol-less-5-microns' => 'less5mkm',
            'external-dimensions' => 'dimensions',
            'weight' => 'weight',
            'length-of-the-airway-tube' => 'tube_length',
        ];

        if(isset($item->characteristics[0])){
            $characteristics = $this->clearData($item->characteristics[0]);
        }else{
            return false;
        }

        $characteristicsData = [];
        foreach ($fieldsMap as $from => $to){
            if(array_key_exists($from, $characteristics)){
                $characteristicsData[$to] = $characteristics[$from];
            }
        }

        return $characteristicsData;

    }

    public function foreachXml()
    {
        // Заливаем поштучно в БД
        foreach ($this->items as $item) {

            // + Взять список уникальных названий тегов характеристик
            /*$this->listCharacteristics($item);
            continue;*/

            // + Выписать список полей, которые надо перенести
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
            $newProduct->save();

            // + Сделать Nebulizer
            if($newCharacteristicsData = $this->processCharacteristics($item))
            {
                $newCharacteristics = \PNebulizer::create($newCharacteristicsData);
                //$this->cmd->line('перед схранением $newCharacteristics');
                //$newCharacteristics->save();
                $this->cmd->line('перед схранением $newProduct->extend()->save($newCharacteristics)');
                $newCharacteristics->product()->save($newProduct);
                //$newProduct->extend()->save($newCharacteristics);
            }


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

    public function test()
    {
        echo 'test' . PHP_EOL;
    }

}