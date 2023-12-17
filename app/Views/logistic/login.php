<!DOCTYPE html>
<html lang="en">
    <!--begin::Head-->
    <head>
        <title>Login Page | MonnaExpress</title>
        <meta charset="utf-8" />
        <meta name="description" content="" />
        <meta name="keywords" content="" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta property="og:locale" content="en_US" />
        <meta property="og:type" content="" />
        <meta property="og:title" content="" />
        <meta property="og:url" content="" />
        <meta property="og:site_name" content="" />
        <link rel="canonical" href="" />

        <link rel="apple-touch-icon" sizes="72x72" href="<?= base_url('assets/media/favicon/apple-icon-72x72.png'); ?>">
        <link rel="apple-touch-icon" sizes="114x114" href="<?= base_url('assets/media/favicon/apple-icon-114x114.png'); ?>">
        <link rel="icon" type="image/png" sizes="192x192"  href="<?= base_url('assets/media/favicon/android-icon-192x192.png'); ?>">
        <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/media/favicon/favicon-32x32.png'); ?>">
        <link rel="icon" type="image/png" sizes="96x96" href="<?= base_url('assets/media/favicon/favicon-96x96.png'); ?>">
        <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/media/favicon/favicon-16x16.png'); ?>">

        <!--begin::Fonts(mandatory for all pages)-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
        <!--end::Fonts-->
        <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
        <link href="<?= base_url('assets/plugins/global/plugins.bundle.css'); ?>" rel="stylesheet" type="text/css" />
        <link href="<?= base_url('assets/css/style.bundle.css'); ?>" rel="stylesheet" type="text/css" />
        <!--end::Global Stylesheets Bundle-->
        <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.min.js'); ?>"></script>
    </head>
    <!--end::Head-->
    <!--begin::Body-->
    <body id="kt_body" class="app-blank app-blank">
        <!--begin::Root-->
        <div class="d-flex flex-column flex-root" id="kt_app_root">
            <!--begin::Authentication - Sign-in -->
            <div class="d-flex flex-column flex-lg-row flex-column-fluid">
                <!--begin::Aside-->
                <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center" style="background-image: url(assets/media/auth_bg.jpg)">
                </div>
                <!--begin::Aside-->
                <!--begin::Body-->
                <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10">
                    <!--begin::Form-->
                    <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                        <!--begin::Wrapper-->
                        <div class="w-lg-500px p-10">
                            <!--begin::Form-->
                            <?php echo form_open("login?_=".time(), array('class'=> 'form','id'=>'loginForm')); ?>
                                <!-- this is the notification section -->
                                <div id="notify"></div>
                                <!-- end notification -->

                                <!--begin::Heading-->
                                <div class="text-center mb-11">
                                    <!--begin::Logo-->
                                    <a href="" class="mb-0 mb-lg-20">
                                        <img alt="Logo" src="<?= base_url('assets/media/logo.jpg'); ?>" class="h-40px h-lg-50px" />
                                    </a>
                                    <!--end::Logo-->

                                    <!--begin::Title-->
                                    <h1 class="text-dark fw-bolder mb-3 mt-3">Sign In</h1>
                                    <!--end::Title-->
                                    <!--begin::Subtitle-->
                                    <div class="text-gray-500 fw-semibold fs-6">
                                        Securely manage and oversee operations. Enter your credentials to unlock the power for a smooth ride ahead. Your gateway to enhanced logistics administration awaits.
                                    </div>
                                    <!--end::Subtitle=-->
                                </div>
                                <!--begin::Heading-->

                                <!--end::Separator-->
                                <!--begin::Input group=-->
                                <div class="fv-row mb-8">
                                    <!--begin::Email-->
                                    <input type="email" placeholder="Email" name="email" autocomplete="off" class="form-control bg-transparent" />
                                    <!--end::Email-->
                                </div>
                                <!--end::Input group=-->
                                <div class="fv-row mb-3">
                                    <!--begin::Password-->
                                    <input type="password" placeholder="Password" name="password" autocomplete="off" class="form-control bg-transparent" />
                                    <!--end::Password-->
                                </div>
                                <!--end::Input group=-->

                                <!--begin::Submit button-->
                                <div class="d-grid mb-10">
                                    <button type="submit" id="btnLogin" class="btn btn-primary">
                                        <span class="indicator-label">Sign In</span>
                                    </button>
                                </div>
                                <!--end::Submit button-->

                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Form-->
                </div>
                <!--end::Body-->
            </div>
            <!--end::Authentication - Sign-in-->
        </div>
        <!--end::Root-->
        <!--begin::Javascript-->
        <script>var hostUrl = "<?= base_url('assets/'); ?>";</script>
        <!--begin::Global Javascript Bundle(mandatory for all pages)-->
        <script src="<?= base_url('assets/plugins/global/plugins.bundle.js'); ?>"></script>
        <script src="<?= base_url('assets/js/scripts.bundle.js'); ?>"></script>
        <!--end::Global Javascript Bundle-->

        <script src="<?php echo base_url('assets/js/custom.js'); ?>"></script>
        <script type="text/javascript">
            $(function(){
                // here is the login
                var form = $('#loginForm');
                var note = $("#notify");
                note.text('').hide();

                form.submit(function(event) {
                    event.preventDefault();
                    $("#btnLogin").html("Authenticating...").addClass('disabled').prop('disabled', true);
                    submitAjaxForm($(this));
                    $("#btnLogin").removeClass("disabled").removeAttr('disabled').html("Sign in");
                });
            })

            function ajaxFormSuccess(target,data){
                data = JSON.parse(data);
                $("#notify").text('').show();
                if (data.status) {
                    var path = data.message;
                    console.log('got here');
                    // location.assign(path);
                }
                else{
                  $("#btnLogin").removeClass("disabled").removeAttr('disabled').html("Sign in");
                  $("#notify").text(data.message).addClass("alert alert-danger alert-dismissible show text-center").css({"font-size":"12.368px"}).append('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"></span></button>');
                }
            }
        </script>
        <!--end::Javascript-->
    </body>
    <!--end::Body-->
</html>