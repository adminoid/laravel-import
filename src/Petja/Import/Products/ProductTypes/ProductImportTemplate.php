<?php
/**
 * Created by PhpStorm.
 * User: petja
 * Date: 29/01/15
 * Time: 14:42
 */

namespace Petja\Import\Products\ProductTypes;


abstract class ProductImportTemplate {

    public $items;

    /**
     * @param $file
     * @param $cmd
     */
    public function __construct($file, $cmd)
    {
        $this->cmd = $cmd;
        $this->file = $file;
        $cmd->info('Запуск: ' . get_class($this) . '("' . $file . '");');
    }

    public function clearData($data)
    {
        $clearArray = array();
        foreach ($data as $k => $v) {
            $v = (string) $v;
            if(empty($v)){
                continue;
            }
            $clearArray[$k] = trim($v);
        }
        return $clearArray;
    }

    abstract public function run();
    abstract public function foreachXml();

}