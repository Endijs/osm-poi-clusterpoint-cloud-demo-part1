# Easy OpenStreetMap POI maps with Clusterpoint Cloud

This demo application demonstrates how to retrieve OpenStreetMap (OSM) Points of interest (POI)
data from [Clusterpoint Cloud](http://clusterpoint.com) and how to put such data on Google Maps.

## LIVE DEMO

You can access live demo at: [https://osmpoi.endijs.com](https://osmpoi.endijs.com)

## INSTALLATION

- [Sign up](https://developers.google.com/maps/signup) for Google Maps API Key
- [Sign up](https://cloud.clusterpoint.com/#authentication/signup) for Clusterpoint Cloud account 
- Rename ```/cfg/cps-SAMPLE.php``` to ```cps.php``` and fill with your Clusterpoint Cloud credentials
- Rename ```/cfg/google-SAMPLE.php``` to ```google.php``` and fill with your Google Maps API key

Use [Composer](https://getcomposer.org/) to install project dependencies

```
$ composer install
```

If you are not able to use Composer you can set up dependencies manually: 

- Download [Clusterpoint PHP Client](https://github.com/clusterpoint/php-client-api) and save it in project
 directory.
- In ```/public/index.php``` remove ```require_once '../vendor/autoload.php';``` and add
 ```require_once('/path_to_lib/cps_api.php');``` . Replace ```/path_to_lib/``` with actual path to 
 Clusterpoint PHP Client.
- Follow [Slim framework](http://docs.slimframework.com/start/get-started/) manual installation instructions.


    