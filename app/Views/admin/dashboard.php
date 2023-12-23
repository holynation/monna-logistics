<?php require APPPATH."Views/template/header.php"; ?>

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
                <!--begin::Row-->
                <div class="row gy-5 g-xl-10">
                    <!--begin::Col-->
                    <div class="col-4 mb-xl-10">
                        <div class="card overflow-hidden h-md-50 mb-5 mb-xl-10">
                            <!--begin::Card body-->
                            <div class="card-body d-flex justify-content-between flex-column px-0 pb-0">
                                <!--begin::Statistics-->
                                <div class="mb-4 px-9">
                                    <!--begin::Info-->
                                    <div class="d-flex align-items-center mb-2">
                                        <!--begin::Value-->
                                        <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2"><?= $countData['customer']; ?></span>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Info-->
                                    <!--begin::Description-->
                                    <span class="fs-6 fw-semibold text-gray-400">Total Customers</span>
                                    <!--end::Description-->
                                </div>
                                <!--end::Statistics-->
                                <!--begin::Chart-->
                                <div class="min-h-auto" style="height: 125px"></div>
                                <!--end::Chart-->
                            </div>
                            <!--end::Card body-->
                        </div>
                    </div>
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-4 mb-xl-10">
                        <div class="card overflow-hidden h-md-50 mb-5 mb-xl-10">
                            <!--begin::Card body-->
                            <div class="card-body d-flex justify-content-between flex-column px-0 pb-0">
                                <!--begin::Statistics-->
                                <div class="mb-4 px-9">
                                    <!--begin::Info-->
                                    <div class="d-flex align-items-center mb-2">
                                        <!--begin::Value-->
                                        <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2"><?= $countData['invoice']; ?></span>
                                        <!--end::Value-->
                                    </div>
                                    <!--end::Info-->
                                    <!--begin::Description-->
                                    <span class="fs-6 fw-semibold text-gray-400">Total Invoice(s) created</span>
                                    <!--end::Description-->
                                </div>
                                <!--end::Statistics-->
                                <!--begin::Chart-->
                                <div class="min-h-auto" style="height: 125px"></div>
                                <!--end::Chart-->
                            </div>
                            <!--end::Card body-->
                        </div>
                    </div>
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-4 mb-xl-10">
                        <div class="card overflow-hidden h-md-50 mb-5 mb-xl-10">
                            <!--begin::Card body-->
                            <div class="card-body d-flex justify-content-between flex-column px-0 pb-0">
                                <!--begin::Statistics-->
                                <div class="mb-4 px-9">
                                    <!--begin::Info-->
                                    <div class="d-flex align-items-center mb-2">
                                        <!--begin::Value-->
                                        <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2"><?= number_format($countData['transaction'], 2); ?></span>
                                        <!--end::Value-->
                                    </div>
                                    <!--end::Info-->
                                    <!--begin::Description-->
                                    <span class="fs-6 fw-semibold text-gray-400">Total Invoice Payment</span>
                                    <!--end::Description-->
                                </div>
                                <!--end::Statistics-->
                                <!--begin::Chart-->
                                <div class="min-h-auto" style="height: 125px"></div>
                                <!--end::Chart-->
                            </div>
                            <!--end::Card body-->
                        </div>
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->

                <div class="row gy-5 g-xl-10">
                    <!--begin::Col-->
                    <div class="col-xl-12 mb-5 mb-xl-10">
                        <!--begin::Row-->
                        <div class="row h-xxl-50">
                            <div class="card card-flush h-xl-100">
                                <!--begin::Card header-->
                                <div class="card-header pt-7">
                                    <!--begin::Title-->
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold text-gray-800">Shipping Orders</span>
                                    </h3>
                                    <!--end::Title-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body pt-2">
                                    <!--begin::Table-->
                                    <table class="table align-middle table-row-dashed fs-6 gy-3" id="kt_table_widget_4_table">
                                        <!--begin::Table head-->
                                        <thead>
                                            <!--begin::Table row-->
                                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="min-w-100px">Order ID</th>
                                                <th class="text-end min-w-100px">Track No.</th>
                                                <th class="text-end min-w-100px">Created</th>
                                                <th class="text-end min-w-125px">Customer</th>
                                                <th class="text-end min-w-100px">Total</th>
                                                <th class="text-end min-w-50px">Status</th>
                                                <th class="text-end"></th>
                                            </tr>
                                            <!--end::Table row-->
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody class="fw-bold text-gray-600">
                                            <?php if($transactionContent): ?>
                                                <?php foreach($transactionContent as $content): ?>
                                            <tr>
                                                <td>
                                                    <a href="../../demo1/dist/apps/ecommerce/catalog/edit-product.html" class="text-gray-800 text-hover-primary"><?= $content->invoice_no ?></a>
                                                </td>
                                                <td class="text-end"><?= $content->track_number ?></td>
                                                <td class="text-end"><?= $content->date_created ?></td>
                                                <td class="text-end">
                                                    <a href="#" class="text-gray-600 text-hover-primary"><?= $content->bill_from_name ?></a>
                                                </td>
                                                <td class="text-end">
                                                    <span class="text-gray-800 fw-bolder">N<?= $content->invoice_total; ?></span>
                                                </td>
                                                <td class="text-end">
                                                    <?php if($content->invoice_status == 'processing'): ?>
                                                        <span class="badge py-3 px-4 fs-7 badge-light-info"><?= ucfirst($content->invoice_status); ?></span>
                                                    <?php elseif($content->invoice_status == 'delivered'): ?>
                                                        <span class="badge py-3 px-4 fs-7 badge-light-success"><?= ucfirst($content->invoice_status); ?></span>
                                                    <?php else: ?>
                                                        <span class="badge py-3 px-4 fs-7 badge-light-warning"><?= ucfirst($content->invoice_status); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                        <!--end::Table body-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                                <!--end::Card body-->
                            </div>
                        </div>
                        <!--end::Row-->
                    </div>
                    <!--end::Col-->
                </div>

                <!--begin::Row-->
                <div class="row gy-5 g-xl-10">
                    <!--begin::Col-->
                    <div class="col-xl-12">
                        <!--begin::Chart widget 17-->
                        <div class="card card-flush h-xl-100">

                        </div>
                        <!--end::Chart widget 17-->
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->
</div>
<!--end:::Main-->

<?php require APPPATH.'Views/template/footer.php'; ?>