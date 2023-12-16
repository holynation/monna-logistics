<?php

require_once APPPATH.'Views/modelconfig/table.php';
require_once APPPATH.'Views/modelconfig/form.php';

if (isset($_GET['export'])) {
  $queryHtmlTableObjModel->export=true;
  $tableWithHeaderModel->export=true;
}

$tableData = null;

if($query) {
  $query.= ' '.$where;
  if($searchOrderBy){
    $countFil = 0;
    $tempOrder='';
    foreach($searchOrderBy as $valFilter){
      $tempOrder .= $countFil == 0 ? " $valFilter " : " , $valFilter ";
      $countFil++;
    }
    $query .= "order by $tempOrder desc";
  }
  $tableData = $queryHtmlTableObjModel->openTableHeader($query,array(),null,$tableAttr,$tableExclude)
    ->excludeSerialNumber(false)
    // ->paging(true,0,50)
    ->appendTableAction($tableAction,null)
    // ->appendQueryString($tableQueryString)
    ->appendCheckBox($checkBox,array('class'=>'form-control'))
    ->generateTable();
}
else{
  $tableData = $tableWithHeaderModel->openTableHeader($model,$tableAttr,$tableExclude,true)
  ->excludeSerialNumber(false)
  ->appendTableAction($tableAction)
  ->appendEmptyIcon('<i class="icon-stack-empty mr-2 mb-2 icon-2x"></i>')
  ->appendQueryString($tableQueryString)
  ->generateTableBody(null,true,0,100,' order by ID desc ',$where)
  // ->pagedTable(true,20)
  ->generate();
}

?>

<?php
$modelPath = null;
$extra = "";

$formContent = $modelFormBuilder->start($model.'_table')
->appendInsertForm($model,true,$hidden,'',$showStatus,$exclude)
->addSubmitLink($modelPath)
->appendExtra($extra)
->appendResetButton('Reset','btn btn-lg btn-danger')
->appendSubmitButton($submitLabel,'btn btn-lg btn-primary')
->build();
?>


<!-- main header @s -->
<?php include_once ROOTPATH.'template/header.php'; ?>
<!-- main header @e -->

    <!-- content @s -->
    <div class="nk-content ">
      <div class="container-fluid">
        <div class="nk-content-inner">
          <div class="nk-content-body">
            <div class="components-preview wide-lg mx-auto">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title"><?php echo $tableTitle; ?></h3>
                            <div class="nk-block-des text-soft">
                                <!-- <p>You have total 2,595 users.</p> -->
                            </div>
                        </div>
                        <!-- .nk-block-head-content -->
                        <div class="nk-block-head-content">
                            <div class="toggle-wrap nk-block-tools-toggle">
                                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                                <div class="toggle-expand-content" data-content="more-options">
                                    <ul class="nk-block-tools g-3">
                                        <li class="nk-block-tools-opt">
                                            <?php if($show_add): ?>
                                                <a href="#" class="btn btn-icon btn-primary d-md-none mx-2 px-2" data-toggle='modal' data-target='#myModal'><em class="icon ni ni-plus"></em> Add</a>
                                                <a href="#" class="btn btn-primary d-none d-md-inline-flex" data-toggle='modal' data-target='#myModal'><em class="icon ni ni-plus"></em><span>Add</span>
                                                </a>

                                                <?php if($has_upload): ?>
                                                <a href="#" class="btn btn-icon btn-primary d-md-none mx-2 px-2" data-toggle='modal' data-target='#modal-upload'><em class="icon ni ni-plus"></em> Batch Upload</a>
                                                <a href="#" class="btn btn-primary d-none d-md-inline-flex ml-1" data-toggle='modal' data-target='#modal-upload'><em class="icon ni ni-upload-cloud"></em><span>Batch Upload</span>
                                                </a>
                                                <?php endif; ?> <!-- end batch upload -->
                                            <?php endif; ?> <!-- end the show add -->
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div><!-- .nk-block-head-content -->
                    </div><!-- .nk-block-between -->
                </div><!-- .nk-block-head -->

                <div class="nk-block nk-block-lg">
                    <!-- here is the section for table filter option on the server level -->
                    <?php include_once APPPATH."Views/modelconfig/filter.php"; ?>
                    <!-- here is the end for table filter -->

                    <div class="card card-preview">
                      <div class="card-inner">
                        <!-- <table class="datatable-init nowrap nk-tb-list is-separate" data-auto-responsive="false"> -->
                          <?php echo $tableData; ?>
                      </div>
                    </div>
                </div> <!-- nk-block -->
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- content @e -->

    <!-- modal for batch uploading -->
    <?php if ($configData==false || array_key_exists('has_upload', $configData)==false || $configData['has_upload']): ?>
      <div class="modal modal-default fade" id="modal-upload">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><?php echo removeUnderscore($model) ?> Batch Upload</h5>
                  <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                      <em class="icon ni ni-cross"></em>
                  </a>
            </div>
            <div class="modal-body">
              <?php
                $batchUrl = "mc/template/$model?exec=name";
                $batchActionUrl = "mc/sFile/$model";
              ?>
              <div>
                <a  class='btn btn-info' href="<?=base_url($batchUrl)?>">Download Template</a>
              </div>
              <br/>
              <h5>Upload <?php echo removeUnderscore($model) ?></h5>
              <form method="post" action="<?php echo base_url($batchActionUrl) ?>" enctype="multipart/form-data">
                <div class="form-group">
                  <input type="file" name="bulk-upload" class="form-control">
                  <input type="hidden" name="MAX_FILE_SIZE" value="4194304">
                </div>
                <div class="form-group">
                  <input type="submit" class='btn btn-lg btn-primary' name="submit" value="Upload">
                </div>
              </form>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
    <?php endif; ?>
    <!-- batch uploading end -->

    <!-- add modal -->
    <div class="modal fade" tabindex="-1" id="myModal" role="dialog">
      <div class="modal-dialog modal-dialog-top" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title"><?php echo removeUnderscore($model);  ?> Record Form</h5>
                  <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                      <em class="icon ni ni-cross"></em>
                  </a>
              </div>
              <div class="modal-body">
                <?php if($showAddCaption): ?>
                  <div class="alert alert-danger" style="background-color: #ea2825;color:#fff;">
                      <b><?= $showAddCaption; ?></b>
                  </div>
                <?php endif; ?>
                <?php echo $formContent; ?>
              </div>
          </div>
      </div>
    </div>

    <!-- modal for editing form -->
    <div id="modal-edit" class="modal fade animated" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><?php echo removeUnderscore($model);  ?> Update</h5>
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
            </div>
            <div class="modal-body">
              <?php if(@$editMessageInfo != ""): ?>
                <div class="alert alert-danger" style="background-color: #ea2825;color:#fff;">
                    <b><?php echo @$editMessageInfo; ?></b>
                </div>
                <?php endif; ?>
                <p id="edit-container"> </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" id="close" data-dismiss="modal">Close
                </button>
            </div>
        </div>
      </div>
    </div>
    <!-- modal edit form end -->

    <!-- footer & JavaScript -->
<?php include_once ROOTPATH.'template/footer.php'; ?>
<script>
    var inserted=false;
      $(document).ready(function($) {
        $('.modal').on('hidden.bs.modal', function (e) {
          if (inserted) {
            inserted = false;
            location.reload();
          }
      });
      $('.close').click(function(event) {
        if (inserted) {
          inserted = false;
          location.reload();
        }
      });
      $('li[data-ajax-edit=1] a').click(function(event){
        event.preventDefault();
        let link = $(this).attr('href');
        let action = $(this).text();
        sendAjax(null,link,'','get',showUpdateForm);
      });
    });

    function showUpdateForm(target,data) {
      var data = JSON.parse(data);
      if (data.status==false) {
        showNotification(false,data.message);
        return;
      }

       let container = $('#edit-container');
       container.html(data.message);
       //rebind the autoload functions inside
       $('#modal-edit').modal();
    }

    function ajaxFormSuccess(target,data) {
      data = JSON.parse(data);
      if (data.status) {
        inserted = true;
        $('form').trigger('reset');
        // location.reload();
      }
      showNotification(data.status,data.message);
      let btnSubmit = $("input[type=submit], #refreshBankList");
      btnSubmit.removeClass('disabled');
      btnSubmit.prop('disabled', false);
      btnSubmit.html('Submit');
      if (typeof target ==='undefined') {
        location.reload();
      }
    }
</script>
<script type="text/javascript">
  (function (NioApp, $) { 
    function showNotify(data){
      $('#notification').on("click", function (e) {
          e.preventDefault();
          toastr.clear();
          let status = data.status;
          if(status){
              NioApp.Toast(data.message, 'success',{
                  position: 'bottom-left'
              });
          }else{
              NioApp.Toast(data.message, 'error',{
                  position: 'bottom-left'
              });
          }
          
      });
    }
  })(NioApp, jQuery);
</script>