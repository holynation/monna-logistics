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
			<h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Transaction List</h1>
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
				<li class="breadcrumb-item text-muted">Finance Management</li>
				<!--end::Item-->
				<li class="breadcrumb-item">
					<span class="bullet bg-gray-400 w-5px h-2px"></span>
				</li>
				<li class="breadcrumb-item text-muted">Transaction</li>
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
							<th class="min-w-125px">Description</th>
							<th class="min-w-125px">Amount Paid</th>
							<th class="min-w-125px">Payment Status</th>
							<th class="min-w-125px">Payment Date</th>
							<th class="min-w-125px"></th>
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
											<?= formatToNameLabel($content['fullname'], true); ?>
										</div>
									</a>
								</div>
								<div class="d-flex flex-column">
									<a href="javascript:void(0);" class="text-gray-800 text-hover-primary mb-1"><?= $content['fullname']; ?></a>
									<span><?= $content['email']; ?></span>
								</div>
							</td>
							<td><?= $content['invoice_no']; ?></td>
							<td><?= $content['description']; ?></td>
							<td><?= $content['amount_paid']; ?></td>
							<td>
								<?php if($content['payment_status'] == 'not paid'): ?>
									<div class="badge badge-danger fw-bold"><?= ucfirst($content['payment_status']); ?></div>
								<?php elseif($content['payment_status'] == 'paid'): ?>
									<div class="badge badge-success fw-bold"><?= ucfirst($content['payment_status']); ?></div>
								<?php else: ?>
									<div class="badge badge-warning fw-bold"><?= ucfirst($content['payment_status']); ?></div>
								<?php endif; ?>
							</td>
							<td><?= $content['payment_date']; ?></td>
							<td>
								<div data-item-id="<?php echo $content['id']; ?>" data-default='1' data-critical='1' >
                                    <a href="<?= base_url("changestatus/invoice_transaction/approved/{$content['id']}") ?>" class="btn btn-light-primary <?= $content['payment_status'] == 'paid' ? 'disabled' : '' ?>" >Approve Payment</a>
                                 </div>
							</td>
							<td class="text-end">
								<a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
								<!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
								<span class="svg-icon svg-icon-5 m-0">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
									</svg>
								</span>
								<!--end::Svg Icon--></a>
								<!--begin::Menu-->
								<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
									<!--begin::Menu item-->
									<div class="menu-item px-3" data-item-id="<?php echo $content['id']; ?>" data-default='1' data-critical='1'>
										<a href="<?= base_url("changestatus/invoice_transaction/rejected/{$content['id']}") ?>" class="menu-link px-3" <?= $content['payment_status'] == 'not paid' ? 'disabled' : '' ?>>Disapproved</a>
									</div>
									<!--end::Menu item-->
								</div>
								<!--end::Menu-->
							</td>
							<!--end::Action=-->
						</tr>
						<!--end::Table row-->
						<?php endforeach; ?>
						<?php else: ?>
						<div class="alert alert-info">
							<p>There is no invoice transactions at the moment</p>
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