<?php

declare(strict_types=1);

namespace Keboola\DaktelaCovid;

use GuzzleHttp\Client;
use Keboola\Component\BaseComponent;
use Keboola\Csv\CsvReader;
use Keboola\Csv\CsvWriter;

class Component extends BaseComponent
{
    protected function run(): void
    {
        $contactReader = $this->getRealConfig()->getContactTableCsvReader();
        $contactReader->next(); //first line is header
        $csvWriter = new CsvWriter($this->getRealConfig()->getOutputSentTablePath());
        $csvWriter->writeRow($this->getRealConfig()->getAlreadySentTable()->getHeader());
        foreach ($this->getRealConfig()->getAlreadySentTable() as $row) {
            $csvWriter->writeRow($row);
        }

        $numbersToSend = $this->filterNotSentNumbers(
            $this->getRealConfig()->getContactTableCsvReader(),
            $this->getRealConfig()->getAlreadySentTable()
        );

        $this->getLogger()->info(sprintf('This job will send %s number(s)', count($numbersToSend)));

        foreach ($numbersToSend as $row) {
            $this->getLogger()->info(
                sprintf(
                    'Sending %s *** %s to daktela',
                    substr($row[0], 0, 5),
                    substr($row[0], -3, 3)
                )
            );
            $recordId = $this->sendNumberToCampaign([
                'queue' => (int) $row[1],
                'number' => $row[0],
                'customFields' => [
                    'clevermaps_url' => [$row[2]],
                ],
            ]);
            $csvWriter->writeRow([$row[0], $recordId]);
        }
    }

    private function filterNotSentNumbers(CsvReader $allNumbers, CsvReader $alreadySentNumbers): array
    {
        $sentNumbersMap = [];
        foreach ($alreadySentNumbers as $row) {
            $sentNumbersMap[] = $row[0];
        }

        $notSentAlready = [];
        foreach ($allNumbers as $row) {
            if (!in_array($row[0], $sentNumbersMap)) {
                $notSentAlready[] = $row;
            }
        }
        return $notSentAlready;
    }

    private function sendNumberToCampaign(array $data): string
    {
        $req = (new Client())->request('POST', $this->getRealConfig()->getPostUrl(), [
            'json' => $data,
        ]);

        $body = \GuzzleHttp\json_decode($req->getBody(), true);
        $recordId = $body['result']['name'];
        $this->getLogger()->info('Record in Daktela created: ' . $recordId);
        return $recordId;
    }


    protected function getConfigClass(): string
    {
        return Config::class;
    }


    private function getRealConfig(): Config
    {
        /** @var Config $config */
        $config = $this->getConfig();
        return $config;
    }

    protected function getConfigDefinitionClass(): string
    {
        return ConfigDefinition::class;
    }
}
