<?php require APPPATH . "Views/template/header.php";?>

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
			<h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Create</h1>
			<!--end::Title-->
			<!--begin::Breadcrumb-->
			<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
				<!--begin::Item-->
				<li class="breadcrumb-item text-muted">
					<a href="<?=base_url('admin/dashboard');?>" class="text-muted text-hover-primary">Home</a>
				</li>
				<!--end::Item-->
				<!--begin::Item-->
				<li class="breadcrumb-item">
					<span class="bullet bg-gray-400 w-5px h-2px"></span>
				</li>
				<!--end::Item-->
				<!--begin::Item-->
				<li class="breadcrumb-item text-muted">Invoice Management</li>
				<!--end::Item-->
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
		<!--begin::Form-->
		<form action="<?=base_url('invoices/process');?>" id="kt_invoice_form" method="post">
		<div class="d-flex flex-column flex-lg-row">
			<!--begin::Content-->
			<div class="flex-lg-row-fluid mb-10 mb-lg-0 me-lg-7 me-xl-1">

			<?php if ($webSessionManager->getFlashMessage('error')): ?>
				<!--begin::Alert-->
				<div class="alert alert-dismissible bg-light-danger border border-danger d-flex flex-column flex-sm-row p-5 mb-10">
				    <!--begin::Icon-->
				    <i class="ki-duotone ki-search-list fs-2hx text-success me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
				    <!--end::Icon-->

				    <!--begin::Wrapper-->
				    <div class="d-flex flex-column pe-0 pe-sm-10">
				        <!--begin::Title-->
				        <h5 class="mb-1">Error Message</h5>
				        <!--end::Title-->

				        <!--begin::Content-->
				        <?php foreach ($webSessionManager->getFlashMessage('error') as $err): ?>
				        <span><?=$err;?></span>
				    	<?php endforeach;?>
				        <!--end::Content-->
				    </div>
				    <!--end::Wrapper-->

				    <!--begin::Close-->
				    <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
				        <i class="ki-duotone ki-cross fs-1 text-success"><span class="path1"></span><span class="path2"></span></i>
				    </button>
				    <!--end::Close-->
				</div>
				<!--end::Alert-->
			<?php endif;?>

				<!--begin::Card-->
				<div class="card">
					<!--begin::Card body-->
					<div class="card-body p-12">
							<!--begin::Wrapper-->
							<div class="d-flex flex-column align-items-start flex-xxl-row">
								<!--begin::Input group-->
								<div class="d-flex align-items-center flex-equal fw-row me-4 order-2" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Specify invoice date">
									<!--begin::Date-->
									<div class="fs-6 fw-bold text-gray-700 text-nowrap">Ship Date:</div>
									<!--end::Date-->
									<!--begin::Input-->
									<div class="position-relative d-flex align-items-center w-150px">
										<!--begin::Datepicker-->
										<input class="form-control form-control-transparent fw-bold pe-5" placeholder="Select date" name="invoice_date" value="<?=old('invoice_date');?>" />
										<!--end::Datepicker-->
										<!--begin::Icon-->
										<!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
										<span class="svg-icon svg-icon-2 position-absolute ms-4 end-0">
											<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
											</svg>
										</span>
										<!--end::Svg Icon-->
										<!--end::Icon-->
									</div>
									<!--end::Input-->
								</div>
								<!--end::Input group-->
								<!--begin::Input group-->
								<div class="d-flex flex-center flex-equal fw-row text-nowrap order-1 order-xxl-2 me-4" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Enter invoice number">
									<span class="fs-2x fw-bold text-gray-800">Invoice #</span>
									<input type="text" class="form-control form-control-flush fw-bold text-muted fs-3 w-125px" value="<?=old('invoice_no', $invoiceNum);?>" placehoder="..." name='invoice_no'/>
								</div>
								<!--end::Input group-->
								<div class="d-flex flex-center flex-equal fw-row text-nowrap order-1 order-xxl-2 me-4" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Enter tracking number">
									<span class="fs-2x fw-bold text-gray-800">Tracking No.</span>
									<input type="text" class="form-control form-control-flush fw-bold text-muted fs-3 w-125px" value="<?=old('track_number', $trackNum);?>" placehoder="..." name="track_number" />
								</div>
								<!--end::Input group-->
							</div>
							<!--end::Top-->
							<!--begin::Separator-->
							<div class="separator separator-dashed my-10"></div>
							<!--end::Separator-->
							<!--begin::Wrapper-->
							<div class="mb-0">
								<!--begin::Row-->
								<div class="row gx-10 mb-5">
									<!--begin::Col-->
									<div class="col-lg-6">
										<label class="form-label fs-6 fw-bold text-gray-700 mb-3">Bill From</label>
										<div class="mb-5">
											<select class="form-control" name="customer" id="customer">
												<option value=''>...choose customer...</option>
												<?=$customerOptions;?>
											</select>
										</div>
									</div>
									<!--end::Col-->
								</div>
								<!--end::Row-->
								<!--begin::Row-->
								<div class="row gx-10 mb-5">
									<!--begin::Col-->
									<div class="col-lg-6">
										<label class="form-label fs-6 fw-bold text-gray-700 mb-3">Ship To Details</label>
										<!--begin::Input group-->
										<div class="mb-5">
											<input type="text" class="form-control form-control-solid" placeholder="Name" name="bill_to_name" value="<?=old('bill_to_name');?>" />
										</div>
										<!--end::Input group-->
										<!--begin::Input group-->
										<div class="mb-5">
											<input type="text" class="form-control form-control-solid" placeholder="Email" name="bill_to_email" value="<?=old('bill_to_email');?>" />
										</div>
										<!--end::Input group-->
										<div class="mb-5">
											<input type="text" class="form-control form-control-solid" placeholder="Phone number" name="bill_to_phone" value="<?=old('bill_to_phone');?>" />
										</div>
									</div>
									<!--end::Col-->
									<!--begin::Col-->
									<div class="col-lg-6">
										<label class="form-label fs-6 fw-bold text-gray-700 mb-3">Bill To Address</label>
										<!--begin::Input group-->
										<div class="mb-5">
											<input type="text" class="form-control form-control-solid" placeholder="City" name="bill_to_city" value="<?=old('bill_to_city');?>" />
										</div>
										<!--end::Input group-->
										<!--begin::Input group-->
										<div class="mb-5">
											<input type="text" class="form-control form-control-solid" placeholder="Country" name="bill_to_country" value="<?=old('bill_to_country');?>" />
										</div>
										<!--end::Input group-->
										<div class="mb-5">
											<input type="text" class="form-control form-control-solid" placeholder="Address number" name="bill_to_address" value="<?=old('bill_to_address');?>" />
										</div>
										<div class="mb-5">
											<input type="text" class="form-control form-control-solid" placeholder="Postal Code" name="bill_to_postalcode" value="<?=old('bill_to_postalcode');?>" />
										</div>
									</div>
									<!--end::Col-->
								</div>
								<!--end::Row-->
								<!--begin::Table wrapper-->
								<div class="table-responsive mb-10">
									<!--begin::Table-->
									<table class="table g-5 gs-0 mb-0 fw-bold text-gray-700" data-kt-element="items">
										<!--begin::Table head-->
										<thead>
											<tr class="border-bottom fs-7 fw-bold text-gray-700 text-uppercase">
												<th class="min-w-300px w-475px">Item</th>
												<th class="min-w-100px w-100px">Weight(Kg)</th>
												<th class="min-w-150px w-150px">Rate</th>
												<th class="min-w-150px w-150px">Value</th>
												<th class="min-w-100px w-150px text-end">Total</th>
												<th class="min-w-75px w-75px text-end">Action</th>
											</tr>
										</thead>
										<!--end::Table head-->
										<!--begin::Table body-->
										<tbody>
											<tr class="border-bottom border-bottom-dashed" data-kt-element="item">
												<td class="pe-7">
													<input type="text" class="form-control form-control-solid mb-2" name="description[]" placeholder="Item name" value="<?=old('description[]');?>" />
												</td>
												<td class="ps-0">
													<input class="form-control form-control-solid" type="number" min="1" name="weight[]" placeholder="1" value="<?=old('weight[]', 1);?>" data-kt-element="weight" />
												</td>
												<td>
													<select class="form-control form-control-solid text-end" name="rates[]" id="rates" data-kt-element="rates">
														<option value=''>choose rates</option>
														<?=$rateOptions;?>
													</select>
													<input type="hidden" name="custom_prices[]" value="" data-kt-element="custom_prices" />
												</td>
												<td>
													<input type="text" class="form-control form-control-solid text-end" name="custom_value[]" placeholder="0.00" data-kt-element="custom_value" />
												</td>
												<td class="pt-8 text-end text-nowrap"> <!-- &#x20A6; -->
												<span data-kt-element="total">0.00</span></td>
												<td class="pt-5 text-end">
													<button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-kt-element="remove-item">
														<!--begin::Svg Icon | path: icons/duotune/general/gen027.svg-->
														<span class="svg-icon svg-icon-3">
															<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																<path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor" />
																<path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor" />
																<path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor" />
															</svg>
														</span>
														<!--end::Svg Icon-->
													</button>
												</td>
											</tr>
										</tbody>
										<!--end::Table body-->
										<!--begin::Table foot-->
										<tfoot>
											<tr class="border-top border-top-dashed align-top fs-6 fw-bold text-gray-700">
												<th class="text-primary">
													<button class="btn btn-primary" data-kt-element="add-item">Add item</button>
												</th>
												<th colspan="2" class="border-bottom border-bottom-dashed ps-0">
													<div class="d-flex flex-column align-items-start">
														<div class="fs-5">Subtotal</div>
													</div>
												</th>
												<th colspan="2" class="border-bottom border-bottom-dashed text-end">
												<span data-kt-element="sub-total">0.00</span></th>
											</tr>
											<tr class="align-top fw-bold text-gray-700">
												<th></th>
												<th colspan="2" class="fs-4 ps-0">Add VAT
													<span class="text-muted">(The amount should not be in percentage(%))</span>
												</th>
												<th colspan="2" class="text-end fs-4 text-nowrap">
												<span>
													<input type="text" class="form-control form-control-solid text-end" name="tax" placeholder="0.00" value="7.5" data-kt-element="tax" />
												</span>
											</th>
											</tr>
											<tr class="align-top fw-bold text-gray-700">
												<th></th>
												<th colspan="2" class="fs-4 ps-0">Packaging Fee</th>
												<th colspan="2" class="text-end fs-4 text-nowrap">
												<span>
													<input type="text" class="form-control form-control-solid text-end" name="package_fee" placeholder="0.00" value="0.00" data-kt-element="package_fee" />
												</span></th>
											</tr>
											<tr class="align-top fw-bold text-gray-700">
												<th></th>
												<th colspan="2" class="fs-4 ps-0">Phytosanitary Certificate</th>
												<th colspan="2" class="text-end fs-4 text-nowrap">
												<span>
													<input type="text" class="form-control form-control-solid text-end" name="certificate_fee" placeholder="0.00" value="0.00" data-kt-element="certificate_fee" />
												</span></th>
											</tr>
											<tr class="align-top fw-bold text-gray-700">
												<th></th>
												<th colspan="2" class="fs-4 ps-0">Add discount</th>
												<th colspan="2" class="text-end fs-4 text-nowrap">
												<span>
													<input type="text" class="form-control form-control-solid text-end" name="discount" placeholder="0.00" value="0.00" data-kt-element="discount" />
												</span></th>
											</tr>
											<tr class="align-top fw-bold text-gray-700">
												<th></th>
												<th colspan="2" class="fs-4 ps-0">Total</th>
												<th colspan="2" class="text-end fs-4 text-nowrap">
												<span data-kt-element="grand-total">0.00</span></th>
											</tr>
											<tr class="align-top fw-bold text-gray-700">
												<th></th>
												<th colspan="3" class="fs-7 ps-0">
													<span class="text-danger">NOTE: Both VAT and discount values would only be calculated during processing after submission, so don't be bothered when you noticed nothing is happening to the input.</span>
												</th>
											</tr>
										</tfoot>
										<!--end::Table foot-->
									</table>
								</div>
								<!--end::Table-->

								<!--begin::Item template-->
								<table class="table d-none" data-kt-element="item-template">
									<tr class="border-bottom border-bottom-dashed" data-kt-element="item">
										<td class="pe-7">
											<input type="text" class="form-control form-control-solid mb-2" name="description[]" placeholder="Item name" />
										</td>
										<td class="ps-0">
											<input class="form-control form-control-solid" type="number" min="1" name="weight[]" placeholder="1" data-kt-element="weight" />
										</td>
										<td>
											<select class="form-control form-control-solid text-end" name="rates[]" id="rates" data-kt-element="rates">
												<option value=''>choose rates</option>
												<?=$rateOptions;?>
											</select>
											<input type="hidden" name="custom_prices[]" value="" data-kt-element="custom_prices" />
										</td>
										<td>
											<input type="text" class="form-control form-control-solid text-end" name="custom_value[]" placeholder="0.00" data-kt-element="custom_value" />
										</td>
										<td class="pt-8 text-end"><!-- &#x20A6; -->
										<span data-kt-element="total">0.00</span></td>
										<td class="pt-5 text-end">
											<button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-kt-element="remove-item">
												<!--begin::Svg Icon | path: icons/duotune/general/gen027.svg-->
												<span class="svg-icon svg-icon-3">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor" />
														<path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor" />
														<path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</button>
										</td>
									</tr>
								</table>
								<table class="table d-none" data-kt-element="empty-template">
									<tr data-kt-element="empty">
										<th colspan="5" class="text-muted text-center py-10">No items</th>
									</tr>
								</table>
								<!--end::Item template-->
								<!--begin::Notes-->
								<div class="mb-0">
									<label class="form-label fs-6 fw-bold text-gray-700">Notes</label>
									<textarea name="invoice_notes" class="form-control form-control-solid" rows="3" placeholder="Thanks for your business"></textarea>
								</div>
								<!--end::Notes-->
							</div>
							<!--end::Wrapper-->

					</div>
					<!--end::Card body-->
				</div>
				<!--end::Card-->
			</div>
			<!--end::Content-->
			<!--begin::Sidebar-->
			<div class="flex-lg-auto min-w-lg-300px">
				<!--begin::Card-->
				<div class="card" data-kt-sticky="true" data-kt-sticky-name="invoice" data-kt-sticky-offset="{default: false, lg: '200px'}" data-kt-sticky-width="{lg: '250px', lg: '300px'}" data-kt-sticky-left="auto" data-kt-sticky-top="150px" data-kt-sticky-animation="false" data-kt-sticky-zindex="95">
					<!--begin::Card body-->
					<div class="card-body p-10">
						<!--begin::Separator-->
						<div class="separator separator-dashed mb-8"></div>
						<!--end::Separator-->
						<!--begin::Input group-->
						<div class="mb-8">
							<!--begin::Option-->
							<label class="form-check form-switch form-switch-sm form-check-custom form-check-solid flex-stack mb-5">
								<span class="form-check-label ms-0 fw-bold fs-6 text-gray-700">Must pay</span>
								<input class="form-check-input" type="checkbox" checked="checked" value="" name="must_pay" />
							</label>
							<!--end::Option-->
						</div>
						<!--end::Input group-->
						<!--begin::Separator-->
						<div class="separator separator-dashed mb-8"></div>
						<!--end::Separator-->
						<!--begin::Actions-->
						<div class="mb-0">
							<button type="submit" class="btn btn-primary w-100" id="kt_invoice_submit_button">
							<!--begin::Svg Icon | path: icons/duotune/general/gen016.svg-->
							<span class="svg-icon svg-icon-3">
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M15.43 8.56949L10.744 15.1395C10.6422 15.282 10.5804 15.4492 10.5651 15.6236C10.5498 15.7981 10.5815 15.9734 10.657 16.1315L13.194 21.4425C13.2737 21.6097 13.3991 21.751 13.5557 21.8499C13.7123 21.9488 13.8938 22.0014 14.079 22.0015H14.117C14.3087 21.9941 14.4941 21.9307 14.6502 21.8191C14.8062 21.7075 14.9261 21.5526 14.995 21.3735L21.933 3.33649C22.0011 3.15918 22.0164 2.96594 21.977 2.78013C21.9376 2.59432 21.8452 2.4239 21.711 2.28949L15.43 8.56949Z" fill="currentColor" />
									<path opacity="0.3" d="M20.664 2.06648L2.62602 9.00148C2.44768 9.07085 2.29348 9.19082 2.1824 9.34663C2.07131 9.50244 2.00818 9.68731 2.00074 9.87853C1.99331 10.0697 2.04189 10.259 2.14054 10.4229C2.23919 10.5869 2.38359 10.7185 2.55601 10.8015L7.86601 13.3365C8.02383 13.4126 8.19925 13.4448 8.37382 13.4297C8.54839 13.4145 8.71565 13.3526 8.85801 13.2505L15.43 8.56548L21.711 2.28448C21.5762 2.15096 21.4055 2.05932 21.2198 2.02064C21.034 1.98196 20.8409 1.99788 20.664 2.06648Z" fill="currentColor" />
								</svg>
							</span>
							<!--end::Svg Icon-->Submit Invoice</button>
						</div>
						<!--end::Actions-->
					</div>
					<!--end::Card body-->
				</div>
				<!--end::Card-->
			</div>
			<!--end::Sidebar-->
		</div>
		</form>
	</div>
	<!--end::Content container-->
</div>
<!--end::Content-->
</div>
<!--end::Content wrapper-->
</div>
<!--end:::Main-->

<?php require APPPATH . 'Views/template/footer.php';?>

<script type="text/javascript">
	"use strict";

	// Class definition
	var KTAppInvoicesCreate = function () {
	    var form;

		// Private functions
		var updateTotal = function() {
			var items = [].slice.call(form.querySelectorAll('[data-kt-element="items"] [data-kt-element="item"]'));
			var grandTotal = 0;

			var format = wNumb({
				//prefix: '$ ',
				decimals: 2,
				thousand: ','
			});

			items.map(function (item) {
	            let quantity = item.querySelector('[data-kt-element="weight"]');
				let price = item.querySelector('[data-kt-element="rates"]');
				let customPrice = item.querySelector('[data-kt-element="custom_prices"]');

				var priceValue = price.value;
				var customPriceValue = customPrice.value;
				if(priceValue){
					let temp = priceValue.split('::');
					priceValue = format.from(temp[1]);
				}
				priceValue = (!priceValue || priceValue < 0) ? 0 : priceValue;
				customPriceValue = priceValue;

				var quantityValue = parseInt(quantity.value);
				quantityValue = (!quantityValue || quantityValue < 0) ?  1 : quantityValue;

				quantity.value = quantityValue;
				customPrice.value = customPriceValue;

				item.querySelector('[data-kt-element="total"]').innerText = format.to(priceValue * quantityValue);

				grandTotal += priceValue * quantityValue;
			});

			form.querySelector('[data-kt-element="sub-total"]').innerText = format.to(grandTotal);
			form.querySelector('[data-kt-element="grand-total"]').innerText = format.to(grandTotal);
		}

		var handleEmptyState = function() {
			if (form.querySelectorAll('[data-kt-element="items"] [data-kt-element="item"]').length === 0) {
				var item = form.querySelector('[data-kt-element="empty-template"] tr').cloneNode(true);
				form.querySelector('[data-kt-element="items"] tbody').appendChild(item);
			} else {
				KTUtil.remove(form.querySelector('[data-kt-element="items"] [data-kt-element="empty"]'));
			}
		}

		var handeForm = function (element) {
			// Add item
			form.querySelector('[data-kt-element="items"] [data-kt-element="add-item"]').addEventListener('click', function(e) {
				e.preventDefault();

				var item = form.querySelector('[data-kt-element="item-template"] tr').cloneNode(true);

				form.querySelector('[data-kt-element="items"] tbody').appendChild(item);

				handleEmptyState();
				updateTotal();
			});

			// Remove item
			KTUtil.on(form, '[data-kt-element="items"] [data-kt-element="remove-item"]', 'click', function(e) {
				e.preventDefault();

				KTUtil.remove(this.closest('[data-kt-element="item"]'));

				handleEmptyState();
				updateTotal();
			});

			// Handle price and quantity changes
			KTUtil.on(form, '[data-kt-element="items"] [data-kt-element="weight"], [data-kt-element="items"] [data-kt-element="rates"]', 'change', function(e) {
				e.preventDefault();

				updateTotal();
			});
		}

		var initForm = function(element) {
			// Due date. For more info, please visit the official plugin site: https://flatpickr.js.org/
			var invoiceDate = $(form.querySelector('[name="invoice_date"]'));
			invoiceDate.flatpickr({
				enableTime: false,
				dateFormat: "Y-m-d",
			});
		}

		// Public methods
		return {
			init: function(element) {
	            form = document.querySelector('#kt_invoice_form');

				handeForm();
	            initForm();
				updateTotal();
	        }
		};
	}();

	// On document ready
	KTUtil.onDOMContentLoaded(function () {
	    KTAppInvoicesCreate.init();
	});

</script>

<script type="text/javascript">
	$(document).ready(function(e){
		$('#kt_invoice_form').submit(function(e){
			e.preventDefault();
			submitAjaxForm($(this));
		})
	});

	function ajaxFormSuccess(target,data) {
	  data = JSON.parse(data);
	  if (data.status) {
	    $('form').trigger('reset');
	    setTimeout(() => {
	    	location.reload();
	    }, 2000);
	  }
	  showNotification(data.status,data.message);
	}
</script>
