
//import L from "leaflet";
require('webpack-jquery-ui/autocomplete');
//require("./map/map.js");
/*(function ($) {

   

})(jQuery);

var cache = {};
$(function () {
    $("#bird").autocomplete({
        "source": function (request, response) {
            observationsSearch(request.term, response);
        },
        "minLength": 3,
        select: function (event, ui) {
            
            return false;

        },
        focus: function () {
            return false;
        }
    });
});
function observationsSearch(term, response) {
    if (cache.hasOwnProperty(term.substring(0, 3))) {

        var results = filterResults(term, cache[term.substring(0, 3)]);

        response(results);
    } else {
        $.getJSON("/get-bird-list",
                {"term": term},
                function (data) {

                    cache[term] = data;
                    data.map(item => {
                        item['label'] =
                                (item.birdName != "")
                                ? '<div><span class="main">' + item.birdName + '</span><span class="latin">' + item.birdLatinName + '</span></div>'
                                : '<div><span class="main">' + item.birdLatinName + '</span></div>';

                    })
                    response(filterResults(term, data));
                })
    }
    //console.log("recherche pour l'id: "+id )
}
function filterResults(term, data) {
    var termR = RegExp(term, "i");
    return data.filter(item => termR.test(item.birdName) || termR.test(item.birdLatinName));
}
function getObservationsByBird(birdId){
     $.getJSON("/get-bird-observations/"+birdId,
     function(Observations){
            photoLayer.clear();
            var id = $(this).val();
            photoLayer.add(birds[id].data).addTo(mymap);
     });
     
}*/








(function ($) {

 var proto = $.ui.autocomplete.prototype,
            initSource = proto._initSource;

function filter(array, term) {
    var matcher = new RegExp($.ui.autocomplete.escapeRegex(term), "i");
    return $.grep(array, function (value) {
        return matcher.test($("<div>").html(value.label || value.value || value).text());
    });
}

$.extend(proto, {
    _initSource: function () {
        if (this.options.html && $.isArray(this.options.source)) {
            this.source = function (request, response) {
                response(filter(this.options.source, request.term));
            };
        } else {
            initSource.call(this);
        }
    },

    _renderItem: function (ul, item) {

        return $("<li></li>")
                .data("item.autocomplete", item)
                .append($("<a></a>").html(item.label))
                .appendTo(ul);

    },
    _resizeMenu: function () {
        var ul = this.menu.element;
        ul.outerWidth(this.element.outerWidth());
    }
});



var birdApp = function(element){
    var _this = this;
    
    this.cache = [];
    
    this.birdId = null;
    this.container = element;
    this.observeMode = false;
    this.modal = null;
    this.L = L; 
    this.container.find(".search").autocomplete({
        "source": function(request, response){
            _this.birdSearch(request.term, response)
        },
        "minLength": 3,
        select: function (event, ui) { return false; },
        focus: function () { return false; }
    });
    
    
    
    this.mapNode = this.container.find(".map");
    var franceBounds = [
        [51.39920565355378, -5.537109375000001 ],
        [42.06560675405716, 8.613281250000002 ] 
    ];
    this.map = this.L.map(this.mapNode[0]).fitBounds(franceBounds);
    
    this.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
	maxZoom: 19,
	attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(this.map);
    
    this.mapNode.on('animationend animationend webkitAnimationEnd oanimationend MSAnimationEnd', 
                ".ripple",  
                function() {
                    $(this).remove();
                });
    this.map.on('click', function(e) {   
        if(_this.observeMode){  
            
            let ripple = $('<div class="ripple">');
            
            ripple.css({top: e.containerPoint.y, left: e.containerPoint.x})
            $(this._container).append(ripple);
            _this.openSightingModal(e.latlng);
         }   
            
    });
    
    this.birdMarkersLayer = L.photo.cluster().on('click', function (evt) {
            var photo = evt.layer.photo,
                    template = '<img src="{url}"/></a><p>{caption}</p>';
            if (photo.video && (!!document.createElement('video').canPlayType('video/mp4; codecs=avc1.42E01E,mp4a.40.2'))) {
                    template = '<video autoplay controls poster="{url}"><source src="{video}" type="video/mp4"/></video>';
            }; 
            evt.layer.bindPopup(L.Util.template(template, photo), {
                    className: 'leaflet-popup-photo',
                    minWidth: 400
            }).openPopup();
    });
    
    return this;
}

birdApp.prototype.birdSearch = function(term, response) {
    var _this = this;
    
    if (_this.cache.hasOwnProperty(term.substring(0, 3))) {

        var results = _this.filterResults(term, _this.cache[term.substring(0, 3)]);

        response(results);
    } else {
        $.getJSON("/get-bird-list",
                {"term": term},
                function (data) {

                    _this.cache[term] = data;
                    data.map(item => {
                        item['label'] =
                                (item.birdName != "")
                                ? '<div><span class="main">' + item.birdName + '</span><span class="latin">' + item.birdLatinName + '</span></div>'
                                : '<div><span class="main">' + item.birdLatinName + '</span></div>';

                    })
                    response(_this.filterResults(term, data));
                })
    }
}

birdApp.prototype.observationsSearch = function(){
    $.getJSON("/get-observation",
                this.filters,
                function(data){
                   photoLayer.clear(); 
                });
}

birdApp.prototype.filterResults = function(term, data) {
    var termR = RegExp(term, "i");
    return data.filter(item => termR.test(item.birdName) || termR.test(item.birdLatinName));
}

birdApp.prototype.initSightModal = function(){
    this.sightModal = this.container.find(".sightModal");
    this.sightModal.modal({show:false});
    this.sightModal.on('shown.bs.modal', function () {
        var latlng = $(this).data("latlng")
        window.setTimeout(function(){
            if(!this.sightModal.minimap){
               this.sightModal.minimap = this.L.map(this.sightModal.find(".minimap")[0],{
                    zoomControl:false,
                    scrollWheelZoom:false,
                    touchZoom :false,
                    keyboard: false,
                    dragging :false,
                    doubleClickZoom:false,
                    boxZoom:false
                }).setView(latlng, 13);
                var OpenMapSurfer_Roads = L.tileLayer('https://korona.geog.uni-heidelberg.de/tiles/roads/x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    attribution: 'Imagery from <a href="http://giscience.uni-hd.de/">GIScience Research Group @ University of Heidelberg</a> &mdash; Map data &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(minimap);
                this.sightModal.singleMarker = L.marker(latlng).addTo(minimap);
            }
            else{
                this.sightModal.setView(latlng, minimap.getZoom());
                this.sightModal.singleMarker.setLatLng(latlng);
            }
        },100);
      });
}
birdApp.prototype.openSightModal = function(latLng){
    if(!this.sightModal){
        
    }
}

$.fn.birdApp = function() {
      
      this.birdApp = new birdApp($(this));
      return this;
   };

})(jQuery)

$(".birdAppContainer").birdApp();
