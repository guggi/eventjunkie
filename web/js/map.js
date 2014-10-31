var map;
var mapOptions;
var geocoder;

function init() {
    initMap();
}

function initMap() {
    var latitude = 48.2081743;
    var longitude = 16.3738189;

    if (typeof jsonMarkerList !== "undefined") {
        latitude = jsonMarkerList[0].latitude;
        longitude = jsonMarkerList[0].longitude;
    }

    geocoder = new google.maps.Geocoder();
    mapOptions = {
        zoom: 12,
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
    marker = new google.maps.Marker({
        position: new google.maps.LatLng(jsonMarker.latitude, jsonMarker.longitude),
        map: map
    });
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
