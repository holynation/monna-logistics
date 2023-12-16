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
                            <h4 class="nk-block-title page-title">Statistics Page</h4>
                        </div><!-- .nk-block-head-content -->
                    </div><!-- .nk-block-between -->
                </div><!-- .nk-block-head -->
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
                                <label class="form-label mt-3"></label>
                                <div class="form-control-wrap">
                                    <button type='submit' class="btn btn-primary save">Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="nk-block">
                    <div class="row g-gs">
                        <div class="col-xl-12">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-2">
                                        <div class="card-title">
                                            <h6 class="title">Cashback Overview</h6>
                                        </div>
                                    </div>

                                    <div class="align-end gy-3 gx-5 flex-wrap flex-md-nowrap flex-xl-wrap">
                                        <!-- this is the contribution chart -->
                                        <div class="nk-sales-ck sales-revenue">
                                            <canvas class="sales-bar-chart" id="daysRevenue"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- .col -->

                        <div class="col-xl-12">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-2">
                                        <div class="card-title">
                                            <h6 class="title">Client Fund Overview</h6>
                                        </div>
                                    </div>

                                    <div class="align-end gy-3 gx-5 flex-wrap flex-md-nowrap flex-xl-wrap">
                                        <!-- this is the contribution chart -->
                                        <div class="nk-sales-ck sales-revenue">
                                            <canvas class="sales-bar-chart" id="mnthRevenue"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- .col -->

                         <!-- withdrawal -->
                        <div class="col-xl-12">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="card-title-group align-start gx-3 mb-3">
                                        <div class="card-title">
                                            <h6 class="title">Total Withdrawal Overview</h6>
                                        </div>
                                    </div>
                                    <!-- this is for the withdrawal -->
                                    <div class="nk-sales-ck large pt-4">
                                        <canvas class="sales-overview-chart" id="salesOverview"></canvas>
                                    </div>
                                </div>
                            </div><!-- .card -->
                        </div><!-- .col -->
                    </div><!-- .row -->
                </div><!-- .nk-block -->
            </div>
        </div>
    </div>
</div>
<!-- content @e -->


<?php
$cashbackDistrix[0] = json_encode(@$cashbackDistrix[0]); 
$cashbackDistrix[1] = json_encode(@$cashbackDistrix[1]);

$fundDistrix[0] = json_encode(@$fundDistrix[0]); 
$fundDistrix[1] = json_encode(@$fundDistrix[1]);

// withdrawal
$withdrawalDistrix[0] = json_encode(@$withdrawalDistrix[0]); 
$withdrawalDistrix[1] = json_encode(@$withdrawalDistrix[1]);

?>

<!-- JavaScript -->
<?php include_once ROOTPATH.'template/footer.php'; ?>

<script type="text/javascript">
    "use strict";
!function (NioApp, $) {
    let data2 = JSON.parse('<?php echo $cashbackDistrix[0]; ?>'); // cashback days
    let data3 = JSON.parse('<?php echo $cashbackDistrix[1]; ?>');

    let data4 = JSON.parse('<?php echo $fundDistrix[0]; ?>'); // fund mnth
    let data5 = JSON.parse('<?php echo $fundDistrix[1]; ?>');

    let data6 = JSON.parse('<?php echo $withdrawalDistrix[0]; ?>'); // withdrawal mnth
    let data7 = JSON.parse('<?php echo $withdrawalDistrix[1]; ?>');

    var daysRevenue = {
        labels: data2,
        dataUnit: 'STAKE',
        stacked: true,
        datasets: [{
          label: "Cashback",
          color: [NioApp.hexRGB("#5CE0AA", .4), NioApp.hexRGB("#5CE0AA", .4), NioApp.hexRGB("#5CE0AA", .4), NioApp.hexRGB("#5CE0AA", .4), NioApp.hexRGB("#5CE0AA", .4), "#5CE0AA"],
          data: data3
        }]
    };

    var mnthRevenue = {
        labels: data4,
        dataUnit: 'NGN',
        stacked: true,
        datasets: [{
          label: "Fund Overview",
          color: [NioApp.hexRGB("#8cefd4", .4), NioApp.hexRGB("#8cefd4", .4), NioApp.hexRGB("#8cefd4", .4), NioApp.hexRGB("#8cefd4", .4), NioApp.hexRGB("#8cefd4", .4), NioApp.hexRGB("#8cefd4", .4), NioApp.hexRGB("#8cefd4", .4), NioApp.hexRGB("#8cefd4", .4), NioApp.hexRGB("#8cefd4", .4), NioApp.hexRGB("#8cefd4", .4), NioApp.hexRGB("#8cefd4", .4), "#8cefd4"],
          data: data5
        }]
    };

    function salesBarChart(selector, set_data) {
        var $selector = selector ? $(selector) : $('.sales-bar-chart');
        $selector.each(function () {
          var $self = $(this),
              _self_id = $self.attr('id'),
              _get_data = typeof set_data === 'undefined' ? eval(_self_id) : set_data,
              _d_legend = typeof _get_data.legend === 'undefined' ? false : _get_data.legend;

          var selectCanvas = document.getElementById(_self_id).getContext("2d");
          var chart_data = [];

          for (var i = 0; i < _get_data.datasets.length; i++) {
            chart_data.push({
              label: _get_data.datasets[i].label,
              data: _get_data.datasets[i].data,
              // Styles
              backgroundColor: _get_data.datasets[i].color,
              borderWidth: 2,
              borderColor: 'transparent',
              hoverBorderColor: 'transparent',
              borderSkipped: 'bottom',
              barPercentage: .7,
              categoryPercentage: .7
            });
          }

          var chart = new Chart(selectCanvas, {
            type: 'bar',
            data: {
              labels: _get_data.labels,
              datasets: chart_data
            },
            options: {
              legend: {
                display: _get_data.legend ? _get_data.legend : false,
                rtl: NioApp.State.isRTL,
                labels: {
                  boxWidth: 30,
                  padding: 20,
                  fontColor: '#6783b8'
                }
              },
              maintainAspectRatio: false,
              tooltips: {
                enabled: true,
                rtl: NioApp.State.isRTL,
                callbacks: {
                  title: function title(tooltipItem, data) {
                    return false;
                  },
                  label: function label(tooltipItem, data) {
                    return data['labels'][tooltipItem['index']] + ' - ' + data.datasets[tooltipItem.datasetIndex]['data'][tooltipItem['index']] + ' ' + _get_data.dataUnit;
                  }
                },
                backgroundColor: '#1c2b46',
                titleFontSize: 13,
                titleFontColor: '#fff',
                titleMarginBottom: 4,
                bodyFontColor: '#fff',
                bodyFontSize: 12,
                bodySpacing: 10,
                yPadding: 12,
                xPadding: 12,
                footerMarginTop: 0,
                displayColors: false
              },
              scales: {
                yAxes: [{
                  display: false,
                  stacked: _get_data.stacked ? _get_data.stacked : false,
                  ticks: {
                    beginAtZero: true
                  }
                }],
                xAxes: [{
                  display: false,
                  stacked: _get_data.stacked ? _get_data.stacked : false,
                  ticks: {
                    reverse: NioApp.State.isRTL
                  }
                }]
              }
            }
          });
        });
    } // init chart

    NioApp.coms.docReady.push(function () {
        salesBarChart();
    }); // end

    // this is for withdraw chart
    var salesOverview = {
        labels: data6,
        dataUnit: 'NGN',
        lineTension: 0.4,
        datasets: [{
          label: "withdrawal Overview",
          color: "#42f4aa",
          background: NioApp.hexRGB('#8cefd4', .35),
          data: data7
        }]
    };

    function lineSalesOverview(selector, set_data) {
        var $selector = selector ? $(selector) : $('.sales-overview-chart');
        $selector.each(function () {
          var $self = $(this),
              _self_id = $self.attr('id'),
              _get_data = typeof set_data === 'undefined' ? eval(_self_id) : set_data;

          var selectCanvas = document.getElementById(_self_id).getContext("2d");
          var chart_data = [];

          for (var i = 0; i < _get_data.datasets.length; i++) {
            chart_data.push({
              label: _get_data.datasets[i].label,
              tension: _get_data.lineTension,
              backgroundColor: _get_data.datasets[i].background,
              borderWidth: 4,
              borderColor: _get_data.datasets[i].color,
              pointBorderColor: "transparent",
              pointBackgroundColor: "transparent",
              pointHoverBackgroundColor: "#fff",
              pointHoverBorderColor: _get_data.datasets[i].color,
              pointBorderWidth: 4,
              pointHoverRadius: 6,
              pointHoverBorderWidth: 4,
              pointRadius: 6,
              pointHitRadius: 6,
              data: _get_data.datasets[i].data
            });
          }

          var chart = new Chart(selectCanvas, {
            type: 'line',
            data: {
              labels: _get_data.labels,
              datasets: chart_data
            },
            options: {
              legend: {
                display: _get_data.legend ? _get_data.legend : false,
                rtl: NioApp.State.isRTL,
                labels: {
                  boxWidth: 30,
                  padding: 20,
                  fontColor: '#6783b8'
                }
              },
              maintainAspectRatio: false,
              tooltips: {
                enabled: true,
                rtl: NioApp.State.isRTL,
                callbacks: {
                  title: function title(tooltipItem, data) {
                    return data['labels'][tooltipItem[0]['index']];
                  },
                  label: function label(tooltipItem, data) {
                    return data.datasets[tooltipItem.datasetIndex]['data'][tooltipItem['index']] + ' ' + _get_data.dataUnit;
                  }
                },
                backgroundColor: '#1c2b46',
                titleFontSize: 13,
                titleFontColor: '#fff',
                titleMarginBottom: 4,
                bodyFontColor: '#fff',
                bodyFontSize: 12,
                bodySpacing: 10,
                yPadding: 12,
                xPadding: 12,
                footerMarginTop: 0,
                displayColors: false
              },
              scales: {
                yAxes: [{
                  display: true,
                  stacked: _get_data.stacked ? _get_data.stacked : false,
                  position: NioApp.State.isRTL ? "right" : "left",
                  ticks: {
                    beginAtZero: true,
                    fontSize: 11,
                    fontColor: '#9eaecf',
                    padding: 10,
                    callback: function callback(value, index, values) {
                      return '$ ' + value;
                    },
                    min: 100,
                    stepSize: 3000
                  },
                  gridLines: {
                    color: NioApp.hexRGB("#526484", .2),
                    tickMarkLength: 0,
                    zeroLineColor: NioApp.hexRGB("#526484", .2)
                  }
                }],
                xAxes: [{
                  display: true,
                  stacked: _get_data.stacked ? _get_data.stacked : false,
                  ticks: {
                    fontSize: 9,
                    fontColor: '#9eaecf',
                    source: 'auto',
                    padding: 10,
                    reverse: NioApp.State.isRTL
                  },
                  gridLines: {
                    color: "transparent",
                    tickMarkLength: 0,
                    zeroLineColor: 'transparent'
                  }
                }]
              }
            }
          });
        });
    } // init chart

    NioApp.coms.docReady.push(function () {
        lineSalesOverview();
    }); // end
}(NioApp, jQuery);
</script>