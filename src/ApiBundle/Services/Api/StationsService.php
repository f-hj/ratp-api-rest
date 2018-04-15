<?php
namespace RatpApi\ApiBundle\Services\Api;

use RatpApi\ApiBundle\Helper\NamesHelper;
use RatpApi\ApiBundle\Helper\NetworkHelper;
use FOS\RestBundle\Exception\InvalidParameterException;
use Ratp\Api;
use Ratp\Line;
use Ratp\Reseau;
use Ratp\Station;
use Ratp\Stations;

class StationsService extends ApiService implements ApiDataInterface
{
    /**
     * @param $method
     * @param array $parameters
     * @return mixed
     */
    public function get($method, $parameters = [])
    {
        return parent::getData($method, $parameters);
    }

    /**
     * @param $parameters
     * @return array|null
     */
    protected function getLine($parameters)
    {
        $stations = [];

        $typesAllowed = [
            'rers',
            'metros',
            'tramways',
            'bus',
            'noctiliens'
        ];

        if (!in_array($parameters['type'], $typesAllowed)) {
            throw new InvalidParameterException(sprintf('Unknown type : %s', $parameters['type']));
        }

        $networkRatp = NetworkHelper::typeSlug($parameters['type'], true);
        $prefixcode  = NetworkHelper::forcePrefix($parameters['type']);

        $reseau = new Reseau();
        $reseau->setCode($networkRatp);

        $line = new Line();
        $line->setReseau($reseau);
        $line->setCode($prefixcode . $parameters['code']);

        $apiStation = new Station();
        $apiStation->setLine($line);

        // lines with several destinations
        if ($parameters['id'] != '') {
            $line->setId($parameters['id']);
        }

        $apiStations = new Stations($apiStation);

        $api = new Api();

        $return = $api->getStations($apiStations)->getReturn();

        $this->isAmbiguous($return);

        foreach ($return->getStations() as $station) {
            /** @var \Ratp\Station $station */
            $stations[] = [
                'slug' => NamesHelper::slugify($station->getName()),
                'name' => $station->getName(),
                'id' => $station->getId(),
                'idsNextA' => $station->getIdsNextA(),
                'idsNextR' => $station->getIdsNextR(),
            ];
        }

        return [
            'stations' => $stations
        ];
    }
}
