<?php namespace Petja\Import\Products\ProductTypes;
/**
 * Created by PhpStorm.
 * User: petja
 * Date: 10/02/15
 * Time: 18:11
 */

// todoParse1 Здесь будет заливка тонометров из XML в БД сайта


class FatAnalysersAndScales extends ProductImportTemplate
{

    function utf8_for_xml($string)
    {
        return preg_replace ('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $string);
    }

    public function run()
    {
        //$products = simplexml_load_file($this->file);
        $xmlPlain = file_get_contents($this->file);
        $xmlPlain = $this->utf8_for_xml($xmlPlain);

        $products = simplexml_load_string($xmlPlain);
        $this->items = $products->manufacter->{"fat-analyzers-weighters"}->item;

        $this->foreachXml();
    }

    public function foreachXml()
    {
        // Заливаем поштучно в БД
        foreach ($this->items as $item) {

            // + Взять список уникальных названий тегов характеристик
            /*$this->listCharacteristics($item);
            continue;*/

            // + Взять категорию по xml_cat_id
            //$catId = (integer)$item->category['cat_id'];

            $catId = (integer)$item['cat_id'];

            var_dump($catId);

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

            // !!!!!!!!!!!! ВНИМАНИЕ ТУТ МЕНЯТЬ!!!!!!!!!!!!!
            // + Сделать PFatAnalysersAndScales
            if($newCharacteristicsData = $this->processCharacteristics($item))
            {
                $newCharacteristics = \PFatAnalyserOrScale::create($newCharacteristicsData);
                $this->cmd->line('перед схранением $newCharacteristics->product()->save($newProduct)');
                $newCharacteristics->product()->save($newProduct);
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

    public function processCharacteristics($item)
    {


        $fieldsMap = [
            'maximum-weight-measurement' => 'maximum_weight',
            'external-dimensions' => 'dimensions',
            'error' => 'accuracy',
            'material' => 'material',
            'color' => 'color',
        ];

        // php artisan generate:migration create_p_fat_analysers_and_scales_table --fields="maximum_weight:float:unsigned, dimensions:string, error:string, material:string, color:string"
        // php artisan generate:model PFatAnalyserOrScale

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

    /**
     * Вспомогательный метод, чтобы вывести список характеристик у товаров в XML
     *
     * @param $item
     * @return bool
     */
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