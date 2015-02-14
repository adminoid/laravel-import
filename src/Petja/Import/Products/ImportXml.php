<?php
/**
 * Created by PhpStorm.
 * User: petja
 * Date: 25/01/15
 * Time: 18:09
 */

namespace Petja\Import\Products;

use Illuminate\Console\Command;

class ImportXml {

    public $routeMap, $processor;

    public function route($file, Command $cmd)
    {

        $this->routeMap = array(
            'import/xmls/sphygmomanometers-ch.xml' => 'Sphygmomanometers',
            'import/xmls/nebulizers.xml' => 'Nebulizers',
            'import/xmls/acoustic-toothbrushes.xml' => 'AcousticToothbrushes',
            'import/xmls/electronic-massagers.xml' => 'ElectronicMassagers',
            'import/xmls/fat-analyzers-weighters.xml' => 'FatAnalysersAndScales',
            'import/xmls/mechanical-tonometers-and-accessories.xml' => 'MechanicalTonometersAndAccessories',
            'import/xmls/oral-cavity-irrigators.xml' => 'ProductClear',
            'import/xmls/pedometers-and-activity-analizers.xml' => 'ProductExtended',
            'import/xmls/phonendoscopes.xml' => 'ProductExtended',
            'import/xmls/thermometers-ch.xml' => 'ProductExtended',

        );

        $this->processor = '\Petja\Import\Products\ProductTypes\\' . $this->routeMap[$file]; // \Petja\Import\ProductsProductTypes\Nebulizers
        //$this->processor = '\Petja\Import\ProductsProductTypes\Sphygmomanometers';

        $fullFilePath = base_path() . '/' . $file;

        return new $this->processor($fullFilePath, $cmd);

    }

}