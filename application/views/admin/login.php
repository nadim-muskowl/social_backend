<div class="container">
    <div class="card" style="margin-top: 10rem;">
        <div class="card-body">
            <h5 class="card-title">Admin Login</h5>
            <form action="#" id="form" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter username">
                    <span class="help-block"></span>                            
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" onkeyup="form_submit(event);" class="form-control" placeholder="Password">
                    <span class="help-block"></span>
                </div>                        
                <button type="button" onclick="form_save()" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("input").change(function () {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().next().empty();
        });
        $("textarea").change(function () {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().next().empty();
        });
        $("select").change(function () {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().next().empty();
        });
    });
    function form_save() {
        var formData = new FormData($('#form')[0]);
        $.ajax({
            url: '<?= base_url('api/admins/login') ?>',
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
                        for (var i = 0;
                                i < data.inputerror.length;
                                i++) {
                            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
                            $('[name="' + data.inputerror[i] + '"]').next().next().text(data.error_string[i]);
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