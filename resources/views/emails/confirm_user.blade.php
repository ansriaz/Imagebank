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
    Dear Administrator,
</div>
<br/>
<div>
    A new account has been registered on your website. Details are below:<br /> <br />
    Name: {{ $name }} <br />
    Organization: {{ $organization }} <br /><br />
    Please confirm this account if it is not fake and allow him to use  your website by clicking the button down or copy this link and paste it in your browser.
</div>

<br/>
<div class="text-center">
    <form id="download" role="form" target="_blank" method="GET" action="{{ url('/confirm_user/'.$user_id) }}">
        <button type="submit" id='confirmUser' class="btn btn-primary btn-lg">Confirm User</button>
    </form>
</div>
<br/>
<br/>
<a href="{{ URL::to('/confirm_user/'.$user_id) }}" class="text-sm">{{ URL::to('/confirm_user/'.$user_id) }}</a>

<br/>
<br/>
<div>Best Regards</div>
<br/>
<div>ImageBank</div>

</body>
</html>