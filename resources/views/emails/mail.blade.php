@if($inlineImages)
<?php
  foreach ($inlineImages as $inlineImage) {
    // adding urldecode because htmlpurifier encodes src of the image
    $replace = $message->embedData(file_get_contents($inlineImage['path'].DIRECTORY_SEPARATOR.$inlineImage["name"]), $inlineImage['name']);
    $data = str_replace("cid:".$inlineImage["content_id"], $replace, $data);
  }
?>
@endif
{!! $data !!}
