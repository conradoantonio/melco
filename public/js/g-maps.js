    $('input.search-box').keydown(function(event) {
        if( event.keyCode == 13 ) {
            event.preventDefault();
            return false;
        }
    });

    var map;
    var marker;
    var center;
    function initMap() {

        center = getPosicion();
        var elem = document.getElementById("map");
        if ( center == null ) {
            center = {lat: 20.676580, lng: -103.34785};
            if ( navigator.geolocation ) {
                navigator.geolocation.getCurrentPosition(function (pos) {
                    center = {lat: pos.coords.latitude, lng: pos.coords.longitude};
                    drawMap(elem, center);
                    setPosicion(center);
                }, function () {
                    drawMap(elem, center);
                });
            } else {
                drawMap(elem, center);
            }
        } else {
            drawMap(elem, center);
        }
    }

    function drawMap(elem, center) {
        map = new google.maps.Map(elem, {
            center: center,
            zoom: 14
        });
        marker = new google.maps.Marker({
            position: center,
            map: map,
            animation: google.maps.Animation.DROP,
            title: 'Mueve el mapa'
        });
        var searchBox = new google.maps.places.SearchBox(document.getElementById('search-box'));

        google.maps.event.addListener(searchBox, 'places_changed', function() {
            var places = searchBox.getPlaces();
            var bounds = new google.maps.LatLngBounds();
            var i, place;

            for (i=0; place=places[i]; i++) {
                bounds.extend(place.geometry.location);
                marker.setPosition(place.geometry.location);
            }

            map.fitBounds(bounds);
            map.setZoom(14);
        })
        map.addListener('center_changed', function () {
            var p = map.getCenter();
            marker.setPosition({lat: p.lat(), lng: p.lng()});
            setPosicion({lat: p.lat(), lng: p.lng()});
        });
    }

    function setPosicion(center) {
        $("#latitude").val(center.lat);
        $("#longitude").val(center.lng);
    }

    function getPosicion() {
        if ( $("#latitude").val() != "" ) {
            return {lat: parseFloat($("#latitude").val()), lng: parseFloat($("#longitude").val())};
        }
        return null;
    }
    