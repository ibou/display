<?php

namespace App\Reader;

use Symfony\Component\Serializer\Encoder\CsvEncoder;

class Import extends CsvEncoder
{
    public function getCsvFromRoot()
    {
        return true;
    }
}
