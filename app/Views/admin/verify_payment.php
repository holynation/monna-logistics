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
                <h4 class="nk-block-title page-title">Verify Interswitch Wallet Payment</h4>
            </div><!-- .nk-block-head-content -->
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->
    <div class="nk-block">
    <div class="card card-preview">
    <div class="card-inner">
    <div class="preview-block">
    <div class="row g-gs">
    <div class="col-sm-12">
        <div class="card card-preview">
            <div class="card-inner">
                <?php if(!$modelStatus){ ?>
                    <div class="alert alert-info text-center w-100">
                      <span>There seems to be no data available</span>
                    </div>
                <?php }else{ ?>

                <form action="" method="get">
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-label">Start Date</label>
                                <div class="form-control-wrap">
                                    <div class="form-icon form-icon-left">
                                        <em class="icon ni ni-calendar"></em>
                                    </div>
                                    <input type="text" class="form-control date-picker" data-date-format="yyyy-mm-dd" name="startDate">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-label">End Date</label>
                                <div class="form-control-wrap">
                                    <div class="form-icon form-icon-left">
                                        <em class="icon ni ni-calendar"></em>
                                    </div>
                                    <input type="text" class="form-control date-picker" data-date-format="yyyy-mm-dd" name="endDate">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <div class="form-control-wrap">
                                    <select class="form-select" name="paymentStatus">
                                        <option></option>
                                        <option value="pending">Pending</option>
                                        <option value="success">Success</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-label"></label>
                                <div class="form-control-wrap">
                                    <button type='submit' class="btn btn-primary save">Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <table class="datatable-init nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col nk-tb-col-check">
                                <div class="custom-control custom-control-sm custom-checkbox notext">
                                    <input type="checkbox" class="custom-control-input" id="uid">
                                    <label class="custom-control-label" for="uid"></label>
                                </div>
                            </th>
                            <th class="nk-tb-col"><span class="sub-text">Fullname</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Reference Number</span></th>
                            <th class="nk-tb-col tb-col-mb"><span class="sub-text">Amount (NGN)</span></th>
                            <th class="nk-tb-col tb-col-md"><span class="sub-text">Phone</span></th>
                            <th class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></th>
                            <th class="nk-tb-col tb-col-md"><span class="sub-text">Date created</span></th>
                            <th class="nk-tb-col nk-tb-col-tools text-right">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($modelPayload as $model): ?>
                        <tr class="nk-tb-item verify-content">
                            <td class="nk-tb-col nk-tb-col-check">
                                <div class="custom-control custom-control-sm custom-checkbox notext">
                                    <input type="checkbox" class="custom-control-input" id="uid1_<?= $model['ID']; ?>" value="<?= $model['ID']; ?>">
                                    <label class="custom-control-label" for="uid1_<?= $model['ID']; ?>"></label>
                                </div>
                            </td>
                            <?php
                                $userdata = $userObject->getRealUserData($model['user_id']);
                            ?>
                            <td class="nk-tb-col">
                                <div class="user-card">
                                    <div class="user-avatar bg-dim-primary d-none d-sm-flex">
                                        <span><?= (@$userdata['fullname']) ? formatToNameLabel($userdata['fullname'],true) : null; ?></span>
                                    </div>
                                    <div class="user-info">
                                        <span class="tb-lead"> <?= (@$userdata['fullname']) ? $userdata['fullname'] : null; ?> <span class="dot dot-success d-md-none ml-1"></span></span>
                                        <span><?= @$userdata['email'] ?: null; ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="nk-tb-col tb-col-mb" data-order="35040.34">
                                <span><?= $model['reference_number']; ?></span>
                            </td>
                            <td class="nk-tb-col tb-col-mb" data-order="35040.34">
                                <span class="tb-amount"><?= number_format($model['amount']); ?></span>
                            </td>
                            <td class="nk-tb-col tb-col-md">
                                <span><?= @$userdata['phone_number'] ?: null ; ?></span>
                            </td>
                            <td class="nk-tb-col tb-col-md">
                                <span class="tb-status <?php echo $model['payment_status'] == 'success' ? 'text-success' : 'text-danger'; ?> "><?= strtoupper($model['payment_status']); ?></span>
                            </td>
                            <td class="nk-tb-col tb-col-md">
                                <span><?= dateFormatter($model['date_created']); ?></span>
                            </td>
                            <td class="nk-tb-col nk-tb-col-tools">

                            </td>
                            <input type="hidden" class="wallet_amount" value="<?= $model['amount']; ?>">
                        </tr><!-- .nk-tb-item  -->
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <form id="verify_form" action="<?php echo base_url('ajaxData/approvePayment'); ?>" method="post">
                    <input type="hidden" name="update" id="verify_update">
                    <button type='submit' class="btn btn-primary save float-right">Approve Payment Manually</button>
                </form>
                <?php } ?>
            </div>
        </div><!-- .card-preview -->
    </div>
    </div>
    </div>
    </div>
    </div>
    </div><!-- .nk-block -->
</div>
</div>
</div>
</div>
<!-- content @e -->

<!-- JavaScript -->
<?php include_once ROOTPATH.'template/footer.php'; ?>
<script>
$(document).ready(function($){
    $('#verify_form').submit(function(e) {
        e.preventDefault();
        var update = [];

        $('.verify-content').each(function(index, el){
            let amount = $(this).children('.wallet_amount').val();
            let wallet = $(this).find(':checked').val();
            if(typeof wallet !== 'undefined'){
                update.push({wallet: wallet, amount: amount});
            }
        });

        //stringify the content and send it to the server straight
        if(!isArrayEmpty(update)){
            if(confirm("Are you sure you want to proceed?")){
                var updateString = JSON.stringify(update);
                $('#verify_update').val(updateString);
                submitAjaxForm($(this));
            }
        }
    });
});

function ajaxFormSuccess(target,data) {
    data = JSON.parse(data);
    if (data.status) {
      reportAndRefresh(target,data);
      return;
    }
    showNotification(data.status,data.message);   
}
</script>