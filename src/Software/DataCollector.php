<?php

namespace App\Software;

use League\Csv\Reader;
use Psr\Log\LoggerInterface;

class DataCollector
{
    public const CUSTOMERS = 'customers';
    public const PURCHASES = 'purchases';
    /**
     * @var array
     */
    protected $mapping_civil = [
        1 => 'mme',
        2 => 'm',
    ];

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * Undocumented variable.
     *
     * @var array
     */
    private $files;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->files = [];
    }

    public function getData()
    {
        $this->logger->warning('All Data parsed');
    }

    public function addFile(string $file): self
    {
        $this->files[] = $file;

        return $this;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * Return customers with purchases.
     *
     * @return array
     */
    public function collectDataCsv(): array
    {
        if (count($this->getFiles()) === 0) {
            return [];
        }
        $pre_data = [];
        foreach ($this->getFiles() as $file) {
            $type = $this->getTypeOfData($file);
            if (!\in_array($type, [self::CUSTOMERS, self::PURCHASES])) {
                continue;
            }
            $reader = Reader::createFromPath($file);
            $reader->setDelimiter(';');

            $data = iterator_to_array($reader->getRecords());

            switch ($type) {
                case self::CUSTOMERS:
                    $pre_data[self::CUSTOMERS] = $this->getDataCustomer($data);
                    break;
                case self::PURCHASES:
                    $pre_data[self::PURCHASES] = $this->getDataPurchase($data);
                    break;
            }
        }
        $results = $this->mergeDataCollector($pre_data);

        return $results;
    }

    /**
     * Construit le format attendu en ajoutant purchase dans chaque client.
     *
     * @param array $data
     *
     * @return array
     */
    public function mergeDataCollector(array $data): array
    {
        $data_customer = [];
        foreach ($data[self::CUSTOMERS] as $key => $customer) {
            if (array_key_exists($key, $data[self::PURCHASES])) {
                $customer['purchases'] = $data[self::PURCHASES][$key];
            }
            $data_customer[] = $customer;
        }
        $this->logger->debug(json_encode($data_customer));

        return $data_customer;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function getDataCustomer(array $data): array
    {
        $val = [];
        array_shift($data); //Pour enlever la première ligne entete !
        foreach ($data as $value) {
            if (!isset($value[6])) {
                continue;
            }
            $val[$value[0]] = [
                'salutation' => $this->mapping_civil[$value[1]],
                'last_name' => $value[2],
                'first_name' => $value[3],
                'email' => $value[6],
            ];
        }

        return $val;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function getDataPurchase(array $data): array
    {
        $val = [];
        array_shift($data); //Pour enlever la première ligne entete !
        foreach ($data as $value) {
            $val[$value[1]][] = [
                'product_id' => $value[2],
                'price' => (int) $value[4],
                'currency' => $value[5],
                'quantity' => (int) $value[3],
                'purchased_at' => $value[6],
            ];
        }

        return $val;
    }

    /**
     * A partir du nom du fichier, renvoyer le type a savoir si c'est customer ou purchase.
     *
     * @param [type] $filename
     *
     * @return string
     */
    protected function getTypeOfData($filename)
    {
        if (strpos($filename, self::CUSTOMERS) !== false) {
            return self::CUSTOMERS;
        }

        return self::PURCHASES;
    }
}
