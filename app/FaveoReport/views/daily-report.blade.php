<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8"> <!-- utf-8 works for most cases -->
    <meta name="x-apple-disable-message-reformatting">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
    <title>Daily Report</title> <!-- The title tag shows in email notifications, like Android 4.4. -->


    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,400i,700,700i" rel="stylesheet">

  <style type="text/css">
    body {
      margin: 0;
      padding: 0;
      min-width: 100% !important;
    }

    img {
      height: auto;
    }

    .content {
      max-width: 600px;
    }

    .header {
      padding: 40px 30px 0 30px;
    }

    .innerpadding {
      padding: 30px 30px 30px 30px;
    }

    .borderbottom {
      border-bottom: 1px solid #f2eeed;
    }

    .subhead {
      font-size: 15px;
      font-family: sans-serif;
      letter-spacing: 3px;
    }

    .h1,
    .h3,
    .bodycopy {
      color: #153643;
      font-family: sans-serif;
    }

    .h1 {
      font-size: 28px;
      line-height: 38px;
      font-weight: bold;
    }

    .h3 {
      padding: 0 0 15px 0;
      line-height: 28px;
      font-weight: bold;
    }

    .bodycopy {
      font-size: 16px;
      line-height: 22px;
    }

    .button {
      text-align: center;
      font-size: 18px;
      font-family: sans-serif;
      font-weight: bold;
      padding: 0 30px 0 30px;
    }

    .button a {
      color: #ffffff;
      text-decoration: none;
    }

    .footer {
      padding: 20px 30px 15px 30px;
    }

    .footercopy {
      font-family: sans-serif;
      font-size: 14px;
      color: #ffffff;
    }

    .footercopy a {
      color: #ffffff;
      text-decoration: underline;
    }
  </style>

</head>

<body bgcolor="#f6f8f1">
  <table width="100%" bgcolor="#f6f8f1" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td>
        <table class="content" bgcolor="#ffffff" align="center" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td bgcolor="#f0e68c" class="header">
              <table width="85" align="left" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td height="85" style="padding: 0 20px 20px 0;">
                    <img class="fix" src="{!!$companyLogo!!}" width="85"
                      height="85" border="0" alt="{!!$companyName!!}" />
                  </td>
                </tr>
              </table>
              <table class="col425" align="left" border="0" cellpadding="0" cellspacing="0"
                style="width: 100%; max-width: 425px;">
                <tr>
                  <td height="85">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td class="subhead" style="padding: 0 0 0 3px;">
                          {{$companyName}}
                        </td>
                      </tr>
                      <tr>
                        <td class="h1" style="padding: 5px 0 0 0;">
                          Daily Report
                        </td>
                      </tr>
                      <tr>
                        <td style="text-align:right;font-family:sans-serif;font-size:12px;">{{$date}}
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td class="innerpadding borderbottom">
              <table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" align="center"
                width="100%" style="max-width:680px">
                @for ($i = 0; $i < count($reports); $i++)
                <tbody>
                  <tr>
                    <td>
                      <h3
                        style="font-weight:lighter">
                        <span>{{$reports[$i]->title}}</span>
                        @if (!is_null($reports[$i]->total))
                          <span>({!! $reports[$i]->total !!})</span>
                        @endif
                      </h3>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0"
                        align="center" width="100%" style="max-width:680px">
                        <tbody>
                          @foreach ($reports[$i]->data as $report_data)
                          <tr>
                            <td colspan="2"
                              style="padding:16px;text-align:left;font-family:sans-serif;font-size:15px;line-height:24px;color:#333333;border-top:1px solid #e6eaef">
                              <table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0"
                                align="center" style="margin-right:24px;width:100%;">
                                <tbody>
                                  <tr>
                                    <td colspan="2">
                                      @if (!is_null($report_data->picture))
                                        <div><img style="border-radius: 50%;" src="{!! $report_data->picture !!}" alt="Agent" width="64" height="64"/></div>
                                      @endif
                                      @if (!is_null($report_data->title))
                                        <div>{!! $report_data->title !!}</div>
                                      @endif
                                    </td>
                                    @foreach ($report_data->attributes as $item)
                                      <td style="padding-left:8px;text-align:right;white-space:nowrap">
                                      <strong>
                                        @if (!is_null($item->key))
                                          <span style="color:#777">{!! $item->key !!}: </span>
                                        @endif
                                        @if (!is_null($item->value))
                                          <span style="color:#777">{!! $item->value !!}<span>
                                        @endif
                                      </strong>
                                      </td>
                                    @endforeach
                                  </tr>
                                </tbody>
                              </table>
                            </td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </td>
                  </tr>
                </tbody>
                @endfor
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>

</html>