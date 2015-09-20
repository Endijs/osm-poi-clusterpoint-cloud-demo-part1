<?php
/**
 * Easy OpenStreetMap POI maps with Clusterpoint Cloud
 *
 * Clusterpoint config
 *
 * @author    Endijs Lisovskis <endijs@lisovskis.com>
 * @copyright 2015 Endijs Lisovskis <endijs@lisovskis.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 */

$cps_config = [
    'connection' => [
        'tcps://cloud-eu-0.clusterpoint.com:9008',
        'tcps://cloud-eu-1.clusterpoint.com:9008',
        'tcps://cloud-eu-2.clusterpoint.com:9008',
        'tcps://cloud-eu-3.clusterpoint.com:9008',
    ],
    'database' => 'public::osm_poi',
    'account' => '?', // set to your account
    'username' => '?', // set to your username
    'password' => '?', // set to user password
];
