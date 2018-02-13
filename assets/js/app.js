
require("./jquery-ui.min.js");
require("./map/map.js");
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

})(jQuery);
var cache = {};
$(function () {
    $("#bird").autocomplete({
        "source": function (request, response) {
            observationsSearch(request.term, response);
        },
        "minLength": 3,
        select: function (event, ui) {
            $(this).val("");
            return false;

        },
        focus: function () {
            return false;
        }
    });
})
function observationsSearch(term, response) {
    if (cache.hasOwnProperty(term.substring(0, 3))) {

        var results = filterResults(term, cache[term.substring(0, 3)]);

        response(results);
        console.log("cache: " + results.length);
    } else {
        console.log("fresh");
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

