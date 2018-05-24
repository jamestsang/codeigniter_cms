/*
 * Version: 1.0
 * Creator: James Tsang@Anglia
 **/

var arg = (function()
    {
        //used to hold the argument/value pairs
        var _scriptArgs=[];

        //parse the scripts url and fill scriptArgs with argumen/value pairs
        var _getScriptArgs=function()
        {
            var url = document.getElementsByTagName("script")[document.getElementsByTagName("script").length-1].src;
            var v, args = url.split("?");
            if(args.length<2) return;
            var w = args[1].split("&");
            for(var i=0;i<w.length;i++)
            {
                v=w[i].split("=")
                _scriptArgs[v[0]]=v[1];
            }
        }

        //get the value associated with the argument (if any).
        var _getScriptArg=function(arg)
        {
            return _scriptArgs[arg];
        }
        return {
            setArgs:function(){
                _getScriptArgs();
            },
            getArg:function(n){
                return _getScriptArg(n);
            }
        }
    })();

arg.setArgs();

var tag_name={
    'map_lng':'map_lng',
    'map_lat':'map_lat',
    'center_lng':'center_lng',
    'center_lat':'center_lat',
    'zoom':'zoom',
    'heading':'heading',
    'pitch':'pitch',
    'map_type':'map_type',
    'street_view_zoom':'street_view_zoom'
};

var geocoder;

var map;

var markersArray= [];

var infoBox = [];

var panorama;

var params = {
    'map_lng':114.1751677,
    'map_lat':22.2965011,
    'center_lng':114.1751677,
    'center_lat':22.2965011,
    'zoom':14,
    'heading':'heading',
    'pitch':'pitch',
    'map_type':'map_type',
    'street_view_zoom':'street_view_zoom'
};

function paramsMap(){
    if($("input[name='map_lat']").length && $("input[name='map_lat']").val() != ""){
        params.map_lat = +$("input[name='map_lat']").val();
    }

    if($("input[name='map_lng']").length && $("input[name='map_lng']").val() != ""){
        params.map_lng = +$("input[name='map_lng']").val();
    }
    if($("input[name='center_lng']").length && $("input[name='center_lng']").val() != ""){
        params.center_lng = +$("input[name='center_lng']").val();
    }
    if($("input[name='center_lat']").length && $("input[name='center_lat']").val() != ""){
        params.center_lat = +$("input[name='center_lat']").val();
    }
    if($("input[name='zoom']").length && $("input[name='zoom']").val() != ""){
        params.zoom = +$("input[name='zoom']").val();
    }
}

function initialize() {
    geocoder = new google.maps.Geocoder();

    var latlng = new google.maps.LatLng(params.map_lat,params.map_lng);
    var centerlatlng = new google.maps.LatLng(params.center_lat,params.center_lng);
    var myOptions = {
        zoom: parseInt(params.zoom),
        center: centerlatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        streetViewControl:arg.getArg("has_street")==1?true:false
    }

    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    addMarker(latlng);
    google.maps.event.addListener(map, 'rightclick', function(latLng){
        addMarker(latLng.latLng);
    });

    panorama = map.getStreetView();

    if(arg.getArg("map_type")=='Street'){
        panorama.setPosition(new google.maps.LatLng(params.map_lat,params.map_lng));
        panorama.setPov({
           heading:+arg.getArg("heading"),
           zoom:+arg.getArg("street_view_zoom"),
           pitch:+arg.getArg("pitch")}
        );
        panorama.setVisible(true);
    }else{
        panorama.setVisible(false);
    }

    google.maps.event.addListener(map, 'center_changed', function() {
        var center = (map.getCenter()+'').replace('(','').replace(')', '');
        center = center.split(',');
        $('input[name='+tag_name.center_lat+']').val($.trim(center[0]));
        $('input[name='+tag_name.center_lng+']').val($.trim(center[1]));
    });

    google.maps.event.addListener(map, 'zoom_changed', function() {
        var zoomLevel = map.getZoom();
        $('input[name='+tag_name.zoom+']').val(zoomLevel);
    });

    google.maps.event.addListener(panorama, 'position_changed', function() {
        ll = (panorama.getPosition()+'').replace('(','').replace(')', '').split(',');
        chooseMap(ll[0],ll[1]);
    });

    google.maps.event.addListener(panorama, 'pov_changed', function() {
        $('input[name='+tag_name.heading+']').val($.trim(panorama.getPov().heading));
        $('input[name='+tag_name.pitch+']').val($.trim(panorama.getPov().pitch));
        $('input[name='+tag_name.street_view_zoom+']').val($.trim(panorama.getPov().zoom));
    });

    google.maps.event.addListener(panorama, 'visible_changed', function() {
        if (panorama.getVisible()) {
            $('input[name='+tag_name.map_type+']').val('Street');
        } else {
            $('input[name='+tag_name.map_type+']').val('Map');
        }
    });
}

function clearOverlays() {
    if (markersArray) {
        for (var i = 0; i < markersArray.length; i++ ) {
            markersArray[i].setMap(null);
        }
    }
}

function closeInfoWindow(){
    if(infoBox){
        for(var i = 0; i < infoBox.length; i++){
            infoBox[i].close();
        }
    }
}

function addMarker(location) {
    marker = new google.maps.Marker({
        position: location,
        animation: google.maps.Animation.DROP,
        map: map,
        draggable:true
    });
    setInfoBoxContent(location, marker);
    google.maps.event.addListener(marker, "dragstart", function() {
        closeInfoWindow();
    });
    google.maps.event.addListener(marker, 'dragend', function() {
        setInfoBoxContent(this.getPosition(), this);
    });
    markersArray.push(marker);
}

function setInfoBoxContent(location, marker){
    ll = (location+'').replace('(','').replace(')', '').split(',');
    popupContent='<a href="" onclick="return chooseMap('+ll[0]+', '+ll[1]+')">Choose this</a><br />'+location+'<br />';
    geocoder.geocode({
        'latLng': location
    }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if (results) {
                for(var key in results){
                   popupContent+=results[key].formatted_address+'<br />';
                }
            }
        }
        infowindow = new google.maps.InfoWindow({
            content:popupContent
        });
        google.maps.event.addListener(marker, 'click', function(){
            closeInfoWindow();
            infowindow.open(map,this);
        });
        infoBox.push(infowindow);
    });
}


function codeAddress() {

    var address = document.getElementById("address").value;

    geocoder.geocode( {
        'address': address
    }, function(results, status) {

        if (status == google.maps.GeocoderStatus.OK) {
            clearOverlays();
            for(var key in results){
                addMarker(results[key].geometry.location);
            }
            map.setCenter(results[0].geometry.location);
        } else {
            alert("No Result: " + status);
        }
    });

}

function chooseMap(lat, lng){
    $('input[name='+tag_name.map_lat+']').val($.trim(lat));
    $('input[name='+tag_name.map_lng+']').val($.trim(lng));
    return false;
}

$(window).load(function(){
    if($('.map-finder').length==1){
        initialize();
        $('.centerMap').click(function(){
            latlng = new google.maps.LatLng($('input[name='+tag_name.map_lat+']').val(),$('input[name='+tag_name.map_lng+']').val());
            map.setCenter(latlng);
        });
    }
});

$(function(){
    if($('.map-finder').length==1){
        $('.map-finder').html('<p><strong>The search function may not accurate. Please use right mouse place marker and click marker to make a action.</strong></p><br />'+
            '<input id="address" type="textbox" value="" class="inputStyle">&nbsp;'+
            '<input type="button" value="Search" onclick="codeAddress()" class="buttonStyle">&nbsp;<input type="button" value="Center" class="buttonStyle centerMap"><br /><br /><div id="map_canvas" style="height:500px;width:100%; border:1px solid #666;"></div>');
    }
    paramsMap();
});
