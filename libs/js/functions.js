
function suggetion() {

     $('#sug_input').keyup(function(e) {

         var formData = {
             'product_name' : $('input[name=title]').val()
         };

         if(formData['product_name'].length >= 1){

           // process the form
           $.ajax({
               type        : 'POST',
               url         : 'ajax.php',
               data        : formData,
               dataType    : 'json',
               encode      : true
           })
               .done(function(data) {
                   //console.log(data);
                   $('#result').html(data).fadeIn();
                   $('#result li').click(function() {

                     $('#sug_input').val($(this).text());
                     $('#result').fadeOut(500);

                   });

                   $("#sug_input").blur(function(){
                     $("#result").fadeOut(500);
                   });

               });

         } else {

           $("#result").hide();

         };

         e.preventDefault();
     });

 }
  $('#sug-form').submit(function(e) {
      var formData = {
          'p_name' : $('input[name=title]').val()
      };
        // process the form
        $.ajax({
            type        : 'POST',
            url         : 'ajax.php',
            data        : formData,
            dataType    : 'json',
            encode      : true
        })
            .done(function(data) {
                //console.log(data);
                $('#product_info').html(data).show();
                total();
                $('.datePicker').datepicker('update', new Date());

            }).fail(function() {
                $('#product_info').html(data).show();
            });
      e.preventDefault();
  });
  function total(){
    $('#product_info input').change(function(e)  {
            var price = +$('input[name=price]').val() || 0;
            var qty   = +$('input[name=quantity]').val() || 0;
            var total = qty * price ;
                $('input[name=total]').val(total.toFixed(2));
    });
  }

  $(document).ready(function() {

    //tooltip
    $('[data-toggle="tooltip"]').tooltip();

    $('.submenu-toggle').click(function () {
       $(this).parent().children('ul.submenu').toggle(200);
    });
    //suggetion for finding product names
    suggetion();
    // Callculate total ammont
    total();

    $('.datepicker')
        .datepicker({
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            autoclose: true
        });
  });

  $(document).ready(function() {
    $(".open-modal").click(function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        var type = $(this).data("type");

        $.ajax({
            url: "fetch_products.php",
            type: "POST",
            data: { id: id, type: type },
            success: function(response) {
                $("#modal-content").html(response);
                $("#itemsModal").modal("show");
            },
            error: function() {
                $("#modal-content").html("<tr><td colspan='5' class='text-center'>Failed to load data.</td></tr>");
            }
        });
    });
});

$(document).ready(function() {
    var loading = false;
    var offset = 0; // Start with the first page of logs

    // Function to fetch and append logs
    function loadLogs() {
        if (loading) return;
        loading = true;

        // Send an AJAX request to fetch more logs
        $.ajax({
            url: 'fetch_access_logs.php', // This file will handle the fetching and return logs
            method: 'GET',
            data: { offset: offset }, // Ensure that 'offset' is passed correctly
            success: function(data) {
                if (data) {
                    $('table tbody').append(data); // Append the new logs to the table
                    offset += 10; // Increase the offset for the next request
                    loading = false;
                } else {
                    loading = true; // No more logs to load
                }
            },
            error: function() {
                console.error("Error fetching access logs.");
                loading = false;
            }
        });
    }

    // Trigger load logs when the user scrolls near the bottom
    $(window).scroll(function() {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
            loadLogs();
        }
    });

    // Initial load
    loadLogs();
});


