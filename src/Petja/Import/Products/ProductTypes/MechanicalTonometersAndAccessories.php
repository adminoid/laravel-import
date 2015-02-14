<?php namespace Petja\Import\Products\ProductTypes;
/**
 * Created by PhpStorm.
 * User: petja
 * Date: 25/01/15
 * Time: 18:11
 */

// + Здесь будет заливка тонометров из XML в БД сайта


class MechanicalTonometersAndAccessories extends ProductImportTemplate
{

    function utf8_for_xml($string)
    {
        return preg_replace ('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $string);
    }

    public function run()
    {

        $xmlPlain = file_get_contents($this->file);
        $xmlPlain = $this->utf8_for_xml($xmlPlain);

        $products = simplexml_load_string($xmlPlain);
        $this->items = $products->manufacter->{"mechanical-tonometers-and-accessories"}->item;

        $this->foreachXml();
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

            // -------------- === ---------------

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