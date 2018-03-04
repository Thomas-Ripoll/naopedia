(function ($) {
    var NaoAjax = function () {
        this.lastAction = null;
        this.connectModal = null;
        
        
    }
    NaoAjax.prototype.postConnect = function (url, senddata, success,fail) {
        var _this = this;
        $.post(url, senddata, function (data) {
            success(data);
        }).fail(function (data) {
            if (data.responseJSON && data.responseJSON.state == "connect") {
                
                if (!_this.connectModal) _this.buildModal();
                _this.saveLastAction(url, senddata, success,fail);
                _this.login();
            }
        });
    }
    NaoAjax.prototype.saveLastAction = function(url, data, success,fail){
        this.lastAction = {
            url : url,
            data : data,
            success : success,
            fail : fail,
        };
    }
    NaoAjax.prototype.postLastAction = function(){
        var la = this.lastAction;
        this.postConnect(la.url, la.data, la.success, la.fail);
    }
    NaoAjax.prototype.login = function (formData) {
        var _this = this;
        $.ajax({
            dataType: "json",
            method: formData ? "POST" : "GET",
            url: "/login",
            data: formData,
            success: function (data) {

                if (data.state) {
                    $("#profil").replaceWith($($.parseHTML(data.profil)));
                    _this.connectModal.modal('hide');
                    _this.postLastAction();
                } else {
                    var loginForm = $($.parseHTML(data.view));
                    _this.connectModal.find(".modal-content").html("").append(loginForm.html());
                    _this.connectModal.find("form").on("submit", function (e) {
                        e.preventDefault();
                        _this.login($(this).serialize());
                    })
                    _this.connectModal.find('[href="/signin"]').on("click",function(e){
                        e.preventDefault();
                        _this.signIn();
                    });
                    _this.connectModal.modal('show');
                }

            }
        });
    }
    NaoAjax.prototype.signIn = function (formData) {
        var _this = this;
        $.ajax({
            dataType: "json",
            method: formData ? "POST" : "GET",
            url: "/signin",
            data: formData,
            contentType: false,       
            cache: false,             
            processData:false,
            success: function (data) {

                if (data.state) {
                    _this.login();
                } else {
                    var loginForm = $($.parseHTML(data.view));
                    _this.connectModal.find(".modal-content").html("").append(loginForm.html());
                    _this.connectModal.find("form").on("submit", function (e) {
                        e.preventDefault();
                        _this.signIn(new FormData(this));
                    })
                    _this.connectModal.find('[href="/login"]').on("click",function(e){
                        e.preventDefault();
                        _this.login();
                    });
                    _this.connectModal.modal('show');
                }

            }
        });
    }
    NaoAjax.prototype.buildModal = function () {
        this.connectModal = $('<div class="modal" id="NaoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">\n\
                                <div class="modal-dialog modal-dialog-centered" role="document">\n\
                                 <div class="modal-content">\n\
                            </div></div></div>');
        $("body").append(this.connectModal);
        this.connectModal.modal();
    }
    NaoAjax.prototype.loading = function(start){
        if(start){
            this.connectModal.addClass("loading").find(".modal-content").html("");
                    this.connectModal.modal("show");
        }
        else{
            this.connectModal.removeClass("loading");
        }
    }
    var naoAjax = new NaoAjax();
    $.postConnect = function () {
        naoAjax.postConnect.apply(naoAjax, arguments);

    };
    console.log($.fn);


})(jQuery)


