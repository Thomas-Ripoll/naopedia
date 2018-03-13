$(function(){
            window.birdApp = $(".birdAppContainer").birdApp();
            $(".birdAppContainer .search ").birdSearch(function (item) {
                $(this).val("");
                console.log("blablabl")
                birdApp.birdApp.updateFilters("bird", item);

            });
            $(".filter-btn").on("click",function(e){
                e.preventDefault();
                $(this).parent().find('.filter-column-inner .filters-box').toggleClass("open");
            })
        })