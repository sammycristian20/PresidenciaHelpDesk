<?php

$filter = new \App\Model\helpdesk\Filters\Filter();
$types = $filter->getTagsByTicketId($tickets->id);

?>
<link rel="stylesheet" href="{{assetLink('css','jquery-ui-base-1')}}">
<link rel="stylesheet" href="{{assetLink('css','tagit-stylish-yellow')}}">

<tr>
    <td>
        <b>Type:</b>
    </td>   
    <td contenteditable='true'>
        <ul id="type" data-name="nameOfSelect" name="type">
            @forelse($types as $type)
            <li>{!! $type !!}</li>
            @empty 
            
            @endforelse
        </ul>
    </td>
</tr>

