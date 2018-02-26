import flatpickr from "flatpickr";
import { French } from "flatpickr/dist/l10n/fr.js"
var dates = $(".dates").flatpickr({
    mode: "range",
    maxDate: "today",
    locale: French,
    onChange:function(selectedDates, dateStr, instance){
        console.log(dateStr);
        if(selectedDates.length == 1 && !birdApp.birdApp.filters.bird.data){
            var dateTimeStamp = selectedDates[0].getTime();
            var threeMonths = (90 * 24 * 3600 * 1000);
            var minDate = new Date(dateTimeStamp - threeMonths);
            var maxDate = new Date(Math.min((new Date()).getTime(), dateTimeStamp + threeMonths));
            
            this.config.minDate = minDate;
            this.config.maxDate = maxDate;
        }
        if(selectedDates.length == 2){
            birdApp.birdApp.updateFilters("dates",selectedDates);
            $(this._input).val("");
        }
    },
    onOpen: function(){
        console.log(birdApp.birdApp.filters.bird.data);
        if(!birdApp.birdApp.filters.bird.data){
            $('.flatpickr-warning').removeClass("d-none");
        }
        else{
            $('.flatpickr-warning').addClass("d-none");
            
        }
    },
    onClose: function(){
        this.config.minDate = null;
        this.config.maxDate = "today";
    },
    onReady:function(){
        var warningDiv = $('<div class="flatpickr-warning">').html("Sans espèce sélectionnée,<br>la recherche est limitée à 90 jours");
        $(".flatpickr-month").after(warningDiv);
    }
});

$(birdApp.birdApp).on("updateFiltersEvent",function(e){
    
});


