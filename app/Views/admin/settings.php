<!-- main header @s -->
<?php include_once ROOTPATH.'template/header.php'; ?>
<!-- main header @e -->

<!-- content @s -->
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h4 class="nk-block-title page-title">General Settings</h4>
                        </div><!-- .nk-block-head-content -->
                    </div><!-- .nk-block-between -->
                </div><!-- .nk-block-head -->
                <div class="nk-block">
                    <form method="post" action="<?= base_url('ajaxData/app_settings'); ?>" id="formSettings">
                        <div class="row g-gs">
                            <div class="col-6">
                                <div class="card text-white bg-gray">
                                    <div class="card-header"><b>Withdrawal Settings</b></div>
                                    <div class="card-inner">
                                        <div class='form-group'>
                                            <label for='min_withdrawal'>Minimum Withdrawal</label>
                                            <input type='text' name='min_withdrawal' id='min_withdrawal' value="<?= isset($setting['min_withdrawal']) ? $setting['min_withdrawal'] : '' ?>" class='form-control' required />
                                        </div>
                                        <span class="preview-title text-white">Automatic Withdrawal</span>
                                        <div class="custom-control custom-control-lg custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="customSwitch1" name="auto_withdrawal" <?= (isset($setting['auto_withdrawal']) && $setting['auto_withdrawal'] == '1') ? "checked" : '' ?>>
                                            <label class="custom-control-label" for="customSwitch1"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- .row -->
                        <div class="text-center mx-auto mt-4 w-35">
                            <button type='submit' class="btn btn-block btn-lg btn-primary" id="btnSettings">Save Settings</button>
                        </div>
                    </form>
                </div><!-- .nk-block -->
            </div>
        </div>
    </div>
</div>
<!-- content @e -->

<!-- JavaScript -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#formSettings').submit(function(e){
            e.preventDefault();
            submitAjaxForm($(this));
        });
    });
    function ajaxFormSuccess(target,data) {
        data = JSON.parse(data)
        if (data.status) {
            setTimeout(function(){
                location.reload();
            }, 5000);
        }
        else{
            $("#btnSettings").removeClass("disabled").removeAttr('disabled').text("Save Settings");
        }
        showNotification(data.status, data.message);
    }
</script>
<?php include_once ROOTPATH.'template/footer.php'; ?>