<?php

declare(strict_types=1);

namespace Keboola\DaktelaCovid;

use Keboola\Component\Config\BaseConfig;
use Keboola\Csv\CsvOptions;
use Keboola\Csv\CsvReader;

class Config extends BaseConfig
{

    private const IN_DIR = '/data/in/tables/';
    private const OUT_DIR = '/data/out/tables/';


    public function getContactTableCsvReader(): CsvReader
    {
        $tablePath = self::IN_DIR . $this->getValue(['parameters', 'contact_table']) . '.csv';
        return new CsvReader(
            $tablePath,
            CsvOptions::DEFAULT_DELIMITER,
            CsvOptions::DEFAULT_ENCLOSURE,
            CsvOptions::DEFAULT_ESCAPED_BY,
            1
        );
    }

    public function getAlreadySentTable(): CsvReader
    {
        $tablePath = self::IN_DIR . $this->getValue(['parameters', 'already_sent_table']) . '.csv';
        return new CsvReader(
            $tablePath,
            CsvOptions::DEFAULT_DELIMITER,
            CsvOptions::DEFAULT_ENCLOSURE,
            CsvOptions::DEFAULT_ESCAPED_BY,
            1
        );
    }

    public function getExtendInfoTable(): CsvReader
    {
        $tablePath = self::IN_DIR . $this->getValue(['parameters', 'extend_info_table']) . '.csv';
        return new CsvReader(
            $tablePath,
            CsvOptions::DEFAULT_DELIMITER,
            CsvOptions::DEFAULT_ENCLOSURE,
            CsvOptions::DEFAULT_ESCAPED_BY,
            1
        );
    }

    public function getOutputSentTablePath(): string
    {
        return self::OUT_DIR . '/output_sent.csv';
    }

    public function getPostUrl(): string
    {
        return sprintf(
            'https://%s/api/v6/campaignsRecords.json?accessToken=%s',
            $this->getValue(['parameters', 'daktela_gateway_url']),
            $this->getValue(['parameters', '#daktela_token'])
        );
    }
}
