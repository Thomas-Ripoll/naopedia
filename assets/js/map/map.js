var greet = require('./greet');
import L from "leaflet";
var leafletPhotos = require('leaflet.markercluster');
var leafletPhotos = require('./Leaflet.Photo.js');
delete L.Icon.Default.prototype._getIconUrl;
require("bootstrap");
let observeMode = false;
let minimap = null;

import iconRetina from 'leaflet/dist/images/marker-icon-2x.png';
import icon from 'leaflet/dist/images/marker-icon.png';
import iconShadow from 'leaflet/dist/images/marker-shadow.png';

L.Icon.Default.mergeOptions({
  iconRetinaUrl: iconRetina,
  iconUrl: icon,
  shadowUrl: iconShadow,
});

function toggleObserveMode(on){
    observeMode = on;
    if(on){
        $("body").addClass("onObserve");
    }
    else{
        
        $("body").removeClass("onObserve");
    }
}
$(document).ready(function() {

    //init
    $('#exampleModal').modal({show:false});

    //events
    $("#addObervation").on("click",function(e){
        e.preventDefault();
        toggleObserveMode(!observeMode);
    })
    $("#mapid").on('animationend animationend webkitAnimationEnd oanimationend MSAnimationEnd', ".ripple",  
                function(e) {
                    $(this).remove();
              });
    

    
    //action
    var mymap = L.map('mapid').fitBounds([
        [51.39920565355378, -5.537109375000001 ],
        [42.06560675405716, 8.613281250000002 ]
    ]);
    
    var thumbnails = {
        corbeau:[
            "corbeau-2-143327.jpg",
            "corbeau.d.australie.pain.8g.jpg",
            "corbeau.d.australie.pain.9p.jpg",
            "cover-r4x3w1000-593821dd68ae6-sipa-rex40247619-000001.jpg",
            "grand.corbeau.auau.5p.jpg"         
        ],
        pie: [
            "67-zoom.jpg",                      
            "pie.bavarde.redu.13g.jpg",
            "_OwCtMsKLztlWLYixbXxI4jduwI.jpg",  
            "pie-oiseau.jpg",
            "Pie-bavarde_0.jpg"
        ]
    }
    
    var birds = [
        {
            name: 'corbeau',
            data: []
            
        },
        {
            name: "pie",
            data:[]
        }
        
    ];
    function generateBird(bird){
        var max = Math.random()*500;
        for(var i = 0; i < max; i++ ){
            bird.data.push({
                lat: ((Math.random()*8) + 42),
                lng : ((Math.random()*13) -5),
                thumbnail: "images/uploads/imgs/birds/"+bird.name+"/"+thumbnails[bird.name][Math.floor(Math.random()*thumbnails[bird.name].length)],
                url:"images/uploads/imgs/birds/"+bird.name+"/"+thumbnails[bird.name][Math.floor(Math.random()*thumbnails[bird.name].length)],
                caption:bird.name
            })
        }
    }
   for (var idx in birds){
       generateBird(birds[idx])
   }
   console.log(birds)
   
    var photoLayer = L.photo.cluster().on('click', function (evt) {
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
   
   
   
   
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
	maxZoom: 19,
	attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(mymap);
    
    
    mymap.on('click', function(e) {   
        if(observeMode){  
            //console.log(e.latlng);
            $('#exampleModal').data("latlng",e.latlng).modal("show");
            let ripple = $('<div class="ripple">');
            
            ripple.css({top: e.containerPoint.y, left: e.containerPoint.x})
            $(this._container).append(ripple);
         }   
            
    });
    
    
    photoLayer.add(birds[0].data).addTo(mymap);
    mymap.fitBounds(photoLayer.getBounds());
    
    $('#exampleModal').on('shown.bs.modal', function () {
        var latlng = $(this).data("latlng")
        window.setTimeout(function(){
            if(!minimap){
                minimap = L.map('minimap',{
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
                minimap.singleMarker = L.marker(latlng).addTo(minimap);
            }
            else{
            minimap.setView(latlng, minimap.getZoom());
            minimap.singleMarker.setLatLng(latlng);
            }
        },100);
      });
      
      
    $("#file-upload").on("change", function () {
        var reader = new FileReader();

        reader.onload = function (e) {
            // get loaded data and render thumbnail.
            $("#image-preview").css("display","inline-block").attr("src",e.target.result);
            $("#image-before-preview").css("display","none");
        };

        // read the image file as a data URL.
        reader.readAsDataURL(this.files[0]);

    });
    
    
    $("#especes").change(function(){
        photoLayer.clear();
        var id = $(this).val();
        photoLayer.add(birds[id].data).addTo(mymap);
    });
    
});


