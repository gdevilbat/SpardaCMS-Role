/*=================================
=            Form Role            =
=================================*/

    $("#submit-role").click(function(event) {
        $(".checkbox").each(function(index, el) {
            if ($(this).is(':checked')) {
                $($(this).next('.role').get(0)).val(true);
              } else {
                $($(this).next('.role').get(0)).val(false);
              }
            $(this).attr('value', 'false');
        });
        $("#form-role").submit();
    });

/*=====  End of Form Role  ======*/