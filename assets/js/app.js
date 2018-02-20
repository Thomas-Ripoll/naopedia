
//import L from "leaflet";

var leafletPhotos = require('leaflet.markercluster');


(function ($) {

    L.Photo = L.FeatureGroup.extend({
        options: {
            icon: {
                iconSize: [40, 40]
            }
        },

        initialize: function (photos, options) {
            L.setOptions(this, options);
            L.FeatureGroup.prototype.initialize.call(this, photos);
        },

        addLayers: function (photos) {
            if (photos) {
                for (var i = 0, len = photos.length; i < len; i++) {
                    this.addLayer(photos[i]);
                }
            }
            return this;
        },

        addLayer: function (photo) {
            L.FeatureGroup.prototype.addLayer.call(this, this.createMarker(photo));
        },

        createMarker: function (photo) {
            var marker = L.marker(photo, {
                icon: L.divIcon(L.extend({
                    html: '<div style="background-image: url(' + photo.thumbnail + ');"></div>​',
                    className: 'leaflet-marker-photo'
                }, photo, this.options.icon)),
                title: photo.caption || ''
            });
            marker.photo = photo;
            return marker;
        }
    });

    L.photo = function (photos, options) {
        return new L.Photo(photos, options);
    };

    if (L.MarkerClusterGroup) {
        var degrees = [10, 20, 40, 80, 160];


        L.Photo.Cluster = L.MarkerClusterGroup.extend({
            options: {
                featureGroup: L.photo,
                maxClusterRadius: 100,
                showCoverageOnHover: false,
                iconCreateFunction: function (cluster) {
                    var count = cluster.getChildCount();
                    var range = 0;
                    for (var i = degrees.length - 1; i >= 0; i--) {
                        if (count < degrees[i]) {
                            range = i;
                        }
                    }
                    return new L.DivIcon(L.extend({
                        className: 'leaflet-marker-heat heat-' + range,
                        html: '<div>' + count + '</div>'
                    }, this.icon));
                },
                icon: {
                    iconSize: [40, 40]
                }
            },

            initialize: function (options) {
                options = L.Util.setOptions(this, options);
                L.MarkerClusterGroup.prototype.initialize.call(this);
                this._photos = options.featureGroup(null, options);
            },

            add: function (photos) {
                this.addLayer(this._photos.addLayers(photos));
                return this;
            },

            clear: function () {
                this._photos.clearLayers();
                this.clearLayers();
            }

        });

        L.photo.cluster = function (options) {
            return new L.Photo.Cluster(options);
        };

    }


    var birdApp = function (element) {
        var _this = this;

        this.cache = [];
        this.filters = {};
        this.birdId = null;
        this.container = element;
        this.observeMode = false;
        this.modal = null;
        this.L = L;




        this.mapNode = this.container.find(".map");
        var franceBounds = [
            [51.39920565355378, -5.537109375000001],
            [42.06560675405716, 8.613281250000002]
        ];
        this.map = this.L.map(this.mapNode[0]).fitBounds(franceBounds);

        this.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(this.map);

        this.mapNode.on('animationend animationend webkitAnimationEnd oanimationend MSAnimationEnd',
                ".ripple",
                function () {
                    $(this).remove();
                });
        this.map.on('click', function (e) {
            if (_this.observeMode) {

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
            }
            ;
            evt.layer.bindPopup(L.Util.template(template, photo), {
                className: 'leaflet-popup-photo',
                minWidth: 400
            }).openPopup();
        });

        return this;
    }



    birdApp.prototype.initSightModal = function () {
        this.sightModal = this.container.find(".sightModal");
        this.sightModal.modal({show: false});
        this.sightModal.on('shown.bs.modal', function () {
            var latlng = $(this).data("latlng")
            window.setTimeout(function () {
                if (!this.sightModal.minimap) {
                    this.sightModal.minimap = this.L.map(this.sightModal.find(".minimap")[0], {
                        zoomControl: false,
                        scrollWheelZoom: false,
                        touchZoom: false,
                        keyboard: false,
                        dragging: false,
                        tap: false,
                        doubleClickZoom: false,
                        boxZoom: false
                    }).setView(latlng, 13);
                    var OpenMapSurfer_Roads = L.tileLayer('https://korona.geog.uni-heidelberg.de/tiles/roads/x={x}&y={y}&z={z}', {
                        maxZoom: 20,
                        attribution: 'Imagery from <a href="http://giscience.uni-hd.de/">GIScience Research Group @ University of Heidelberg</a> &mdash; Map data &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(this.sightModal.minimap);
                    this.sightModal.singleMarker = L.marker(latlng).addTo(this.sightModal.minimap);
                } else {
                    this.sightModal.setView(latlng, this.sightModal.minimap.getZoom());
                    this.sightModal.singleMarker.setLatLng(latlng);
                }
            }, 100);
        });
    }
    birdApp.prototype.openSightModal = function (latLng) {
        if (!this.sightModal) {

        }
    }
    birdApp.prototype.updateFilters = function (data) {
        $.extend(this.filters, data);
        console.log(data);
        this.searchBird();
        /*if(this.filters.birdId == null && this.filters.dates == null){
         
         }*/
    }
    birdApp.prototype.searchBird = function () {
        var _this = this;
        $.getJSON("/get-observations",
                this.filters,
                function (data) {
                    console.log(data)
                    
                    _this.birdMarkersLayer.clear();
                    _this.birdMarkersLayer.add(data).addTo(_this.map);
                });
    }

    $.fn.birdApp = function () {

        this.birdApp = new birdApp($(this));
        return this;
    };

})(jQuery)



var birdApp = $(".birdAppContainer").birdApp();


$(".birdAppContainer .search").birdSearch(function (item) {
    $(this).val("");
    birdApp.birdApp.updateFilters({"bird": item.birdId})
});
