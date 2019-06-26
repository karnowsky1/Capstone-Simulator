//Detect the document's state of readiness
$(document).ready(function () {

    //Faculty's UI (Approval for Grad Student's pre-proposal
    $(".approval").click(function(){
         var pid = this.id;
         var sid = "400";

        //Perform an asynchronous HTTP (Ajax) request
        $.ajax({
            method: "POST",
            data: "pid="+pid+"&sid="+sid,
            url: "functions/src/setProjectStatus.php",
            success: function(result)
            {
                var obj = jQuery.parseJSON(result); //parse the JSON Object

                //Success?
                if(obj.status === 200){
                     var msg = "<div class='alert alert-success'><strong>Success! </strong>" + obj.msg + "</div>";
                     $(".modal-body").before(msg);
                     $(".approval").prop("disabled", true);

                }
                //Fail?
                else
                {
                    var msg = "<div class='alert alert-danger'><strong>Failed! </strong>" + obj.msg + "</div>";
                    $(".modal-body").before(msg);
                }

            }
        });
    });

    //Setting up the function for table (Grad Student's Capstone)
    var table = $('#dataTable').DataTable();
    $('#dataTable tbody').on('mouseover', 'tr', function(){
        $(this).css("background-color", "lightgray"); //mouse enter
        $(this).css("cursor", "pointer");
        });
    $('#dataTable tbody').on('mouseleave', 'tr', function(){
        $(this).css("background-color", "white"); //mouse exit
        $(this).css("cursor", "auto");
    });

    $('#dataTable tbody').on('click', 'tr', function () {

        var tableRow = table.row(this).data(); //Getting the row from table
        var pid= tableRow[1].toString(); // Getting the PID value from the row
        var name = tableRow[9].toString(); //Grad Student's name from the row

        //Perform an asynchronous HTTP (Ajax) request
        $.ajax({
            method: "POST",
            data: "pid="+pid,
            url: "functions/src/getProjectStatus.php",
            success: function(result)
            {
                var obj = jQuery.parseJSON(result); //parse the JSON Object

                //Fail?
                if(obj.status==400)
                {
                    alert("No status was found for this project")
                }
                //Success?
                else if(obj.status==200)
                {
                    $('.myGradStatus').empty(); //Remove all child nodes inside

                    var jsonArray = jQuery.parseJSON(obj.msg.toString());
                    var modal = "<div class='modal fade' id='myGradStatus' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>" +
                        "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'>" +
                        "<h4 class='modal-title'>" + name + "'s Capstone Status</h4><button type='button' class='exit' data-dismiss='modal' aria-hidden='true'>×</button>" +
                        "</div><div class='modal-body'><div class='table-responsive'><table class='table table-bordered'>" +
                        "<thead><tr><th>Status</th><th>Status Description</th><th>Last Modified</th></thead><tbody>";

                    // A generic iterator function
                    $.each(jsonArray,function(index, jsonObject){
                        $.each(jsonObject, function(key, value){
                            modal+= "<tr><td>" + value.name + "</td>"
                                + "<td>" + value.description + "</td>"
                                + "<td>" + value.last_modified + "</td></tr>";
                        });
                    });

                    modal+= "</tbody></table></div></div><div class='modal-footer'><button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>" +
                        "</div></div></div></div>";

                    $('.myGradStatus').append(modal);
                    $("#myGradStatus").modal();
                }

            }
        });

    });
});