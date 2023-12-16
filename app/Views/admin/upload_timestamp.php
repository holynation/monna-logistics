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
                <h4 class="nk-block-title page-title">Upload Boom Number</h4>
            </div><!-- .nk-block-head-content -->
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->
    <div class="nk-block">
        <div class="card card-preview">
            <div class="card-inner">
                <div class="preview-block">
                    <div class="row g-gs">
                        <div class="col-sm-12">
                            <form action="<?php echo base_url('mc/upload_timestamp'); ?>" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label class="form-label" for="customFileLabel">Upload New Boom Numbers</label>
                                    <div class="form-control-wrap mb-4">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="customFile" name="time-upload">
                                            <label class="custom-file-label" for="customFile">Choose file</label>
                                            <span class='form-text text-muted'>Supported formats: <code> .csv only</code></span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="upload_type" value="upload_timestamp" />
                                    <div class="form-group align-self-center">
                                      <input type="submit" name="btnTimestamp" class="btn btn-primary" value="Upload Timestamp" />
                                    </div>
                                </div>
                            </form>

                            <br/><br/>
                            <?php
                              $tableAction = [];
                              $tableExclude = [];
                              $queryString = "SELECT timestamp_perm.ID, time_stamp_perm,date_created from timestamp_perm";
                              $tableData = $queryHtmlTableObjModel->openTableHeader($queryString,[],null,array('class'=>'nowrap nk-tb-list is-separate table table-bordered table-responsive ',"data-auto-responsive"=>'false'),
                                $tableExclude
                                )
                                ->paging(true,0,50)
                                ->excludeSerialNumber(false)
                                ->appendTableAction($tableAction)
                                ->generateTable();
                              echo $tableData;
                            ?>
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
  function showUpdateForm(target,data) {
     var data = JSON.parse(data);
     if (data.status==false) {
       showNotification(false,data.message);
       return;
     }
    var container = $('#edit-container');
      container.html(data.message);
      //rebind the autoload functions inside
      $('#modal-edit').modal();
   }
  function ajaxFormSuccess(target,data) {
    data = JSON.parse(data);
    showNotification(data.status,data.message);
    if (data.status) {
      location.reload();
    }
  }
</script>