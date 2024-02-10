<?php

namespace App\Controller;

use App\Library\AbstractController;
use App\Library\Helper;
use MaxMind\Db\Reader;

class GeoController extends AbstractController
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * Countries
     *
     * @var array
     */
    private $countries = [];

    /**
     * IP Cache
     *
     * @var array
     */
    private $storage = [];

    /**
     * IP Cache Valid
     *
     * @var array
     */
    private $storageInvalid;

    /**
     * GeoAbstractController constructor.
     *
     * @throws Reader\InvalidDatabaseException
     */
    public function __construct()
    {
        // Open City DB
        $db = Helper::getRootDir('data/GeoLite2-City_20240209/GeoLite2-City.mmdb');
        if (file_exists($db)) {
            $this->reader = new Reader($db);
        }

        // Open JSON File
        $json = Helper::getRootDir('data/Country.json');
        if (file_exists($json)) {
            $this->countries = json_decode(file_get_contents($json), true);
        }
    }

    /**
     * Geo Location
     *
     * @param string|null $IP
     * @throws Reader\InvalidDatabaseException
     */
    public function location(string $IP = null): void
    {
        // Get IP
        $IP = $IP ?? $this->getIP();
        if (!$IP || !filter_var($IP, FILTER_VALIDATE_IP)) {
            $this->errorResponse();
            return;
        }

        // Cache Invalid
        if (!$this->storageInvalid || ($this->storageInvalid < time())) {
            $this->storageInvalid = time() + 3600;
            $this->storage = [];
        }

        // Find MM DB & Json
        if (!isset($this->storage[$IP])) {
            $mmdb = $this->reader->get($IP);
            if ($mmdb) {
                $this->storage[$IP] = $this->countries[$mmdb['country']['iso_code']];

                // Response
                $this->storage[$IP]['location'] = $mmdb['location'];
                $this->storage[$IP]['city'] = $mmdb['city'];
            } else {
                $this->errorResponse();
                return;
            }
        }

        $this->jsonResponse($this->storage[$IP]);
    }

    /**
     * Get Client IP Address
     *
     * @return string
     */
    private function getIP(): string
    {
        return $this->request->server['remote_addr'];
    }
}
