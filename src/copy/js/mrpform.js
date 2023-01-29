var MrpForm = {};


MrpForm.Init = function () {

    $('.mprForm').each(function (index) {
        MrpForm.New($(this));
    });

}


MrpForm.New = function (e) {
    var self = {};

    $(e).on("submit", function () {
        self.Send();
        return false;
    });

    $(e).find(".btnSumbitForm").on("click", function () {
        self.Send();
    });

    self.route = e.attr("action");
    self.btnSumbitForm = e.find(".btnSumbitForm");
    self.loadingDiv = e.find(".loadingDiv");
    self.errorDiv = e.find(".errorDiv");
    self.goodDiv = e.find(".goodDiv");
    self.isLoading = false;

    self.GetData = function () {
        var data = {};
        var formData = e.serializeArray();
        for (var i = 0; i < formData.length; i++) {
            data[formData[i].name] = formData[i].value;
        }
        return data;
    }

    self.OnSended = function () {
        e.css("opacity", "1");
        self.isLoading = false;
        self.loadingDiv.hide();
        self.btnSumbitForm.show();
    }

    self.Send = function () {
        if (self.isLoading) return;
        e.css("opacity", "0.4");
        e.css("transition", "0.4s");
        self.loadingDiv.show();
        self.btnSumbitForm.hide();
        self.errorDiv.hide();
        self.goodDiv.hide();
        var data = self.GetData();

        EasyApi.Post(self.route, data, function (response, error) {
            self.OnSended();
            if (error) {
                self.errorDiv.show();
                self.errorDiv.html(error);
                return;
            }

            self.goodDiv.show();

        });
    }

    return self;
}


$(document).ready(function () {
    MrpForm.Init();
});

window.StepPoll = MrpForm;
