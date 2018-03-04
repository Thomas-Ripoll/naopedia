
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
                    html: '<div style="background-image: url(' + (photo.img? photo.img.url : "") + ');"></div>​',
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
        this.birdId = null;
        this.container = element;
        
        this.observeMode = false;
        this.modal = null;
        this.L = L;
        this.isLoading = false;
        this.fakeEmpty = false;
        this.observeMode = false;
        $(".searchBirds").click(function (e) {
            e.preventDefault();
            if (!_this.isLoading) {
                _this.container.addClass("loading");
                _this.updateAddress();
                _this.searchBird();
            }
        });
        $(".filters").on("click", ".filter-item .close", function () {
            var filterNode = $(this).closest(".filter-item");
            _this.filters[filterNode.data("filter")].data = null;
            filterNode.remove();
        });
        $("#addObservation").on("click", function (e) {
            e.preventDefault();
            _this.toggleObserveMode(!_this.observeMode);
        });
        $('#exampleModal').modal({show: false});
        
        this.cache = {
            "all": []
        };
        
        
        this.filters = {
            bird: {
                data: null,
                getUrlString: function () {
                    return this.data.birdSlug;
                },
                getJsonParameters: function () {
                    return (this.data) ? this.data.birdId : null;

                },
                getFilterHtml: function () {
                    return (this.data.birdName != "")
                            ? '<span class="main">' + this.data.birdName + '</span><span class="latin">' + this.data.birdLatinName + '</span>'
                            : '<span class="main">' + this.data.birdLatinName + '</span>';
                },
                setData: function (data) {
                    if (!_this.cache[data.birdId]) {
                        _this.cache[data.birdId] = {};
                    }
                    this.data = data;
                }
            },
            dates: {
                data: null,
                getUrlString: function () {
                    var date1 = (" 00" + this.data[0].getDate()).substr(-2) + "-" + (" 00" + (parseInt(this.data[0].getMonth()) + 1)).substr(-2) + "-" + this.data[0].getFullYear();
                    var date2 = (" 00" + this.data[1].getDate()).substr(-2) + "-" + (" 00" + (parseInt(this.data[1].getMonth()) + 1)).substr(-2) + "-" + this.data[1].getFullYear();
                    return date1 + "to" + date2;
                },
                getJsonParameters: function () {
                    if (!this.data)
                        return null;
                    var startMonth = parseInt(this.data[0].getMonth()) + 1;
                    var startYear = parseInt(this.data[0].getFullYear());

                    var endDate = this.data[1];
                    var endMonth = parseInt(endDate.getMonth()) + 1;
                    var endYear = parseInt(endDate.getFullYear());

                    var limitDate = new Date(endDate.getTime());
                    limitDate = new Date(limitDate.setMonth(limitDate.getMonth() + 1));
                    var limitMonth = parseInt(limitDate.getMonth()) + 1;
                    var limitYear = parseInt(limitDate.getFullYear());

                    var bird = (_this.filters.bird.data) ? _this.filters.bird.data.birdId : "all";

                    var range = [[]];
                    var query = [];
                    var i = 0;
                    do {

                        if (!_this.cache[bird][startYear + (" 00" + startMonth).substr(-2)]) {
                            if (!Array.isArray(range[range.length - 1])) {
                                range.push([]);
                            }
                            if (range[range.length - 1].length <= 1) {
                                range[range.length - 1].push((" 00" + startMonth).substr(-2));
                            } else {
                                range[range.length - 1][1] = (" 00" + startMonth).substr(-2);
                            }
                        }

                        if (_this.cache[bird][startYear + (" 00" + startMonth).substr(-2)]
                                ||
                                startMonth === 12
                                ||
                                (startMonth === parseInt(this.data[1].getMonth()) + 1 && startYear === parseInt(this.data[1].getFullYear()))) {

                            if (Array.isArray(range[range.length - 1])) {
                                range[range.length - 1] = range[range.length - 1].join("-");
                                if ($.trim(range[range.length - 1]) === "") {
                                    range.pop();
                                }
                            }
                        }
                        if (startMonth === 12 || (startMonth === endMonth && startYear === endYear)) {
                            if (range.length) {
                                query.push(startYear + ("(" + range.join(",") + ")"));
                            }
                            range = [[]];
                        }
                        if (startMonth === 12) {
                            startMonth = 1;
                            startYear++;
                        } else {
                            startMonth++;
                        }
                        i++;

                    } while ((startMonth !== limitMonth || startYear !== limitYear));

                    return (query.length) ? query.join("+") : null;
                },
                getFilterHtml: function () {
                    return this.getUrlString().replace(/to/, '<span class="sep"> au </span>')
                },
                setData: function (data) {
                    
                    this.data = data.map(function(date){
                        if(date instanceof Date){
                            return date;
                        }
                        var splitDate = date.split("-");
                        
                        return new Date(splitDate[2],splitDate[1]-1,splitDate[0]); 
                    });
                    console.log(this.data);
                }
            },

        };
        
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
            
            var photo = evt.layer.photo;
            
            var liked = evt.layer.photo.img.liked ? "fas":"far"
            
            var template = '<div class="image-container"><div class="image"><img src="'+photo.img.url+'"/><div class="likes"><span class="badge badge-light likes-count">'+photo.img.countLikes+'</span><i class="'+liked+' fa-heart"></i></div></div><p>{caption}</p><p="extra"><span>par {author} </span><span>le {date}</span><p></div>';
            
            var template = $(L.Util.template(template, photo));
            
           
            $(template).find(".likes").click(function(){
                $.postConnect("/like-image/"+photo.img.id,{}, function(e){

                    template.find(".likes svg[data-fa-i2svg] ")
                            .toggleClass( "fas", e.like )
                            .toggleClass( "far", !e.like );
                    template.find(".likes .likes-count").html(e.countLikes);
                });
            })
            evt.layer.bindPopup(template[0], {
                className: 'leaflet-popup-photo',
                minWidth: 400
            }).openPopup();
        });
        if(this.container.data("birdsloaded")){
            
            $.extend(true,this.cache, this.container.data("birdsloaded").data);
            var onloadfilters = this.container.data("birdsloaded").filters;
            for(var filter  in onloadfilters){
                this.filters[filter].setData(onloadfilters[filter]);
            }
            
        }
        this.renderFilters();
        this.renderBirds();
        return this;
    }

    birdApp.prototype.toggleObserveMode = function (on) {
        this.observeMode = on;
        if (on) {
            this.container.addClass("onObserve");
        } else {

            this.container.removeClass("onObserve");
        }
    }

    birdApp.prototype.openSightingModal = function () {
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
    birdApp.prototype.updateAddress = function () {
        if (history.pushState) {
            var queryString = [];
            for (var key in this.filters) {
                if (this.filters[key].data)
                    queryString.push(key + "=" + this.filters[key].getUrlString());
            }
            var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + queryString.join("&");
            window.history.pushState({path: newurl}, '', newurl);
        }
    }
    birdApp.prototype.getJsonParameters = function () {

        var _this = this;
        var parameters = {};
        var param;
        for (var key in _this.filters) {
            if (param = _this.filters[key].getJsonParameters())
                parameters[key] = param;
        }
        return parameters;
    }
    birdApp.prototype.updateFilters = function (filterName, data) {

        if (!this.filters[filterName])
            throw("le filtre " + filterName + " n'éxiste pas");

        this.filters[filterName].setData(data);
        this.renderFilters();
        
        $(this).trigger(
                {
                    type: "updateFiltersEvent",
                    newfilter: {filterName: data}
                });

    }
    birdApp.prototype.renderFilters = function(){
        $(".filters").html("");
        for (var filter in this.filters) {
            if (this.filters[filter].data) {
                var filterHtml = $('<div class="filter-item ' + filter + '-filter">')
                        .append(
                                $('<div class="inner-filter">')
                                .append($('<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>'))
                                .append(this.filters[filter].getFilterHtml())
                                )
                        .data("filter", filter);

                $(".filters").append(filterHtml);
            }
        }
    }
    birdApp.prototype.searchBird = function () {
        var _this = this;
        this.isLoading = true;
        var parameters = _this.getJsonParameters();
        if (!parameters.dates && _this.filters.dates.data)
        {
            _this.renderBirds();
            _this.isLoading = false;
            _this.container.removeClass("loading");
        } else {
            $.getJSON("/get-observations",
                    parameters,
                    function (data) {
                        $.extend(true, _this.cache[parameters.bird ? parameters.bird : "all"], data);

                        _this.renderBirds();
                        _this.isLoading = false;
                        _this.container.removeClass("loading");
                    });
        }
    }
    birdApp.prototype.renderBirds = function () {
        var data = [];
        if (this.filters.dates.data) {
            var startDate = new Date(this.filters.dates.data[0].getTime());
            var startDay = startDate.getDate();
            var startDateInt = parseInt(startDate.getFullYear() + (" 00" + (parseInt(startDate.getMonth()) + 1)).substr(-2));
            var rotationDate = new Date(startDate.getTime());
            var endDate = new Date(this.filters.dates.data[1].getTime());
            var endDay = endDate.getDate();
            var endDateInt = endDate.getFullYear() + (" 00" + (parseInt(endDate.getMonth()) + 1)).substr(-2);
            var bird = (this.filters.bird.data) ? this.filters.bird.data.birdId : "all";

            var rotationDateInt = 0;
            do {
                rotationDateInt = rotationDate.getFullYear() + (" 00" + (parseInt(rotationDate.getMonth()) + 1)).substr(-2)
              
                var newArray = this.cache[bird][rotationDateInt].filter(function (ob) {

                    if (startDateInt === rotationDateInt) {
                        return startDay < ob.day;
                    }
                    if (endDateInt === rotationDateInt) {
                        return endDay > ob.day;
                    }
                    return true;
                })
                data = data.concat(newArray);
                rotationDate.setMonth(rotationDate.getMonth() + 1);
            } while (rotationDateInt < endDateInt);
        } else {
            var bird = (this.filters.bird.data) ? this.filters.bird.data.birdId : "all";
            for (var month in this.cache[bird]) {
                data = data.concat(this.cache[bird][month]);
            }
        }
        this.birdMarkersLayer.clear();
        this.birdMarkersLayer.add(data).addTo(this.map);
    }
    $.fn.birdApp = function () {
        this.birdApp = new birdApp($(this));
        return this;
    };
})(jQuery)



window.birdApp = $(".birdAppContainer").birdApp();

$(".birdAppContainer .search").birdSearch(function (item) {
    $(this).val("");
    birdApp.birdApp.updateFilters("bird", item);
    
});

