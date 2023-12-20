<?php

require_once APPPATH.'Views/modelconfig/table.php';
require_once APPPATH.'Views/modelconfig/form.php';

if (isset($_GET['export'])) {
  $queryHtmlTableObjModel->export = true;
  $tableWithHeaderModel->export = true;
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
  ->appendEmptyIcon('<i class=las la-dumpster mr-2 mb-2 fs-2x"></i>')
  ->appendQueryString($tableQueryString)
  ->generateTableBody(null,true,0,100,' order by id desc ',$where)
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
<?php require APPPATH."Views/template/header.php"; ?>
<!-- main header @e -->

<!--begin::Main-->
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
  <!--begin::Content container-->
  <div id="kt_app_content_container" class="app-container container-xxl">
    
    <!--begin::Card-->
    <div class="card">
      <!--begin::Card header-->
      <div class="card-header border-0 pt-6">
        <!--begin::Card title-->
        <div class="card-title">
          <!--begin::Search-->
          <div class="d-flex align-items-center position-relative my-1">
            <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
            <span class="svg-icon svg-icon-1 position-absolute ms-6">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor" />
              </svg>
            </span>
            <!--end::Svg Icon-->
            <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search user" />
          </div>
          <!--end::Search-->
        </div>
        <!--begin::Card title-->
        <!--begin::Card toolbar-->
        <div class="card-toolbar">
          <!--begin::Toolbar-->
          <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
            <!--begin::Add user-->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_model">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
            <span class="svg-icon svg-icon-2">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor" />
              </svg>
            </span>
            <!--end::Svg Icon-->Add <?= removeUnderscore($model); ?></button>
            <!--end::Add user-->
          </div>
          <!--end::Toolbar-->
        </div>
        <!--end::Card toolbar-->
      </div>
      <!--end::Card header-->
      <!--begin::Card body-->
      <div class="card-body py-4">
        <?php echo $tableData; ?>
      </div>
      <!--end::Card body-->
    </div>
    <!--end::Card-->


    <!-- modal for batch uploading -->
    <?php if ($configData==false || array_key_exists('has_upload', $configData)==false || $configData['has_upload']): ?>
      <div class="modal fade" id="kt_modal_upload_model" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-650px">
          <!--begin::Modal content-->
          <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header" id="kt_modal_upload_model_header">
              <!--begin::Modal title-->
              <h2 class="fw-bold"><?php echo removeUnderscore($model);  ?> Batch Upload</h2>
              <!--end::Modal title-->
              <!--begin::Close-->
              <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                <span class="svg-icon svg-icon-1">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                  </svg>
                </span>
                <!--end::Svg Icon-->
              </div>
              <!--end::Close-->
            </div>
            <!--end::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
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
            <!--end::Modal body-->
          </div>
          <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
      </div>
    <?php endif; ?>
    <!-- batch uploading end -->

    <!-- modal add form end -->
    <div class="modal fade" id="kt_modal_add_model" tabindex="-1" aria-hidden="true">
      <!--begin::Modal dialog-->
      <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
          <!--begin::Modal header-->
          <div class="modal-header" id="kt_modal_add_model_header">
            <!--begin::Modal title-->
            <h2 class="fw-bold">Create New <?php echo removeUnderscore($model);  ?></h2>
            <!--end::Modal title-->
            <!--begin::Close-->
            <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
              <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
              <span class="svg-icon svg-icon-1">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                  <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                </svg>
              </span>
              <!--end::Svg Icon-->
            </div>
            <!--end::Close-->
          </div>
          <!--end::Modal header-->
          <!--begin::Modal body-->
          <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
            <?php if($showAddCaption): ?>
              <div class="alert alert-danger" style="background-color: #ea2825;color:#fff;">
                  <b><?= $showAddCaption; ?></b>
              </div>
            <?php endif; ?>

            <?php echo $formContent; ?>
          </div>
          <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
      </div>
      <!--end::Modal dialog-->
    </div>
    <!-- modal add form end -->

    <!-- modal for editing form -->
    <div class="modal fade" id="kt_modal_edit_model" tabindex="-1" aria-hidden="true">
      <!--begin::Modal dialog-->
      <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
          <!--begin::Modal header-->
          <div class="modal-header" id="kt_modal_edit_model_header">
            <!--begin::Modal title-->
            <h2 class="fw-bold"><?php echo removeUnderscore($model);  ?> Update</h2>
            <!--end::Modal title-->
            <!--begin::Close-->
            <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
              <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
              <span class="svg-icon svg-icon-1">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                  <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                </svg>
              </span>
              <!--end::Svg Icon-->
            </div>
            <!--end::Close-->
          </div>
          <!--end::Modal header-->
          <!--begin::Modal body-->
          <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
            <?php if(@$editMessageInfo != ""): ?>
              <div class="alert alert-danger" style="background-color: #ea2825;color:#fff;">
                  <b><?php echo @$editMessageInfo; ?></b>
              </div>
              <?php endif; ?>
              <p id="edit-container"> </p>
          </div>
          <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
      </div>
      <!--end::Modal dialog-->
    </div>
    <!-- modal edit form end -->
  </div>
  <!--end::Content container-->
</div>
<!--end::Content-->
</div>
<!--end::Content wrapper-->
</div>
<!--end:::Main-->
</div>
<!--end::Wrapper-->
</div>
<!--end::Page-->
</div>
<!--end::App-->

<!-- footer & JavaScript -->
<?php require APPPATH.'Views/template/footer.php'; ?>

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
    }
</script>

<script type="text/javascript">
  var KTUsersAddUser = function () {
      // Shared variables
      const element = document.getElementById('kt_modal_add_model');
      const modal = new bootstrap.Modal(element);

      // Init add schedule modal
      var initAddUser = () => {
          // Close button handler
          const closeButton = element.querySelector('[data-kt-users-modal-action="close"]');
          closeButton.addEventListener('click', e => {
              e.preventDefault();  
              modal.hide(); 
          });
      }

      return {
          // Public functions
          init: function () {
              initAddUser();
          }
      };
  }();

  // On document ready
  KTUtil.onDOMContentLoaded(function () {
      KTUsersAddUser.init();
  });
</script>