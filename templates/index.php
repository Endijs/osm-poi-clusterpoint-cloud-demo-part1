<!DOCTYPE html>
<html>
<head>
    <title>Easy OSM POI maps with Clusterpoint Cloud and Google Maps API</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
    <style>
        html, body {
            height: 100%;
        }
        #container {
            height: 100%;
        }
        #map {
            float: left;
            height: 100%;
            width: calc(100% - 370px);
        }
        #panel {
            float: left;
            width: 370px;
            padding: 10px;
            overflow-y: auto;
            height: 100%;
            overflow-x: hidden;
        }
        #info-window-content {
            text-transform: capitalize;
        }
        #osm-copyright {
            text-transform: none;
            margin-top: 5px;
        }
        #osm-copyright a {
            color: black;
        }
        #osm-copyright a:hover {
            text-decoration: none;
        }
        #amenities {
            border-top: 1px solid #ccc;
        }
    </style>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&key=<?= $maps_api_key ?>"></script>
    <script src="/js/scripts.js"></script>
</head>
<body>
<div id="container">
    <div id="map"></div>
    <div id="panel" class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <h1>OSM POI Maps</h1>
                </div>
                <p class="text">Center: <span id="center">Lat: 56.9554752; Long: 24.1250764</span></p>
                <p class="text">To change center click on map or drag circle</p>
                <div class="form-group">
                    <label for="search-phrase">Search phrase:</label>
                    <input type="text" class="form-control" id="search-phrase">
                </div>
                <label for="distance">Meters: </label>
                <input type="range" name="distance" id="distance" min="100" max="3000" />
                <span id="distance-m"></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h2>Search results</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <p class="text"><strong>Total objects in circle:</strong> <span id="total"></span></p>
                <p class="text">Showing from <span id="from"></span> to <span id="to"></span></p>
            </div>
            <div class="col-md-4">
                <select class="form-control" id="pager"></select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h3>Amenities filter:</h3>
                <div class="checkbox"><label><input type="checkbox" value="-" name="amenity" class="amenity">Amenity is not set</label></div>
                <div id="amenities"></div>
            </div>
        </div>


    </div>
</div>
</body>
</html>