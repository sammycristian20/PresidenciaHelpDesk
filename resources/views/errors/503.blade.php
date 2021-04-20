<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Maintenance Mode</title>

  <!-- Font Awesome Icons -->
  <link href="{{assetLink('css','font-awesome')}}" rel="stylesheet" type="text/css" />
  <!-- Fonts -->
  <style type="text/css">
    body {
      background-color: #009AB9;
      color: #ffffff;
      font-family: 'Roboto Condensed', sans-serif;
      font-size: 26px;
    }
    .msg-container {
        text-align: center;
        padding: 5rem 10rem;
    }
    @media screen and (max-width: 992px) {
    .msg-container {
        padding: 3rem;
      }
    }
    @media screen and (max-width: 600px) {
    .msg-container {
        padding: 1rem;
      }
    }

  </style>
</head>

<body>
    <div>
        <span class="pull-right"><i class="fa fa-spinner fa-pulse" aria-hidden="true"></i></span>
        <div class="msg-container">
            <p><i class="fa fa-wrench fa-2x" aria-hidden="true"></i><i class="fa fa-gear" aria-hidden="true"></i></p>
            <p>{!! Lang::get('lang.please_wait_for_few_minutes') !!}</p>
            <p>{!! Lang::get('lang.system_under_maintenance_mode') !!}</p>
        </div>
    </div>
</body>

</html>
<script>
    setTimeout(() => location.reload(), 20000);
</script>