<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script><script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>
<div>
    Dear Mr./Ms. {{ $name }} 
</div>
<br/>
<div>
    Your matlab script run successfully. Please check the file attached with the email. Login to website by clicking the link below and explore more.
    <br/>
    Thank you for your interest.
</div>

<br/>
<div class="text-center">
    <form id="download" role="form" target="_blank" method="GET" action="{{ url('/home') }}">
        <button type="submit" id='confirmUser' class="btn btn-default btn-lg">Go To Image Bank</button>
    </form>
</div>
<br/>
<br/>
<a href="{{ URL::to('/home') }}" class="text-sm">{{ URL::to('/home') }}</a>


<br/>
<br/>
<div>
    <b>Note: </b>
    <br>
    Output file may contains the errors in case of errors in your script file.
</div>

<br/>
<br/>
<div>Best Regards</div>
<br/>
<div>ImageBank</div>

</body>
</html>