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
    Your account has been confirmed. Now you can explore and use this website. <br/>
    Thank you for your interest.
</div>

<br/>
<div class="text-center">
    <a type="button" class="btn btn-primary btn-lg" href="{{URL::to('/home')}}">Go To ImageBank</a>
</div>
<br/>
<br/>
<div>Best Regards</div>
<br/>
<div>ImageBank</div>

</body>
</html>