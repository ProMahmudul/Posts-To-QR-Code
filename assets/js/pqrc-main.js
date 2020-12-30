;(function ($) {
    $(document).ready(function () {
        let current_value =  $("#pqrc_toggle").val();
        $("#toggle1").minitoggle();

        if(current_value == 1){
            $("#toggle1 .minitoggle").addClass("active");
        }

        $("#toggle1").on("toggle", function (e) {
            if (e.isActive) {
                $("#pqrc_toggle").val(1);
            } else {
                $("#pqrc_toggle").val(0);
            }
        });
    });
})(jQuery);