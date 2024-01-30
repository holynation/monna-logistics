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
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Dashboard</h1>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="<?=base_url('admin/dashboard');?>" class="text-muted text-hover-primary">Home</a>
                        </li>
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
                <!--begin::Row-->
                <div class="row gy-5 g-xl-10 mb-4 mb-xl-10">
                    <!--begin::Col-->
                    <div class="col-4 ">
                        <div class="card overflow-hidden h-auto mb-xl-10">
                            <!--begin::Card body-->
                            <div class="card-body d-flex justify-content-between flex-column px-0 pb-0">
                                <!--begin::Statistics-->
                                <div class="mb-4 px-9">
                                    <!--begin::Info-->
                                    <div class="d-flex align-items-center mb-2">
                                        <!--begin::Value-->
                                        <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2"><?=$countData['customer'];?></span>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Info-->
                                    <!--begin::Description-->
                                    <span class="fs-6 fw-semibold text-gray-400">Total Customers</span>
                                    <!--end::Description-->
                                </div>
                                <!--end::Statistics-->
                            </div>
                            <!--end::Card body-->
                        </div>
                    </div>
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-4 ">
                        <div class="card overflow-hidden h-auto mb-xl-10">
                            <!--begin::Card body-->
                            <div class="card-body d-flex justify-content-between flex-column px-0 pb-0">
                                <!--begin::Statistics-->
                                <div class="mb-4 px-9">
                                    <!--begin::Info-->
                                    <div class="d-flex align-items-center mb-2">
                                        <!--begin::Value-->
                                        <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2"><?=$countData['invoice'];?></span>
                                        <!--end::Value-->
                                    </div>
                                    <!--end::Info-->
                                    <!--begin::Description-->
                                    <span class="fs-6 fw-semibold text-gray-400">Total Invoice(s) created</span>
                                    <!--end::Description-->
                                </div>
                                <!--end::Statistics-->
                            </div>
                            <!--end::Card body-->
                        </div>
                    </div>
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-4 ">
                        <div class="card overflow-hidden h-auto mb-xl-10">
                            <!--begin::Card body-->
                            <div class="card-body d-flex justify-content-between flex-column px-0 pb-0">
                                <!--begin::Statistics-->
                                <div class="mb-4 px-9">
                                    <!--begin::Info-->
                                    <div class="d-flex align-items-center mb-2">
                                        <!--begin::Value-->
                                        <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2"><?=number_format($countData['transaction'], 2);?></span>
                                        <!--end::Value-->
                                    </div>
                                    <!--end::Info-->
                                    <!--begin::Description-->
                                    <span class="fs-6 fw-semibold text-gray-400">Total Invoice Payment</span>
                                    <!--end::Description-->
                                </div>
                                <!--end::Statistics-->
                            </div>
                            <!--end::Card body-->
                        </div>
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->

                <div class="row gy-5 g-xl-10 mb-4 mb-xl-10">
                    <div class="col-xl-12 mb-xl-10">
                        <div class="card card-flush h-lg-100 mb-lg-10">
                            <div class="card-header pt-5">
                                <div class="card-title d-flex flex-column">
                                    <span class="text-gray-400 pt-1 fw-semibold fs-6">Monthly Rate(s) Amount</span>
                                </div>
                            </div>
                            <div class="card-body d-flex align-items-end pt-0">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="d-flex mx-auto">
                                        <div id="kt_card_dashboard_12_chart" style="height: 400px;width: 700px"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--begin::Row-->
                <div class="row gy-5 g-xl-10">
                    <div class="col-xl-6 mb-xl-10">
                        <div class="card card-flush h-lg-100 mb-lg-10">
                            <div class="card-header pt-5">
                                <div class="card-title d-flex flex-column">
                                    <span class="text-gray-400 pt-1 fw-semibold fs-6">Monthly Rate(s) Count</span>
                                </div>
                            </div>
                            <div class="card-body d-flex align-items-end pt-0">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="d-flex mx-auto">
                                        <div id="kt_card_dashboard_10_chart" style="height: 300px;width: 500px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 mb-xl-10">
                        <div class="card card-flush h-lg-100 mb-lg-10">
                            <div class="card-header pt-5">
                                <div class="card-title d-flex flex-column">
                                    <span class="text-gray-400 pt-1 fw-semibold fs-6">Monthly Rate(s) Amount</span>
                                </div>
                            </div>
                            <div class="card-body d-flex align-items-end pt-0">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="d-flex me-10 me-xxl-10">
                                        <div id="kt_card_dashboard_11_chart" style="height: 300px; width: 500px"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->

                <div class="row gy-2 g-xl-10">
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
                                    <table class="table align-middle table-row-dashed fs-6 gy-3">
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
                                            <?php if ($transactionContent): ?>
                                                <?php foreach ($transactionContent as $content): ?>
                                            <tr>
                                                <td>
                                                    <a href="<?=base_url('vc/admin/invoice_action');?>" class="text-gray-800 text-hover-primary"><?=$content->invoice_no?></a>
                                                </td>
                                                <td class="text-end"><?=$content->track_number?></td>
                                                <td class="text-end"><?=$content->date_created?></td>
                                                <td class="text-end">
                                                    <a href="#" class="text-gray-600 text-hover-primary"><?=$content->bill_from_name?></a>
                                                </td>
                                                <td class="text-end">
                                                    <span class="text-gray-800 fw-bolder">N<?=$content->invoice_total;?></span>
                                                </td>
                                                <td class="text-end">

                                                    <?php if ($content->invoice_status == \App\Enums\InvoiceStatusEnum::INTRANSIT->value): ?>
                                                        <div class="badge py-3 px-4 fs-7 badge-light-primary"><?=ucfirst($content->invoice_status);?></div>
                                                    <?php elseif ($content->invoice_status == \App\Enums\InvoiceStatusEnum::PROCESSING->value): ?>
                                                        <div class="badge py-3 px-4 fs-7 badge-light-info"><?=ucfirst($content->invoice_status);?></div>
                                                    <?php elseif ($content->invoice_status == \App\Enums\InvoiceStatusEnum::COMPLETED->value): ?>
                                                        <div class="badge py-3 px-4 fs-7 badge-light-success"><?=ucfirst($content->invoice_status);?></div>
                                                    <?php elseif ($content->invoice_status == \App\Enums\InvoiceStatusEnum::CANCELLED->value): ?>
                                                        <div class="badge py-3 px-4 fs-7 badge-light-danger"><?=ucfirst($content->invoice_status);?></div>
                                                    <?php else: ?>
                                                        <div class="badge py-3 px-4 fs-7 badge-light-warning"><?=ucfirst($content->invoice_status);?></div>
                                                    <?php endif;?>
                                                </td>
                                            </tr>
                                            <?php endforeach;?>
                                            <?php endif;?>
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
    var element = document.getElementById('kt_card_dashboard_10_chart');
    var element2 = document.getElementById('kt_card_dashboard_11_chart');
    var element3 = document.getElementById('kt_card_dashboard_12_chart');

    var countData = JSON.parse('<?=$invoiceCountData?>');
    var invoiceData = JSON.parse('<?=$invoiceAmountData?>');
    var invoiceData2 = JSON.parse('<?=$invoiceAmountData2?>');

    const countLabels = countData?.map((item) => item.label);
    const countValue = countData?.map((item) => item.total);

    const amountLabels = invoiceData?.map((item) => item.label);
    const amountValue = invoiceData?.map((item) => item.total/100);

    const amountLabels2 = invoiceData2?.map((item) => item.label);
    const amountValue2 = invoiceData2?.map((item) => item.total);

    var options = {
        chart: {
            width: 380,
            type: 'pie'
        },
        plotOptions: {
          pie: {
            donut: {
              labels: {
                show: true,
              },
            }
          }
        },
        legend: {
            position: 'bottom'
        },
        colors: ['#164B60', '#1B6B93', '#4FC0D0', '#A2FF86', '#072541'],
        series: countValue,
        labels: countLabels,
    }

    var options2 = {
        series: amountValue,
        chart: {
            type: 'donut',
            width: 380,
        },
        legend: {
            position: 'bottom'
        },
        colors: ['#5F939A', '#D8AC9C', '#D8AC9C', '#EAC8AF', '#1B2021'],
        labels: amountLabels,
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 200
            },
            legend: {
              position: 'bottom'
            }
          }
        }]
    }

    var options3 = {
        series: [{
            data: amountValue2
        }],
        chart: {
          type: 'bar',
          height: 350,
          toolbar: {
                show: false
            }
        },
        plotOptions: {
          bar: {
            borderRadius: 4,
            horizontal: false,
            columnWidth: ['30%'],
          }
        },
        dataLabels: {
          enabled: false
        },
        xaxis: {
          categories: amountLabels2,
        },
        colors: ['#0B60B0'],
    }

    var chart = new ApexCharts(element, options);
    chart.render();

    var chart2 = new ApexCharts(element2, options2);
    chart2.render();

    var chart3 = new ApexCharts(element3, options3);
    chart3.render();
</script>