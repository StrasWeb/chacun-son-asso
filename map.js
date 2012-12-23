/*global OpenLayers*/
/*jslint browser: true */

function getUrlVars() {
    'use strict';
    var vars = {}, parts;
    parts = window.location.href.replace(/[?&]+(\w+)=([\w+%]*)/gi, function (m, key, value) {
        vars[key] = value;
    });
    return vars;
}
var GET = getUrlVars();

var error = function () {
    'use strict';
    document.getElementById('mapdiv').style.display = 'none';
};

var map = new OpenLayers.Map('mapdiv');
map.addLayer(new OpenLayers.Layer.OSM());

var markers = new OpenLayers.Layer.Markers();
map.addLayer(markers);

var addMarker = function (e) {
    'use strict';
    if (e.target.readyState === e.target.DONE) {
        if (e.target.status === 200 && e.target.response !== null) {
            var geo = JSON.parse(e.target.response)[0], lonLat;
            if (geo) {
                lonLat = new OpenLayers.LonLat(geo.lon, geo.lat).transform(new OpenLayers.Projection('EPSG:4326'), map.getProjectionObject());
                markers.addMarker(new OpenLayers.Marker(lonLat));
                //map.addPopup(new OpenLayers.Popup.FramedCloud(null, lonLat, null, '<small>' + e.target.name + '</small>', null, false));
                map.setCenter(lonLat, 13);
            } else {
                error();
            }
        }
    }
};

var getPostCode = function (e) {
    'use strict';
    if (e.target.readyState === e.target.DONE) {
        var city, codepos, client;
        client = new XMLHttpRequest();
        client.onreadystatechange = addMarker;
        if (e.target.status === 200 && e.target.response !== null) {
            city = JSON.parse(e.target.response).Commune;
            if (city.indexOf('PARIS') >= 0) {
                city = 'Paris';
            }
        } else {
            city = e.target.address[3];
        }
        client.open('GET', 'http://nominatim.openstreetmap.org/search/?q=' + e.target.address[0] + ', ' +  city + ', France' + '&format=json&email=contact@strasweb.fr&limit=1');
        client.name = e.target.name;
        client.send();
    }
};

var handler = function (e) {
    'use strict';
    if (e.target.readyState === e.target.DONE) {
        if (e.target.status === 200 && e.target.response !== null) {
            // success!
            var assos, i, client, address;
            assos = JSON.parse(e.target.response).list;
            for (i = 0; i < assos.length; i += 1) {
                if (assos[i].Adresse && assos[i].id == GET.id) {
                    client = new XMLHttpRequest();
                    address = assos[i].Adresse.split('\n');
                    client.open('GET', 'https://rudloff.pro/divers/codepos/?codepos=' + address[3]);
                    client.onreadystatechange = getPostCode;
                    client.address = address;
                    client.name = assos[i]['Sigle ou acronyme'];
                    client.send();
                }
            }
        }
    }
};

var client = new XMLHttpRequest();
client.onreadystatechange = handler;
client.open('GET', 'https://strasweb.fr/animafac/api.php?action=search&asso=' + GET.name);
client.send();


