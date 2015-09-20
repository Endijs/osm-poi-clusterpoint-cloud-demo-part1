/*!
 * Easy OpenStreetMap POI maps with Clusterpoint Cloud
 *
 * @author    Endijs Lisovskis <endijs@lisovskis.com>
 * @copyright 2015 Endijs Lisovskis <endijs@lisovskis.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 */

$(document).ready(function() {

    var map;
    var circle;
    var radius;
    var center;
    var markers = [];
    var infoWindows = [];

    function initMap() {

        center = {lat: 56.9554752, lng: 24.1250764};
        map = new google.maps.Map(document.getElementById('map'), {
            center: center,
            zoom: 14
        });
        map.addListener('click', function(e) {
            center.lat = e.latLng.lat();
            center.lng = e.latLng.lng();
            circle.setCenter(center);
            reloadMarkers();
        });

        radius = parseInt($('#distance').val());
        $('#distance-m').text(radius + 'm');

        circle = new google.maps.Circle({
            strokeColor: '#CCCCCC',
            strokeOpacity: 0.8,
            strokeWeight: 1,
            fillColor: '#E7FF53',
            fillOpacity: 0.25,
            map: map,
            center: center,
            clickable: true,
            draggable: true,
            radius: radius
        });
        circle.addListener('dragend', function() {
            reloadMarkers();
        });
        circle.addListener('radius_changed', function() {
            reloadMarkers();
        });

        $('#search-phrase').blur(function() {
            reloadMarkers();
        }).keyup(function (e) {
            if (e.keyCode == 13) {
                $(this).blur();
            }
        });

        $('#pager').bind('change', function() {
            reloadMarkers(true);
        });

        reloadMarkers();
    }

    function reloadMarkers(keepPage) {

        var page = typeof keepPage !== 'undefined' ? $('#pager').val() : 1;

        var center = circle.getCenter();

        var data = {
            q: $('#search-phrase').val(),
            page: page,
            amenities: []
        };
        $('input:checked').each(function() {
            data.amenities.push($(this).attr('value'));
        });

        $.ajax({
            url: '/api/list/' + center.lat() + '/' + center.lng() + '/' + circle.getRadius() + '/',
            data: data,
            error: function() {
                alert('There was some error. Please try again.');
            },
            success: function(d) {
                deleteMarkers();
                $('.amenity').unbind('click');
                $('#total').text(d.total);
                $('#from').text(d.from);
                $('#to').text(d.to);
                $.each(d.list, function(index, poi) {
                    var marker = new google.maps.Marker({
                        position: new google.maps.LatLng(poi.lat, poi.lng),
                        map: map,
                        title: poi.tags.name
                    });
                    var infoWindowContent = '<div id="info-window-content">';
                    $.each(poi.tags, function(index, tag) {
                        infoWindowContent += pretifyTags(index, tag) + '<br />';
                    });
                    infoWindowContent += '<div id="osm-copyright">© <a href="http://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap contributors</a></div></div>';
                    var infoWindow = new google.maps.InfoWindow({
                        content: infoWindowContent
                    });
                    marker.addListener('click', function() {
                        closeInfoWindows();
                        infoWindow.open(map, marker);
                    });
                    infoWindows.push(infoWindow);
                    markers.push(marker);
                });
                $('#amenities').html('');
                $.each(d.amenities, function(index, amenity) {
                    var checked = (data.amenities.indexOf(index) != -1) ? 'checked="checked"' : '';
                    var txt = '<div class="checkbox"><label><input type="checkbox" value="' + index + '" name="amenity" class="amenity" ' +checked+ '>' + amenity + '</label></div>';
                    $('#amenities').append(txt);
                });
                $('.amenity').click(function() {
                    reloadMarkers();
                });
                var pager = $('#pager');
                pager.empty();
                for (var i = 1; i <= d.pages; i++) {
                    pager.append(
                        $('<option></option>').val(i).html(i)
                    );
                }
                pager.val(d.current_page);
            }
        });

        function pretifyTags(tag, value) {
            tag = tag.replace(/_/g, ' ');
            return '<strong>' + tag + ':</strong> ' + value;
        }
    }
    function closeInfoWindows() {
        for (var i = 0; i < infoWindows.length; i++) {
            infoWindows[i].close();
        }
    }
    function deleteMarkers() {
        closeInfoWindows();
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
        }
        markers = [];
    }

    initMap();
    $('#distance').bind('change', function() {
        radius = parseInt($(this).val());
        $('#distance-m').text(radius + 'm');
        circle.setRadius(radius);
    });
});