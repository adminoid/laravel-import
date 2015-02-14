<?php namespace Petja\Import\Products;

use Jyggen\Curl\Request;

class ParsePdfFromContent {

    public $products, $items, $cmd, $nodeName;

    public function __construct($cmd)
    {
        $this->cmd = $cmd;
    }

    public function prepare(){

        $xmlPath = 'import/xmls/thermometers.xml';
        $this->nodeName = 'thermometers';

        if(php_sapi_name() != 'cli') return 'Скрипт запускать только из командной строки';
        $this->products = simplexml_load_file(base_path() . '/' . $xmlPath);
        $this->items = $this->products->manufacter->{$this->nodeName}->item;

        return true;

    }

    public function processOneItem($item)
    {

        /*$this->cmd->line('dhdhdhhdhdhhdhdh');
        var_dump($item->content);*/

        $item->content = preg_replace_callback('#<a\shref=\"([^\"]+)\"#i', function($matches){

            if(is_array($matches))
            {
                $newUrl = $this->processPDFLink($matches[1]);
                $newLink = "<a href=\"{$newUrl}\"";
                return $newLink;
            }
            else
            {
                return 'not_array';
            }

        }, $item->content);

        // dom тип item который в обходе:
        $itemDom = dom_import_simplexml($item);

        // Делаю cdata с контентом из старого итема
        $cdata = $itemDom->ownerDocument->createCDataSection($item->content);

        // dom тип старого контента ($item->content) и сразу удаляю
        $contentDom = dom_import_simplexml($item->content);
        $itemDom->removeChild($contentDom);

        // Делаю новый элемент content, прикрепляю к нему CData и прикрепляю его к итему
        $newContent = $itemDom->ownerDocument->createElement('content');
        $newContent->appendChild($cdata);
        $itemDom->appendChild($newContent);

        return $newContent->textContent;

    }

    public function saveNewXml()
    {

        $newFile = base_path() . "/import/xmls/{$this->nodeName}-ch.xml";
        if($this->products->asXML($newFile)){
            return $newFile;
        }

        return false;
    }

    /**
     * Переименовывем путь - и отправляем на закачку отсюда на новый локальный путь
     *
     * @param $fromUrl
     * @return string
     */
    public function processPDFLink($fromUrl){


        $this->cmd->line('парсим url: ' . $fromUrl);

        $newUrlData = $this->transformPaths($fromUrl);

        $toDir = pathinfo($newUrlData['toFile'], PATHINFO_DIRNAME);
        $toUrl = $newUrlData['toUrl'];

        if(file_exists($newUrlData['toFile']))
        {

            $this->cmd->info("$toUrl - файл уже загружен");
            return $toUrl;

        }
        elseif($fromFile = $this->fileGetContents($fromUrl))
        {
            if (!file_exists($toDir)) {

                mkdir($toDir, 0777, true);

            }

            if(file_put_contents($newUrlData['toFile'], $fromFile))
            {
                return $toUrl;
            }
            else
            {
                return $fromUrl;
            }
        }
        else
        {
            return $fromUrl;
        }

    }

    public function transformPaths($fromUrl)
    {
        $to = preg_replace('#http:\/\/[^\/]+\/[^\/]+\/[^\/]+\/[^\/]*\((.*)\)\/([^\"]+)#i', '$1/$2', $fromUrl);

        if($fromUrl == $to){
            // http://www.csmedica.ru/upload/Poverka/20140900001UF_20140901400UF.pdf
            $to = preg_replace('#.*/([^/]+)#i', '$1', $fromUrl);
        }

        var_dump($to);

        $to = strtolower($to);
        $to = str_replace(' ', '', $to);


        $relativePath = "/files/pdf/poverka/{$this->nodeName}/" . $to;
        $toFile = public_path() . $relativePath;

        // public/files/pdf/poverka/sphygmomanometers/hem-7117h-%F0%90ru/20140100001lg_20140122240lg.pdf



        if(preg_match('#(.*)[а-я]+.(.*)#i', $toFile, $matches)){

            $toFile = preg_replace('#(.*)[а-я]+.(.*)#i', '${1}a${2}', $toFile);

        }

        // тут можно добавить еще трансформаций

        $ret = array(
            'toFile' => $toFile,
            'toUrl' => url($relativePath),
        );

        //var_dump($ret);

        return $ret;
    }

    public function fileGetContents($fromUrl)
    {

        $this->cmd->line("from $fromUrl");

        //$request = new Request('http://www.csmedica.ru/upload/Poverka/M2 Basic с адаптером (HEM-7116-ARU)/20140200001LG_20140216500LG.pdf');
        $request = new Request($fromUrl);
        $request->execute();

        $resp = $request->getResponse();

        $request = null;

        return $resp;

    }

//    public function urlFixIfNotWorks($url)
//    {
//        $parts = parse_url($url);
//
//        $path = explode('/', $parts['path']);
//
//        $newUrl = '';
//        foreach ($path as &$part) {
//
//            if(empty($part)){
//                continue;
//            }
//
//            if(preg_match('/[а-я]/i', $part)){
//
//                $part = str_replace("\xD0\x3F", "\xD0\x98", $part);
//                $part = iconv("UTF-8", "CP1251//IGNORE", $part);
//
//                $newUrl .= urlencode($part) . '/';
//                $newUrl = str_replace($this->urlReplace['from'], $this->urlReplace['to'], $newUrl);
//
//            }else{
//                $newUrl .= $part . '/';
//            }
//        }
//
//        return 'http://www.csmedica.ru/' . trim($newUrl, '/');
//    }


}