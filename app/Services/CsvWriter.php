<?php

namespace Koltsova\Freshdesk\Services;

use League\Csv\Writer;

class CsvWriter
{
    private Writer $csv;

    public function __construct(string $filename)
    {
        $this->csv = Writer::createFromPath(__DIR__.'/../../storage/'.$filename, 'w');
        $this->csv->setEnclosure('\'');
    }

    public function writeRow(array $data)
    {
        $this->csv->insertOne($data);
    }

}