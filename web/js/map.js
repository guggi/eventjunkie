var map;
var mapOptions;
var geocoder;
var zoom = 12;

function init() {
    initMap();
}

function initMap() {
    var latitude = 48.2081743;
    var longitude = 16.3738189;

    if (typeof jsonMarkerList !== "undefined") {
        if (typeof jsonMarkerList[0] !== "undefined") {
            latitude = jsonMarkerList[0].latitude;
            longitude = jsonMarkerList[0].longitude;
        }
    }

    if (typeof streetZoom !== "undefined") {
        zoom = streetZoom;
    }


    geocoder = new google.maps.Geocoder();
    mapOptions = {
        zoom: zoom,
        center: new google.maps.LatLng(latitude, longitude),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map"), mapOptions);

    if (typeof jsonMarkerList !== "undefined") {
        jsonMarkerList.forEach(function (jsonMarker) {
            createMarker(jsonMarker);
        });
    }
}

function createMarker(jsonMarker) {
    var markerList = new Map();
    var infoWindowList = new Map();

    var marker = new google.maps.Marker({
        title: jsonMarker.name,
        position: new google.maps.LatLng(jsonMarker.latitude, jsonMarker.longitude),
        map: map
    });

    markerList.set(jsonMarker.id, marker);

    google.maps.event.addListener(markerList.get(jsonMarker.id), 'click', function() {
        infoWindowList.get(jsonMarker.id).open(map, markerList.get(jsonMarker.id));
    });

    var infoWindow = new google.maps.InfoWindow({
        content: '<div>' +
        '<strong><a href="/index.php?r=event/view&id=' + jsonMarker.id + '">' + jsonMarker.name + '</a></strong><br>' +
            '<em>' + jsonMarker.start_date + ' - ' + jsonMarker.end_date + '</em><br>' +
            jsonMarker.address +
        '</div>'
    });

    infoWindowList.set(jsonMarker.id, infoWindow);
}

function codeAddress() {
    var address = document.getElementById("address").value;
    var marker;

    geocoder.geocode({'address': address}, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK && results[0] !== undefined) {
            map.setCenter(results[0].geometry.location);
            if (marker) {
                marker.position = results[0].geometry.location;
            } else {
                marker = new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location
                });
            }
        } else {
            alert("Geocode was not successful for the following reason: " + status);
        }
    });
}
