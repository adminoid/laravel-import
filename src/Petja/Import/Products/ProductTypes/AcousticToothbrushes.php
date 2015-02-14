<?php namespace Petja\Import\Products\ProductTypes;
/**
 * Created by PhpStorm.
 * User: petja
 * Date: 25/01/15
 * Time: 18:11
 */

// todoParse1 Здесь будет заливка тонометров из XML в БД сайта


class AcousticToothbrushes extends ProductImportTemplate
{

    public function run()
    {
        $products = simplexml_load_file($this->file);
        $this->items = $products->manufacter->{"acoustic-toothbrushes"}->item;

        /*$this->cmd->info('Делаем сиды:');
        \Iseed::generateSeed('categories');
        \Iseed::generateSeed('category_types');
        die;*/

        $this->foreachXml();
    }

    public function processCharacteristics($item)
    {


        $fieldsMap = [
            'color' => 'colors',
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

            /*$this->listCharacteristics($item);
            continue;*/

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
                $newCharacteristics = \PAcousticToothbrush::create($newCharacteristicsData);
                //$this->cmd->line('перед схранением $newCharacteristics');
                //$newCharacteristics->save();
                $this->cmd->line('перед схранением $newCharacteristics->product()->save($newProduct);');
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


}