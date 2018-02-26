require('webpack-jquery-ui/autocomplete');
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
        },
        
    });

    var birdSearch = function (element, callback) {
        var _this = this;
        this.cache = [];
        this.container = $("<div class='autocomplete_menu'>");
        $("body").append(this.container);
        element.autocomplete({
            appendTo: _this.container,
            "source": function (request, response) {
                _this.ajaxSearch(request.term, response)
            },
            "minLength": 3,
            select: function (event, ui) {
                callback.call(this, ui.item);
                return false;
            },
            focus: function () {
                return false; 
            },
            open: function( event, ui ) {
                console.log($(this).autocomplete( "instance" ));
                var menu = $(this).autocomplete( "instance" ).menu.element;
                var bounding = $(this)[0].getBoundingClientRect();
                console.log(bounding);
                var style = {
                    top: bounding.top + bounding.height+2+window.pageYOffset ,
                    left: bounding.left,
                    width: bounding.width,
                    display: "block"
                    
                }
                _this.container.css(style);
                menu.removeAttr("style");
            },
            close: function(event, ui){
                _this.container.css("display", "none");
            }
        });
    }
    birdSearch.prototype.ajaxSearch = function (term, response) {
        var _this = this;

        if (_this.cache.hasOwnProperty(term.substring(0, 3))) {

            var results = _this.filterResults(term, _this.cache[term.substring(0, 3)]);

            response(results);
        } else {
            $.getJSON("/get-bird-list",
                    {"term": term.substring(0, 3)},
                    function (data) {

                        _this.cache[term.substring(0, 3)] = data;
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
    birdSearch.prototype.filterResults = function (term, data) {
        var termR = RegExp(term, "i");
        return data.filter(item => termR.test(item.birdName) || termR.test(item.birdLatinName) || termR.test(item.birdName+" "+item.birdLatinName));
    }

    $.fn.birdSearch = function (callback) {

        new birdSearch($(this), callback);
        return this;
    };

})(jQuery);


