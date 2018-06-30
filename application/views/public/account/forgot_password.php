<div class="container">
    <div class="jumbotron">
        <h1 class="display-4">Forgot Password ?</h1>
        <p class="lead">This is a simple hero unit, a simple jumbotron-style component for calling extra attention to featured content or information.</p>
        <hr class="my-4">
        <form action="#" id="form" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col">
                    <div class="form-group">                        
                        <input type="text" name="email" class="form-control" placeholder="Enter Email">
                        <span class="help-block"></span>                            
                    </div>  
                </div>
                <div class="col">
                    <button type="button" onclick="form_save()" class="btn btn-primary mb-2">Submit</button>
                </div>
            </div>
           
        </form>
    </div>
</div>       

<script>
    $(document).ready(function () {
        $("input").change(function () {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("textarea").change(function () {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("select").change(function () {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
    });

    function form_save() {
        var formData = new FormData($('#form')[0]);
        $.ajax({
            url: 'api/users/forgot',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: "JSON",
            beforeSend: function () {
                $("#loading").show();
                $('#button-submit').button('loading');
            },
            complete: function () {
                $("#loading").hide();
                $('#button-submit').button('reset');
            },
            success: function (data) {
                console.log(data);
                if (data.status) {
                    notification('Success:', 'success', data.message);
                    $('#form')[0].reset();
                    $(location).attr("href", data.redirect);
                } else {
                    if (data.inputerror) {
                        for (var i = 0; i < data.inputerror.length; i++) {
                            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                        }
                        notification('Warning:', 'warning', data.message);
                    } else {
                        notification('Error:', 'error', data.message);
                    }

                }

            }
        });
    }

    function form_submit(e) {
        if (e.keyCode == 13 || e.which == 13) {
            form_save();
        }
    }
</script>