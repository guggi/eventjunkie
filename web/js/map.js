// var map = L.map('map', {
//     center: [16.37, 48.209],
//     zoom: 13
// }); 
// 
// L.tileLayer('https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png', {
//   maxZoom: 18,
//   attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
//   '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
//   'Imagery © <a href="http://mapbox.com">Mapbox</a>',
//   id: 'examples.map-i875mjb7'
// }).addTo(map);


var map;
var mapOptions;
var geocoder;

function init() {
    initMap();
}

function initMap() {
    var latitude = 48.2081743;
    var longitude = 16.3738189;

    if (jsonMarkerList !== undefined) {
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

    if (jsonMarkerList !== undefined) {
        jsonMarkerList.forEach(function(jsonMarker) {
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

    geocoder.geocode({'address': address}, function(results, status) {
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