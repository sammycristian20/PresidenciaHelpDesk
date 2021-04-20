<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tbody>
    <tr>
      <td colspan="1" rowspan="1" width="100%" style="padding:15px 0;border-top:1px dotted #c5c5c5">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="table-layout:fixed">
          <tbody>
            <tr>
              <td colspan="1" rowspan="1" valign="top" style="padding:0 15px 0 15px;width:40px">

                <a href="{{$userProfilePath}}" target="__blank">
                  <img height="40"
                  src="{{$user->profile_pic}}" style="height:auto;line-height:100%;outline:none;text-decoration:none;border-radius:5px"
                  width="40">
                </a>
              </td>
              <td colspan="1" rowspan="1" valign="top" width="100%" style="padding:0;margin:0">
                <p style="font-family:'Lucida Grande','Lucida Sans Unicode','Lucida Sans',Verdana,Tahoma,sans-serif;font-size:15px;line-height:18px;margin-bottom:0;margin-top:0;padding:0;color:#1b1d1e">
                  <a href="{{$userProfilePath}}" target="__blank" style="text-decoration: none;color:#1b1d1e">
                    <strong>{{$user->full_name}}</strong>
                  </a>
                </p>
                <p style="font-family:'Lucida Grande','Lucida Sans Unicode','Lucida Sans',Verdana,Tahoma,sans-serif;font-size:13px;line-height:25px;margin-bottom:15px;margin-top:0;padding:0;color:#bbbbbb">
                  {{$createdAt}}
                </p>
                {!! $threadBody !!}
              </td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>
