@extends('layouts.app')

@section('title')
  About ImageBank
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">About ImageBank</div>

                <div class="panel-body">
                    In recent years, the biggest need for academia and research community in different fields, such as speech recognition and computer vision, is to have the access to a variety of big data. In multimedia analysis, the need is even greater in the face of recent advances particularly in deep learning. Making and finding datasets for Multimedia Analysis and Signal Processing is really a tedious job. Moreover, User log data and feedback data are in great need as well. To this aim, in the work we propose a collaborative dataset for algorithm benchmarking in multimedia analysis and signal processing. Such a system is intended to become a powerful support tool for the research community in multimedia analysis by providing a common benchmark for training, testing, validation and comparison of existing and novel algorithms. A detailed description of the step-by-step procedure followed through the design process is provided. Moreover, a Matlab Interface has been integrated to facilitate the researchers in comparisons of their performance with state-of-the-art. 
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="text-center">
           {{--  <a href="http://www.unitn.it/en"><img src="{{URL::asset('/assets/images/unitn.jpg')}}" alt="University Of Trento" height="100px" width="320px"></a>
            <a href="http://mmlab.science.unitn.it/"><img src="{{URL::asset('/assets/images/mmlab.png')}}" alt="Multimedia Signal Processing and Understanding Lab" height="88px" width="120px"></a>
            </div> --}}
        </div>
    </div>
</div>
@endsection
