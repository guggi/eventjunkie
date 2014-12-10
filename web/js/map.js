var map;
var mapOptions;
var geocoder;
var zoom = 12;
var markerList = new Map();
var infoWindowList = new Map();
var codeMarker = null;

function init() {
    initMap();
}

function initMap() {
    var latitude = 48.2081743;
    var longitude = 16.3738189;

    if (typeof jsonMarkerList !== "undefined") {
        if (typeof jsonMarkerList[0] !== "undefined" && jsonMarkerList[0].latitude != null) {
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

    clearMarker();

    if (typeof jsonMarkerList !== "undefined") {
        jsonMarkerList.forEach(function (jsonMarker) {
            createMarker(jsonMarker);
        });
    }
}

function clearMarker() {
    markerList.forEach(function(marker) {
        marker.setMap(null);
    });
    markerList = new Map();
}

function createMarker(jsonMarker) {
    var marker = new google.maps.Marker({
        title: jsonMarker.name,
        position: new google.maps.LatLng(jsonMarker.latitude, jsonMarker.longitude),
        map: map
    });

    markerList.set(jsonMarker.id, marker);

    google.maps.event.addListener(markerList.get(jsonMarker.id), 'click', function () {
        infoWindowList.get(jsonMarker.id).open(map, markerList.get(jsonMarker.id));
    });

    var link;
    var id_string = jsonMarker.id + '';
    if (id_string.substring(0, 7) === 'goabase') {
        link = 'loadgoaparty';
    } else {
        link = 'view';
        jsonMarker.start_date = formatMySQLDate(jsonMarker.start_date);
        jsonMarker.end_date = formatMySQLDate(jsonMarker.end_date);
    }

    var infoWindow = new google.maps.InfoWindow({
        content: '<div>' +
        '<strong><a href="/index.php?r=event/' + link + '&id=' + jsonMarker.id + '">' + jsonMarker.name + '</a></strong><br>' +
        '<em>' + jsonMarker.start_date + ' - ' + jsonMarker.end_date + '</em><br>' +
        jsonMarker.address +
        '</div>'
    });

    infoWindowList.set(jsonMarker.id, infoWindow);
}

function formatMySQLDate(mysql_date) {
    if (mysql_date !== null) {
        var t = mysql_date.split(/[- :]/);
        var date = new Date(t[0], t[1] - 1, t[2], t[3], t[4], t[5]);
        return date.getDay() + '.' + date.getMonth() + '.' + date.getFullYear() + ' ' + date.getHours() + ':' + date.getMinutes();
    } else {
        return null;
    }

}

function codeAddress() {
    clearMarker();
    var address = document.getElementById("event-address").value;

    geocoder.geocode({'address': address}, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK && results[0] !== undefined) {
            map.setCenter(results[0].geometry.location);
            if (codeMarker) {
                codeMarker.setPosition(results[0].geometry.location);
            } else {
                codeMarker = new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location
                });
            }
        } else {
            throw("Geocode was not successful for the following reason: " + status);
        }
    });
}


