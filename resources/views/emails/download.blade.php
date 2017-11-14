<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script><script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>
<h2>Image Bank</h2>

<div>
    Dear Mr./Ms. {{ $name }} 
</div>
<br/>
<div>
    Your archive is ready. Please click on the button below or copy this link and paste it in your browser to download your archive.
</div>

<br/>
<div class="text-center">
    <form id="download" role="form" target="_blank" method="GET" action="{{ url('/downloadArchive/'.$download) }}">
        <button type="submit" id='downloadBtn' class="btn btn-primary btn-lg"><i class="fa fa-btn fa-download"></i>Download Archive</button>
    </form>
</div>
<br/>
<!-- <a href="{{ url('/downloadArchive/'.$download) }}">Download Archive</a> -->
<!-- <input type="button" onclick="window.open('http://www.example.com','_blank','resizable=yes')" /> -->
<br/>
<label>{{ URL::to('/downloadArchive/'.$download) }}</label>

<br/>
<br/>
<div>Best Regards</div>

</body>
</html>