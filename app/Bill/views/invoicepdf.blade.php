<!DOCTYPE html>
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
   <title>Invoice</title>
   <style type="text/css">
	  body{
         margin: 100px !important;
	  }
   </style>
   <link rel="stylesheet" href="{{assetLink('css','bootstrap')}}">
   <!-- Font Awesome Icons -->
</head>
<body>
   <div  class="row">
      <div  class="col-sm-4 pull-left">
      	<?php $output = $invoiceInfo['order']['status'] ? 'PAID' : "UNPAID";?>
         <h3  class="page-header"><i  class="fas fa-globe"></i> {{$output}}
         </h3>
      </div>
   </div>

   <div  class="invoice-info">
      <div  class="row">
         <div  class="col-sm-4">
            <div  class="invoice_box box-solid">
               <div  class="box-header">
                  <h4  class="box-title">From</h4>
               </div>
               <div  class="box-body">
                  @foreach($invoiceInfo['from'] as $key => $value)
                    {!! $value !!}<br/>
                  @endforeach
               </div>
            </div>
         </div>
         <div  class="col-sm-4">
            <div  class="invoice_box box-solid">
               <div  class="box-header">
                  <h4  class="box-title">To</h4>
               </div>
               <div  class="box-body">
                  <address ><strong >{{$invoiceInfo['order']['user']['full_name']}}</strong><br >
                     Email: <b >{{$invoiceInfo['order']['user']['email']}}</b>
                  </address>
               </div>
            </div>
         </div>
         <div  class="col-sm-4">
            <div  class="invoice_box box-solid">
               <div  class="box-header">
                  <h4  class="box-title">Invoice details</h4>
               </div>
               <div  class="box-body">
                  <address ><b >Invoice #{{$invoiceInfo['invoice_id']}}</b><br >
                     Order ID : <b >{{$invoiceInfo['order']['order_id']}}</b><br >
                     Paid on : <b >{{$invoiceInfo['paid_date'] ? faveoDate($invoiceInfo['paid_date']) : null}}</b><br >
                  </address>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div  class="row">
      <div  class="col-md-12">
         <div  class="invoice_box box-solid">
            <div  class="box-header">
               <h4  class="box-title">Package</h4>
            </div>
            <div  class="box-body">
               <table  class="table table-striped">
                  <thead >
                     <tr >
                        <th >Name</th>
                        <th >Description</th>
                        <th >Validity</th>
                        <th >Price</th>
                     </tr>
                  </thead>
                  <tbody >
                     <tr >
                        <td >{{$invoiceInfo['order']['package']['name']}}</td>
                        <td >{{$invoiceInfo['order']['package']['description']}}</td>
                        <td >{{($invoiceInfo['order']['package']['validity']) ? : 'One time'}}</td>
                        <td  id="product_price">
                           <div data-v-36779b28=""  id="Price" class="form-group">
                              <label for="Price" style="display: none;">Price</label> <label style="color: rgb(220, 53, 69); display: none;">*</label> <!----> 
                              <div>
                                 <span data-v-36779b28="" class="inline">{{$invoiceInfo['order']['package']['price']}}</span> <!---->
                              </div>
                           </div>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
   <div  class="row">
      <div  class="col-md-12">
         <div  class="invoice_box box-solid">
            <div  class="box-header">
               <h4  class="box-title">Transaction Details</h4>
            </div>
            <div  class="box-body">
               <table  class="table table-striped">
                  <thead >
                     <tr >
                        <th >Id</th>
                        <th >Payment method</th>
                        <th >Amount paid</th>
                        <th >Paid date</th>
                        <th >Status</th>
                     </tr>
                  </thead>
                  @foreach($invoiceInfo['transactions'] as $transaction)
                  <tbody >
                     <tr >
                        <td >{{$transaction['transactionId']}}</td>
                        <td >{{$transaction['payment_method']}}</td>
                        <td >{{$transaction['amount_paid']}}</td>
                        <td >{{faveoDate($invoiceInfo['paid_date'])}}</td>
                        <td >
                           <?php 
                              $outputStatus = $transaction['status'] ? 'Sucessful' : 'Unsucessful';
                              $btnClass = $transaction['status']? 'btn-success': 'btn-danger';
                           ?>
                           <a  id="status">
                           <span  title="Status" id="edit_btn" class="btn btn-xs {{$btnClass}}">
                        	{{$outputStatus}}
                           </span>
                        </a>
                        </td>
                     </tr>
                  </tbody>
                  @endforeach
               </table>
            </div>
         </div>
      </div>
   </div>
   <div  class="row">
      <div  class="col-md-12">
         <div  class="invoice_box box-solid">
            <div  class="box-header">
               <h4  class="box-title">Payment details</h4>
            </div>
            <div  class="box-body">
               <table  class="table">
                  <tbody >
                     @if($invoiceInfo['paid_date'])
                     <tr >
                        <th  style="width: 50%;">Paid On : </th>
                        <td >{{faveoDate($invoiceInfo['paid_date'])}}</td>
                     </tr>
                     @endif
                     <tr >
                        <th  style="width: 50%;">Paid amount : </th>
                        <td >{{commonSettings('bill', 'currency').' '.$invoiceInfo['amount_paid']}}</td>
                     </tr>
                     <tr >
                        <th  style="width: 50%;">Balance : </th>
                        <td >{{commonSettings('bill', 'currency').' '.($invoiceInfo['amount_paid'] - $invoiceInfo['payable_amount'])}}</td>
                     </tr>
                     <tr >
                        <th >Total:</th>
                        <td >{{commonSettings('bill', 'currency').' '.$invoiceInfo['payable_amount']}}</td>
                     </tr>
                  </tbody>
               </table>
               <br>
            </div>
         </div>
      </div>
   </div>
</body>
<script src="{{assetLink('js','bootstrap')}}" type="text/javascript"></script>
</html>
