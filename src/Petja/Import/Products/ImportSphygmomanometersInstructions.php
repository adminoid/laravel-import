<?php namespace Petja\Import\Products;

use Jyggen\Curl\Request;

class ImportSphygmomanometersInstructions {

    public $products, $items, $cmd;

    public function __construct($cmd)
    {
        $this->cmd = $cmd;
    }

    public function prepare(){

        if(php_sapi_name() != 'cli') return 'Скрипт запускать только из командной строки';
        $this->products = simplexml_load_file(base_path() . '/import/xmls/sphygmomanometers-ch.xml');
        $this->items = $this->products->manufacter->sphygmomanometers->item;

        return true;

    }

    public function processOneItem($instructionUrl)
    {

        $instructionUrl = trim($instructionUrl);
        $this->cmd->info($instructionUrl);


    }

    public function saveNewXml()
    {

        $newFile = base_path() . '/import/xmls/sphygmomanometers-ch.xml';
        if($this->products->asXML($newFile)){
            return $newFile;
        }

        return false;
    }



    public function fileGetContents($fromUrl)
    {

        $this->cmd->line("from $fromUrl");

        $request = new Request('http://www.csmedica.ru/upload/Poverka/M2 Basic с адаптером (HEM-7116-ARU)/20140200001LG_20140216500LG.pdf');
        $request->execute();

        $resp = $request->getResponse();

        $request = null;

        return $resp;

    }



}