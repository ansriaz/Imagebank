@extends('layouts.app')

@section('title')
  Image Bank
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">

                <div class="panel-body">
                    <p class="body text-justify"><strong>ImageBank</strong> is an image database created for the researchers. Researchers can contribute with their own images dataset/database to made it publicaly available for other researchers and can download already available ones. With the help of researchers and already availables image databases like Yahoo Flickr and many others, a great novel database is introduced not only to the computer vision research community but also to multimedia. To maximize the benefit and reduce the pain of creating same dataset for the research community and utilize its potential, these datasets has to be made accessible. By tools allowing to search for target class labels within the dataset and mechanism to browse images of the dataset.</p>
                    <p class="body text-justify">Currently we are in early stage and have some specific datasets. With the contribution of research community all around the world, we hope <strong>ImageBank</strong> will become a useful resource for researchers, students and all of you who need it. </p>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Datasets</div>
                    <div class="panel-body">
                        <div>
                            <label id="total_datasets">Total Datasets:</label>
                             {{-- {{ $datasets['total'] }} --}}
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
                            <label id="total_classes">Image Classes:</label>
                             {{-- {{ $classes['total'] }} --}}
                        </div>
                        <div class="chartsDiv">
                            <canvas id="classesChart" width="200px" height="200px"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Images</div>
                    <div class="panel-body">
                        <div>
                            <label id="total_images">Total Images:</label>
                             {{-- {{ $images['total'] }} --}}
                        </div>
                        <div class="chartsDiv">
                            <canvas id="imagesChart" width="200px" height="200px"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('footer')
    <script src="/assets/js/charts/Chart.bundle.min.js"></script>
    <script>
        // Chart.defaults.global.maintainAspectRatio = true;
        // Chart.defaults.global.responsive = false;
        function setData(datasets, classes, images){
            console.log('datasets: ' + JSON.stringify(datasets) + ' classes: ' + JSON.stringify(classes) + ' images: ' + JSON.stringify(images) )
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
                            data: [datasets['user'], datasets['system'] ],
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
                            data: [images['user'], images['system']],
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
                            data: [classes['user'], classes['system']],
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
        }
    </script>
    <script>
        $.ajax({
            type: "get",
            url: '/getReport',
            data: "",
            timeout: 20000,
            // dataType : 'json',
            success: function(response) {
                document.getElementById('total_datasets').innerHTML = 'Total Datasets: ' + response.datasets.total;
                document.getElementById('total_images').innerHTML = 'Total Images: ' + response.images.total;
                document.getElementById('total_classes').innerHTML = 'Image Classes: ' + response.classes.total;
                // $(".total_datasets").html(response.total_datasets);
                // $(".total_images").html(response.total_images);
                // $(".total_classes").html(response.total_classes);
                setData(response.datasets, response.classes, response.images)
            },
            error: function(xhr, textStatus, errorThrown){
                alert(errorThrown);
            }
        })
    </script>
@endsection
