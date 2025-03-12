<?php require APPPATH."Views/template/header.php"; ?>

<!--begin::Main-->
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
	<div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
		<!--begin::Page title-->
		<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
			<!--begin::Title-->
			<h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Invoice Action</h1>
			<!--end::Title-->
			<!--begin::Breadcrumb-->
			<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
				<!--begin::Item-->
				<li class="breadcrumb-item text-muted">
					<a href="<?= base_url('admin/dashboard'); ?>" class="text-muted text-hover-primary">Home</a>
				</li>
				<!--end::Item-->
				<!--begin::Item-->
				<li class="breadcrumb-item">
					<span class="bullet bg-gray-400 w-5px h-2px"></span>
				</li>
				<!--end::Item-->
				<!--begin::Item-->
				<li class="breadcrumb-item text-muted">invoice Management</li>
				<!--end::Item-->
				<li class="breadcrumb-item">
					<span class="bullet bg-gray-400 w-5px h-2px"></span>
				</li>
				<li class="breadcrumb-item text-muted">Invoice</li>
			</ul>
			<!--end::Breadcrumb-->
		</div>
		<!--end::Page title-->
	</div>
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
			</div>
			<!--end::Card header-->
			<!--begin::Card body-->
			<div class="card-body py-4">
				<!--begin::Table-->
				<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
					<!--begin::Table head-->
					<thead>
						<!--begin::Table row-->
						<tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
							<th class="min-w-125px">Customer</th>
							<th class="min-w-125px">Invoice No.</th>
							<th class="min-w-125px">Ship To</th>
							<th class="min-w-125px">Total Amount</th>
							<th class="min-w-125px">Invoice Status</th>
							<th class="text-end min-w-100px">Actions</th>
						</tr>
						<!--end::Table row-->
					</thead>
					<!--end::Table head-->
					<!--begin::Table body-->
					<tbody class="text-gray-600 fw-semibold">
						<?php if($contents): ?>

						<?php foreach($contents as $content): ?>
						<!--begin::Table row-->
						<tr>
							<td class="d-flex align-items-center">
								<div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
									<a href="javascript:void(0);">
										<div class="symbol-label fs-3 bg-light-danger text-danger">
											<?= formatToNameLabel($content['bill_from_name'], true); ?>
										</div>
									</a>
								</div>
								<div class="d-flex flex-column">
									<a href="javascript:void(0);" class="text-gray-800 text-hover-primary mb-1"><?= $content['bill_from_name']; ?></a>
									<span><?= $content['bill_from_phone']; ?></span>
								</div>
							</td>
							<td><?= $content['invoice_no']; ?></td>
							<td><?= $content['bill_to_address']; ?></td>
							<td><?= $content['invoice_total']; ?></td>
							<td>
								<?php if($content['invoice_status'] == \App\Enums\InvoiceStatusEnum::INTRANSIT->value): ?>
									<div class="badge badge-success fw-bold"><?= ucfirst($content['invoice_status']); ?></div>
								<?php elseif($content['invoice_status'] == \App\Enums\InvoiceStatusEnum::PROCESSING->value): ?>
									<div class="badge badge-info fw-bold"><?= ucfirst($content['invoice_status']); ?></div>
								<?php else: ?>
									<div class="badge badge-warning fw-bold"><?= ucfirst($content['invoice_status']); ?></div>
								<?php endif; ?>
							</td>
							<td class="text-end">
								<a class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#kt_modal_add_model-<?= $content['id']; ?>">
									Update Status
								</a>
							</td>
						</tr>

						<!-- modal update action form end -->
						<div class="modal fade" id="kt_modal_add_model-<?= $content['id']; ?>" tabindex="-1" aria-hidden="true">
						  <!--begin::Modal dialog-->
						  <div class="modal-dialog modal-dialog-centered mw-650px">
						    <!--begin::Modal content-->
						    <div class="modal-content">
						      <!--begin::Modal header-->
						      <div class="modal-header" id="kt_modal_add_model_header">
						        <!--begin::Modal title-->
						        <h2 class="fw-bold">Update Invoice Status #<?= $content['invoice_no'] ?></h2>
						        <!--end::Modal title-->
						        <!--begin::Close-->
						        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close-<?= $content['id']; ?>" onclick="modalJavascript(<?= $content['id']; ?>);">
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
						      	$exclude = ['bill_from_name', 'bill_from_phone', 'bill_from_address', 'bill_to_phone' ,'bill_to_email', 'bill_to_city', 'bill_to_country', 'bill_to_postalcode', 'invoice_discount', 'invoice_tax', 'invoice_date', 'invoice_notes', 'invoice_subtotal', 'invoice_total'];

						      	$formContent = $modelFormBuilder->start('invoices_table_'.$content['id'])
						      	->appendUpdateForm('invoices',true,$content['id'],$exclude,'')
						      	->addSubmitLink(null,false)
						      	->appendSubmitButton('Update Status','btn btn-success')
						      	->build();

						      	echo $formContent;
						      	?>
						        
						      </div>
						      <!--end::Modal body-->
						    </div>
						    <!--end::Modal content-->
						  </div>
						  <!--end::Modal dialog-->
						</div>
						<!-- modal update action form end -->

						<!--end::Table row-->
						<?php endforeach; ?>
						<?php else: ?>
						<div class="alert alert-info">
							<p>There is no invoice(s) at the moment</p>
						</div>
						<?php endif; ?>
					</tbody>
					<!--end::Table body-->
				</table>
				<!--end::Table-->
			</div>
			<!--end::Card body-->
		</div>
		<!--end::Card-->
	</div>
	<!--end::Content container-->
</div>
<!--end::Content-->
</div>
<!--end::Content wrapper-->
</div>
<!--end:::Main-->

<?php  require APPPATH.'Views/template/footer.php'; ?>

<!-- update action modal javascript -->
<script type="text/javascript">
	function modalJavascript(id){
		// Shared variables
		const element = document.getElementById(`kt_modal_add_model-${id}`);
		const modal = new bootstrap.Modal(element);

		// Close button handler
		const closeButton = element.querySelector(`[data-kt-users-modal-action="close-${id}"]`);
		closeButton.addEventListener('click', e => {
		    e.preventDefault();  
		    modal.hide(); 
		});
	}
</script>
<!-- end modal update action javascript -->

<script type="text/javascript">
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
	});

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
	"use strict";

	var KTUsersList = function () {
	    // Define shared variables
	    var table = document.getElementById('kt_table_users');
	    var datatable;
	    $.fn.dataTable.ext.errMode = 'throw';

	    // Private functions
	    var initUserTable = function () {
	        // Init datatable --- more info on datatables: https://datatables.net/manual/
	        datatable = $(table).DataTable({
	            "info": false,
	            'order': [],
	            "pageLength": 10,
	            "lengthChange": false,
	        });
	    }

	    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
	    var handleSearchDatatable = () => {
	        const filterSearch = document.querySelector('[data-kt-user-table-filter="search"]');
	        filterSearch.addEventListener('keyup', function (e) {
	            datatable.search(e.target.value).draw();
	        });
	    }

	    return {
	        // Public functions  
	        init: function () {
	            if (!table) {
	                return;
	            }

	            initUserTable();
	            handleSearchDatatable();

	        }
	    }
	}();

	// On document ready
	KTUtil.onDOMContentLoaded(function () {
	    KTUsersList.init();
	});
</script>