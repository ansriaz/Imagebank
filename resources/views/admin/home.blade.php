@extends('admin.app')

@section('title')
  Home
@endsection

@section('head')

@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="text-center">
            <ul class="list-inline">
                <li><a href="{{ url('/admin/home') }}">Admin Home</a></li> ||
                <li><a href="{{ url('/admin/newimages') }}">New Images</a></li> ||
                <li><a href="{{ url('/admin/geteditimages') }}">Edit Images</a></li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Datasets</div>
                    <div class="panel-body">
                        <div>
                            <label id="total_datasets">Total Datasets: {{ $datasets['total'] }}</label>
                        </div>
                        <div class="chartsDiv">
                            <canvas id="datasetChart" width="200px" height="200px"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Classes</div>
                    <div class="panel-body">
                        <div>
                            <label id="total_classes">Image Classes: {{ $classes['total'] }}</label>
                        </div>
                        <div class="chartsDiv">
                            <canvas id="classesChart" width="200px" height="200px"></canvas>
                            <div id="graphic-legend"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Images</div>
                    <div class="panel-body">
                        <div>
                            <label id="total_images">Total Images: {{ $images['total'] }}</label>
                        </div>
                        <div class="chartsDiv">
                            <canvas id="imagesChart" width="200px" height="200px"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            {{-- <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Datasets</div>
                    <div class="panel-body">
                        <div>
                            <label id="total_datasets">Total Datasets: </label>
                        </div>
                        <div>
                            <canvas id="myChart1" width="200px" height="200px"></canvas>
                        </div>
                    </div>
                </div>
            </div> --}}

            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Images</div>
                    <div class="panel-body">
                        <div>
                            <label class="col-md-3" id="reportClasses">Total Classes: </label>
                            <label class="col-md-3" id="reportImages">Total Images: </label>
                        </div>
                        <div class="chartsDiv">
                            <canvas id="resultChart" width="auto" height="200px"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Classes</div>
                    <div class="panel-body">
                        <div>
                            <label id="total_classes">Image Classes: </label>
                        </div>
                        <div class="chartsDiv">
                            <canvas id="myChart3" width="200px" height="200px"></canvas>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>
@endsection

@section('footer')

    <script src="/assets/js/charts/Chart.bundle.min.js"></script>
    <script>
        $(document).ready(
            function(){
                Chart.defaults.global.maintainAspectRatio = true;
                Chart.defaults.global.responsive = false;
                var ctxDataset = document.getElementById("datasetChart");
                var datasetChart = new Chart(ctxDataset, {
                    type: 'pie',
                    data: {
                        labels: [
                            "User",
                            "System"
                        ],
                        datasets: [
                            {
                                data: [{{ $datasets['user'] }}, {{ $datasets['system'] }}],
                                backgroundColor: [
                                    "#36A2EB",
                                    "#FFCE56"
                                ],
                                hoverBackgroundColor: [
                                    "#36A2EB",
                                    "#FFCE56"
                                ]
                            }]
                    },
                    animation:{
                        animateScale:true
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                    },
                });
                var ctxImages = document.getElementById("imagesChart");
                var imagesChart = new Chart(ctxImages, {
                    type: 'pie',
                    data: {
                        labels: [
                            "User",
                            "System"
                        ],
                        datasets: [
                            {
                                data: [{{ $images['user'] }}, {{ $images['system'] }}],
                                backgroundColor: [
                                    "#36A2EB",
                                    "#FFCE56"
                                ],
                                hoverBackgroundColor: [
                                    "#36A2EB",
                                    "#FFCE56"
                                ]
                            }]
                    },
                    animation:{
                        animateScale:true
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                    },
                });
                var ctxClasses = document.getElementById("classesChart");
                var classesChart = new Chart(ctxClasses, {
                    type: 'pie',
                    data: {
                        labels: [
                            "User",
                            "System"
                        ],
                        datasets: [
                            {
                                data: [{{ $classes['user'] }}, {{ $classes['system'] }}],
                                backgroundColor: [
                                    "#36A2EB",
                                    "#FFCE56"
                                ],
                                hoverBackgroundColor: [
                                    "#36A2EB",
                                    "#FFCE56"
                                ]
                            }]
                    },
                    animation:{
                        animateScale:true
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                    }
                });
                // document.getElementById('graphic-legend').innerHTML = classesChart.generateLegend();

                var canvas = document.getElementById('classesChart');
                // var legendHolder = document.getElementById('graphic-legend');

                canvas.onclick = function(evt){
                        console.log(JSON.stringify(evt));
                        var activePoints = classesChart.getElementsAtEvent(evt);
                        // alert(JSON.stringify(activePoints.length));
                        if(activePoints.length > 0)
                        {
                          //get the internal index of slice in pie chart
                          var clickedElementindex = activePoints[0]["_index"];

                          //get specific label by index
                          var label = classesChart.data.labels[clickedElementindex];
                          console.log(label);

                          //get value by index
                          var value = classesChart.data.datasets[0].data[clickedElementindex];
                          console.log(value);

                          populateData(label);
                        }
                    };

                // var data = {
                //     labels: [
                //         "Red",
                //         "Blue",
                //         "Yellow"
                //     ],
                //     datasets: [
                //         {
                //             label: '# of Votes',
                //             data: [300, 50, 100],
                //             backgroundColor: [
                //                 "#FF6384",
                //                 "#36A2EB",
                //                 "#FFCE56"
                //             ],
                //             hoverBackgroundColor: [
                //                 "#FF6384",
                //                 "#36A2EB",
                //                 "#FFCE56"
                //             ]
                //         }]
                // };
                // var resultChart = document.getElementById("resultChart");
                // var resChart = new Chart(resultChart, {
                //     type: 'bar',
                //     data: data,
                //     animation:{
                //         animateScale:true
                //     },
                //     options: {
                //         scales: {
                //             yAxes: [{
                //                 ticks: {
                //                     beginAtZero:true
                //                 }
                //             }]
                //         }
                //     },
                //     options: {
                //         responsive: true,
                //         maintainAspectRatio: false,
                //     }
                // });

                function populateData(sectionTitle){
                    $.ajax({
                        type: "get",
                        url: '/admin/getClassReport',
                        data: {section:sectionTitle},
                        timeout: 5000,
                        // dataType : 'json',
                        success: function(response) {
                            // response = JSON.parse(response);
                            console.log(response);
                            // console.log(response.length);
                            var titles = [], datas = [];
                            var total_images = 0;
                            for (var i = 0; i < response.length; i++) {
                                // console.log(response[i]);
                                titles.push(response[i].title);
                                datas.push(response[i].images);
                                total_images += response[i].images;
                            };
                            var data = {
                                labels: titles,
                                datasets: [
                                    {
                                        label: '# Images',
                                        data: datas,
                                    }]
                            };
                            // console.log(titles);
                            // console.log(datas);
                            // console.log(data);
                            document.getElementById('reportClasses').innerHTML = 'Total Classes: ' + response.length;
                            document.getElementById('reportImages').innerHTML = 'Total Images: ' + total_images;

                            $('#resultChart').replaceWith('<canvas id="resultChart" width="auto" height="200px"></canvas>');
                            var resultChart = document.getElementById("resultChart");
                            var resChart = new Chart(resultChart, {
                                type: 'bar',
                                data: data,
                                animation:{
                                    animateScale:true
                                },
                                options: {
                                    scales: {
                                        yAxes: [{
                                            ticks: {
                                                beginAtZero:true
                                            }
                                        }]
                                    }
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                }
                            });
                        },
                        error: function(xhr, textStatus, errorThrown){
                            alert(errorThrown);
                        }
                    });
                }
            }
        );

    </script>
@endsection
