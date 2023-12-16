<?php include_once ROOTPATH."template/header.php"; ?>
<!-- Page header -->
<div class="container-p-y container-p-x">
  <div class="d-flex">
    <h4><span><?php echo ucfirst($userType); ?> </span> - <?php echo ucwords($pageTitle); ?> Page</h4>
  </div>

  <div class="d-flex">
    <div class="breadcrumb">
      <a href="<?php echo base_url("vc/$userType/dashboard"); ?>" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
      <a href="#" class="breadcrumb-item"><?php echo $pageTitle; ?></a>
      <span class="breadcrumb-item active">Current</span>
    </div>
  </div>
</div>
<!-- /page header -->
<!-- Content -->
<div class="container-xxl flex-grow-1">
    <div class="row">
        <!-- Content area -->
        <div class="content">
          <!-- Basic card -->
          <div class="card">
            <!-- this is the view table for each model -->
            <div class="card-body">
              <div class="card-inner mx-4 my-3">
                <?php if(!$modelStatus){ ?>
                    <div class="col-lg-12">
                      <div class="alert alert-primary text-center">
                        <span>There seems to be no data available</span>
                      </div>
                    </div>
                <?php }else{ 
                    $colClass = ($modelName == 'customer' || $modelName == 'agent' || $modelName == 'superagent') ? "col-lg-8" : 'col-lg-12 mx-3 ml-4 px-4';
                  ?>
                  <div class="row d-flex">
                    <div class="<?= $colClass; ?>">
                        <div class="card">
                          <h5 class="card-header mb-3">Details Info on <?php echo ucwords($pageTitle); ?></h5>
                          <div class="table-responsive text-nowrap">
                            <table class="table">
                              <tbody class="table-border-bottom-0">
                                <?php foreach($modelPayload as $key => $val): ?>
                                <tr>
                                  <?php if(startsWith($val, base_url()) || endsWith($key, 'path') !== false){ ?>
                                    <td><strong>View Image:</strong></td>
                                    <td>
                                      <span><a href="javascript:void();" data-toggle='modal' data-target='#modal-image' class="btn btn-primary">Click here</a>
                                      </span>
                                    </td>

                                    <!-- this is for modal images -->
                                    <div id="modal-image" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
                                      <div class="modal-dialog modal-dialog-top" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                              <h5 class="modal-title"></h5>
                                                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                                    <em class="icon ni ni-cross"></em>
                                                </a>
                                            </div>
                                            <div class="modal-body">
                                              <div class="card h-50 w-75 mx-auto">
                                                <?php 
                                                  $imagePath = ($val) ? $val : base_url('assets/avatar1.jpg');
                                                ?>
                                                <img class="card-img-top" src="<?php echo $imagePath; ?>" alt="image" style="height:23rem;">
                                              </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                      </div>
                                    </div>
                                    <!-- end modal form -->

                                  <?php } else if(startsWith($key, 'date')){ ?>
                                      <td><strong><?php echo removeUnderscore($key); ?>:</strong></td>
                                      <td><?php echo dateFormatter($val); ?></td>
                                    <?php } else if(strtolower($key) == 'status'){ ?>
                                      
                                      <td><strong><?php echo removeUnderscore($key); ?>:</strong></td>
                                      <td class="<?php echo $val == 1 || $val == 'Active' ? 'badge badge-dim badge-success' : 'badge badge-dim badge-danger' ?> my-1"><?php echo $val == 1 || $val == 'Active' ? 'Active' : 'Inactive'; ?></td>
                                    
                                    <?php } else{ ?>

                                    <td><strong><?php echo removeUnderscore($key); ?>:</strong></td>
                                    <td><?php echo $val; ?></td>
                                  <?php } ?>
                                </tr>
                              <?php endforeach; ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                    </div>

                    <?php if($modelName == 'customer' || $modelName == 'agent' || $modelName == 'superagent'): ?>
                    <div class="col-lg-4">
                      <?php
                        $entityID = @$extra[4];
                        $entityType = @$extra['3'];
                        $updateType = null;
                        if($entityType == 'wallet'){
                          $updateType = @$_GET['reference_number'] ?? null;
                        }
                        else if($entityType == 'withdrawal'){
                          $updateType = @$_GET['reference'] ?? null;
                        }

                        if(!$entityID){ ?>
                          <div class="alert alert-info">
                            It seems no model is available. Kindly go back and try again
                          </div>
                        <?php }
                        $deleteLink = base_url("delete/{$modelName}/$entityID");
                      ?>
                      <span class="mb-4" data-item-id="<?php echo $entityID; ?>" data-default='1' data-critical='1'>
                        <a class="btn btn-danger mx-4 mb-4" href="<?php echo $deleteLink; ?>">Delete Account</a>
                      </span>
                      <div class="alert alert-pro alert-primary ml-4">
                          <span class="h4">Wallet Balance</span>
                          <span class="badge badge-pill badge-lg dadge-dark mx-2"><?php echo number_format($walletBalance); ?></span>
                      </div>
                      <div class="my-4 ml-4">
                        <?php if($entityType != 'withdrawal'): ?>
                        <button class="btn btn-primary mb-1" data-toggle='modal' data-target='#modal-fund'>Fund Account Wallet</button>
                        <?php endif; ?>

                        <?php if($walletBalance != 0): ?>
                        <button class="btn btn-danger mb-1" data-toggle='modal' data-target='#modal-deduct'>Deduct Account Wallet</button>
                        <?php endif; ?>

                        <button class="btn btn-info mb-1" data-toggle='modal' data-target='#modal-account'>Account/Info Details</button>

                        <?php if($modelName == 'superagent'): ?>
                        <a class="btn btn-secondary mb-1" href="<?= base_url("vc/admin/view_model/agent/{$modelEntityID}?super_code={$modelPayload['super_code']}"); ?>">View Agents</a>
                        <?php endif; ?>

                        <!-- this is for fund modal form -->
                        <div id="modal-fund" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-top" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title">Fund <i><?php echo $modelPayload['fullname']; ?></i> Wallet</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-dark">
                                        <span class="h4">Wallet Balance</span>
                                        <span class="badge badge-pill badge-lg dadge-dark mx-2"><?php echo number_format($walletBalance); ?></span>
                                    </div>
                                    <form action="" method="post" role="form" id="form_wallet" name="form_wallet" onsubmit='updateAccountWallet("<?= $user_id ?>","fund",$("#fund_amount").val(),"<?= $entityType ?>","<?= $updateType ?>");return false;'>

                                      <div class="form-group mb-3">
                                          <label for="amount">Amount</label>
                                          <input class="form-control" type="number" required name="fund_amount" id="fund_amount" placeholder="Enter amount to be funded e.g 500">
                                      </div>
                                      <input type="hidden" name="isajax">
                                      <input type="hidden" name="userID" value="<?php echo $user_id; ?>">
                                      <div class="form-group">
                                        <button class="btn btn-secondary">Update Fund</button>
                                      </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                          </div>
                        </div>
                        <!-- end fund modal form -->

                        <!-- this is for deduct modal form -->
                        <div id="modal-deduct" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-top" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title">Deduct <i><?php echo $modelPayload['fullname']; ?></i> Wallet</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body">
                                  <div class="alert alert-dark">
                                      <span class="h4">Wallet Balance</span>
                                      <span class="badge badge-pill badge-lg dadge-dark mx-2"><?php echo number_format($walletBalance); ?></span>
                                  </div>
                                  <form action="" method="post" role="form" id="form_wallet" name="form_wallet" onsubmit='updateAccountWallet("<?php echo $user_id ?>","deduct",$("#deduct_amount").val(),"<?= $entityType ?>","<?= $updateType ?>");return false;'>

                                      <div class="form-group mb-3">
                                          <label for="amount">Amount</label>
                                          <input class="form-control" type="number" required name="deduct_amount" id="deduct_amount" placeholder="Enter amount to be deducted e.g 100">
                                      </div>
                                      <input type="hidden" name="isajax">
                                      <input type="hidden" name="userID" value="<?php echo $user_id; ?>">
                                      <div class="form-group">
                                        <button class="btn btn-danger">Deduct Fund</button>
                                      </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                          </div>
                        </div>
                        <!-- end deduct modal form -->

                        <!-- this is for account modal form -->
                        <div id="modal-account" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-top" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title">User Details</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body">
                                  <?php if($user_kyc): ?>
                                    <?php if($user_kyc->account_number != ''): ?>
                                      <div class="card h-50 w-75 mx-auto">
                                      <h5 class="card-title mb-3">Account Name: <span class='text-muted'><?php echo $user_kyc->account_name; ?></span></h5>
                                      <h5 class="card-title mb-3">Account Number: <span class='text-muted'><?php echo $user_kyc->account_number; ?></span></h5>
                                      <h5 class="card-title mb-3">Phone Number on Bvn: <span class='text-muted'><?php echo $user_kyc->phone_on_bvn; ?></span></h5>
                                      <h5 class="card-title mb-3">Bvn Number: <span class='text-muted'><?php echo $user_kyc->bvn_number; ?></span></h5>
                                      <h5 class="card-title mb-3">Bank Name: <span class='text-muted'><?php echo $user_kyc->bank_lists->name ?? 'None'; ?></span></h5>
                                      <h5 class="card-title mb-3">BVN Status: <span class="badge <?php echo $user_kyc->status ? 'badge-success' : 'badge-danger'; ?>"><?php echo $user_kyc->bvn_status ? 'Verified' : 'Not Verified'; ?></span></h5>
                                      <h5 class="card-title mb-3">Dob on Bvn: <span class='text-muted'><?php echo $user_kyc->dob_on_bvn; ?></span></h5>
                                      </div>
                                    <?php else: ?>
                                      <a href="javascript:void(0);" class="btn btn-secondary" id="createAccountHolder">Create Account Now</a>
                                      <script type="text/javascript">
                                          $(document).ready(function(){
                                              $('#createAccountHolder').click(function(e){
                                                  e.preventDefault();
                                                  if(confirm('Are you sure you want to proceed?')){
                                                    $(this).html("creating account...").addClass('disabled').prop('disabled', true);
                                                      let link = "<?= base_url('ajaxData/createAccountHolder'); ?> ";
                                                      let data = new FormData();
                                                      data.append('user_id', "<?= $user_id; ?>");
                                                      data.append('user_type', "<?= $modelName; ?>");
                                                      sendAjax(null,link,data,'post',ajaxFormSuccess);
                                                  }
                                                  
                                              });
                                          });
                                          function ajaxFormSuccess(target,data) {
                                              var data = JSON.parse(data)
                                              if (data.status) {
                                                  setTimeout(function(){
                                                      location.reload();
                                                  }, 5000);
                                              }
                                              else{
                                                  showNotification(data.status, data.message);
                                                  $("#createAccountHolder").removeClass("disabled").removeAttr('disabled').html("Create Account Now");;
                                              }
                                          }
                                      </script>
                                    <?php endif; ?>
                                  <?php else: ?>
                                    <div class="alert alert-info">
                                      There is no available wallet details
                                    </div>
                                  <?php endif; ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                          </div>
                        </div>
                        <!-- end account modal form -->
                      </div>
                      <div class="alert alert-pro alert-secondary ml-4">
                          <span class="h4">Bonus Balance</span>
                          <span class="badge badge-pill badge-lg dadge-dark mx-2"><?php echo number_format($bonusWalletBalance); ?></span>
                      </div>
                    </div>
                    <?php endif; ?>
                  </div>
                  <div class="float-right">
                    <a href="javascript:history.back();" class="btn btn-primary">Go Back</a>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
          <!-- /basic card -->
        </div>
        <!-- /content area -->
    </div>
</div>
<!-- / Content & end for last graph-->
<?php include_once ROOTPATH."template/footer.php"; ?>

<script type="text/javascript">
  $('span[data-critical=1] a').click(function(event){
    event.preventDefault();
    var link = $(this).attr('href');
    var action = $(this).text();
    if (confirm("Are you sure you want to "+action+" item?")) {
      sendAjax(null,link,'','get');
    }
  });

  function updateAccountWallet(userID,type,amount,pageType,pageVal){
    const link = "<?php echo base_url('ajaxData/updateAccountWallet/'); ?>" +'/'+ type;
    if (confirm("Are you sure you want to "+type+" item?")) {
      var data = new FormData();
      data.append('user_id',userID);
      data.append('amount',amount);
      data.append('pageType',pageType);
      data.append('pageVal',pageVal);
      sendAjax(null,link,data,'post');
    }
  }
</script>

