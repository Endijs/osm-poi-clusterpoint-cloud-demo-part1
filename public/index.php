<?php
/**
 * Easy OpenStreetMap POI maps with Clusterpoint Cloud
 *
 * @author    Endijs Lisovskis <endijs@lisovskis.com>
 * @copyright 2015 Endijs Lisovskis <endijs@lisovskis.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 */

require_once '../vendor/autoload.php';
require_once '../cfg/cps.php';
require_once '../cfg/google.php';

$app = new \Slim\Slim([
        'debug'          => false,
        'templates.path' => '../templates',
        'cps'            => $cps_config,
        'google'         => $google_config,
    ]
);

// route for index page
$app->get('/', function() use ($app) {
    $google_config = $app->config('google');
    $app->view->setData('maps_api_key', $google_config['maps_api_key']);
    $app->render('index.php');
});

// route for ajax calls
$app->get("/api/list/:lat/:lng/:radius/*", function ($lat, $lng, $radius) use ($app) {

    $config = $app->config('cps');

    $lat = (float)$lat;
    $lng = (float)$lng;
    $radius = (int)$radius;
    $q = trim((string)$app->request->get('q', '')); // search phrase
    $amenities = (array)$app->request->get('amenities', []); // amenities filter
    $page = (int)$app->request->get('page', 1); // page

    if ($page < 1) {
        $page = 1;
    }
    // structure of response
    $objects = [
        'total' => 0,
        'from' => 0,
        'to' => 0,
        'pages' => 0,
        'current_page' => 0,
        'list'  => [],
        'amenities' => [],
    ];

    $cps_connection = new CPS_Connection(
                            new CPS_LoadBalancer($config['connection']),
                            $config['database'],
                            $config['username'],
                            $config['password'],
                            'document', '//document/id',
                            ['account' => $config['account']]
    );

    $docs = 50;
    $offset = $docs * ($page - 1);
    $radius = $radius / 1000; // radius is received in meters, but request to Clusterpoint should be in km

    // Shape (circle) definition. This will need to be attached to search query.
    $circle = CPS_CircleDefinition('circle', [$lat, $lng], $radius . ' km', 'lat', 'lon');

    $query = CPS_Term(' ><circle'); // search inside of defined circle

    if ($q !== '') {
        $query .= CPS_Term($q); // if there was search phrase, attach it to the query
    }

    $query_without_amenities = $query;
    $aggregate = 'DISTINCT tags.amenity'; // aggregate distinct values of amenity tag

    if (count($amenities)) {
        if (array_search('-', $amenities) !== false) {
            // if there is amenity with value "-", this means that "Amenity is not set" was selected
            // lets replace - with ="" which tells Clusterpoint to look for documents for which this tag does not exist
            $amenities[array_search('-', $amenities)] = '=""';
        }
        // add amenities filter to search request
        $query .= CPS_Term('{ ' . implode(' ', $amenities) . ' }', 'tags/amenity');
    }

    $search_request = new CPS_SearchRequest($query, $offset, $docs);
    $search_request->setParam('list', '<document>yes</document>'); // search should return full documents
    $search_request->setShape($circle); // attach previously defined circle definition

    if ($query_without_amenities == $query)
    {
        // if there was no amenities filter, we can aggregate data together with search request
        $search_request->setAggregate($aggregate);
    }
    $search_response = $cps_connection->sendRequest($search_request);

    if ($search_response->getHits() > 0) {
        $objects['total'] = $search_response->getHits();
        $objects['from'] = $search_response->getFrom() + 1; // getFrom() returns offset. We want to show offset +1
        $objects['to'] = $search_response->getTo();

        $objects['pages'] = ceil($objects['total'] / $docs);
        $objects['current_page'] = $search_response->getFrom() / $docs + 1;

        foreach ($search_response->getDocuments(DOC_TYPE_ARRAY) as $id => $document) {
            ksort($document['tags']); // sort tags to make it more easy to read in map's info windows
            $objects['list'][] = [
                'id' => $document['id'],
                'tags' => $document['tags'],
                'lat' => $document['lat'],
                'lng' => $document['lon'],
            ];
        }
    }

    if ($query_without_amenities == $query) {
        // if we aggregated data when we sent search request, lets get aggregation result
        $aggregated_data = $search_response->getAggregate(DOC_TYPE_ARRAY);
    }
    else {
        // if we were not able to aggregate data when we sent search request, lets send one more request
        $search_request->setQuery($query_without_amenities); // set query to be without amenities
        $search_request->setAggregate($aggregate); // add aggregate
        $search_response = $cps_connection->sendRequest($search_request);
        $aggregated_data = $search_response->getAggregate(DOC_TYPE_ARRAY); // get aggregation result
    }

    // It is possible to send several aggregation queries in one request. Pop out data for first request
    // which in our case is the only request.
    $aggregated_data = array_pop($aggregated_data);
    foreach ($aggregated_data as $value) {
        $value = array_pop($value);
        if (empty($value)) {
            // We do not want empty values in results
            continue;
        }
        // Replace _ with empty space and capitalize first letter to make values more readable
        $objects['amenities'][$value] = ucfirst(str_replace('_', ' ', $value));
    }
    ksort($objects['amenities']);

    $app->response->headers->set('Content-Type', 'application/json');
    $app->response->setBody(json_encode($objects));
    return $app->response;
});

$app->run();