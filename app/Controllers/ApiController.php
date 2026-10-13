<?php


namespace App\Controllers;



class ApiController extends Controller
{

    public function __construct()
    {

    }

public function testingmail(Request $request) {

$email='sampletestingteam@gmail.com';

$message='Testing From Raju';

$data = ([
 'name' => 'Raju',
 'email' => $email,
  'message' => $message
 ]);


$settings= DB::table('page_settings')->where('id',1)->get();

            $terms=$settings[0]->welcomeemail;
          

//Mail::to($email)->send(new WelcomeEmail($data));

//Mail::send(new WelcomeEmail($message),[],function($message){ $message->from('Support@bysos.in')->to('sampletestingteam@gmail.com')->subject('Welcome Mail'); });

/*Mail::send('admin.welcomemail',array(
            'name' => $terms),function($message){ $message->from('info@swiclo.com')->to('sampletestingteam@gmail.com')->subject('Welcome Mail'); });*/


   Mail::send('admin.welcomemail',array(
            'name' => $message),function($message)  use ($email) { $message->from('info@swiclo.com')->to($email)->subject('Support Ticket'); });


}


public function testing(Request $request) {
    
    require __DIR__ . '/pushnotification1.php';

    echo $result = $push->send(array('type'=>"order",'read_notify'=>1,'designid'=>0,'customer'=>0,'id'=>0,'mtitle'=>"testing",'mdesc'=>'testing','buzzer'=>''),'ftRDujUHBxU:APA91bHeOlbUYHIx-Qla4BxDrEZu0DsvOzrxSms80tRefwuxg6B4Z_W9E79IffCKmesnlyQWTlJGDHwhqNmIGiPBjI4Yac3ySbFmUmBbOTKV0rBam4HTFHSenvL3TMz0VbBEB0-L5BjS');

/* $phone = $request->phone;

          $data = ['phone' => '+34'.$phone, 'text' => 'Testing From Prominere'];

            $sid = 'AC51b09fcdafdd4dfb8c908ee9891d525e';
            $token = 'd754bbb142a2f3f71f10643edbae86c3';
            
            
            
            $client = new Client($sid, $token);
            
          
             $msg= $client->messages->create(
                
                $data['phone'],
                array(
                    
                    "from" => "Swiclo",
                   
                    'body' => $data['text']
                )
            );
            
            if($msg)
            {
             return response()->json(['status' => 'success','message'=>$msg ],200);
            }*/
            
     }       
          


function ibanadd(Request $request) {

$customer = UserAccount::where('id',$request->userid)->first();

/*$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/bankaccounts');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,'customer_id='.$customer['trustid'].'&name='.$request->name.'&type=IBAN&number='.$request->number.'&bic='.$request->bic);

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

 $result = curl_exec($ch);

 $res=explode('{"data":{"id":',$result);//print_r($res);exit;

$res2=explode(',',$res[1]);*/







         $userlog = new Useriban();

           $trans1 =DB::table('user_banks')->where('number', $request->number)->where('userid', $request->userid)->get();
           $trans2 =DB::table('user_banks')->where('bic', $request->bic)->where('userid', $request->userid)->get();
          
          if($request->number==''  || $request->bic=='')
          {
               return response()->json(['status' => 'Failed','message' => 'Please provide all info'],200);
          }
          else if(count($trans1)!=0 || count($trans2)!=0)
          {
               return response()->json(['status' => 'Failed','message' => 'Same bank cannot be added'],200);
          }
          else
          {


            $ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/bankaccounts');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,'customer_id='.$customer['trustid'].'&name='.$request->name.'&type=IBAN&number='.$request->number.'&bic='.$request->bic);

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

 $result = curl_exec($ch);

 $res=explode('{"data":{"id":',$result);//print_r($res);exit;

$res2=explode(',',$res[1]);


             $userlog->number = $request->number;
           // $userlog->code = $request->code;
            $userlog->bic = $request->bic;
             $userlog->userid = $request->userid;
             $userlog->bank_id = $res2[0];

             $trans =DB::table('user_banks')->where('userid', $request->userid)->get();

             if(count($trans)!=0)
               $userlog->status = 0;
              else
              $userlog->status = 1;


             $us=$userlog->save();
            if($us)
              return response()->json(['status' => 'success','message'=>"Successfully added iban" ],200);    

          }

        





if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);


/*$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/customers/861/bankaccounts');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch); echo $result;exit;

$res=json_decode($result);

foreach($res as $key=>$val)
{
 
}

$arr=array();

for($i=0;$i<count($res);$i++)
{
  array_push($arr,array("id"=>$res[$i]->id,"number"=>$res[$i]->number));

}


return response()->json(['status' => 'success','accounts'=>$arr],200);

exit;

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);*/



}


 function payout(Request $request) {


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/orders');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,'buyer_id='.$request->customer_id.'&seller_id='.$request->customer_id.'&name=Money adding to bank&value='.$request->amount);

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);


$res=explode('{"data":{"id":',$result);//print_r($res);exit;

$res2=explode(',',$res[1]);

$aa=explode('"',$res2[0]);//print_r($aa);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/payouts');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,'order_id='.$aa[1].'&bankaccount_id='.$request->bankid.'&type=ACCOUNT');

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);

echo $result;exit;



if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

//echo $res1[0];exit;

$link=stripslashes($res0[1]);

return $arr=array('status' => 'success','link'=>$link);

 //return response()->json(['status' => 'success','link'=>$res1[0],'confirmurl'=>$res3[0] ],200);


 




  }

 function withdraw_payout(Request $request) {

$customer = UserAccount::where('id',$request->userid)->first();

  	$ch = curl_init();
 
curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/orders/'.$request->order_id.'/accept');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);


$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);




curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/orders/'.$request->order_id.'/validate');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);


$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);


curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/payouts');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,'order_id='.$request->order_id.'&bankaccount_id='.$request->bank_id.'&type=ACCOUNT');

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);

$balance=$customer['current_balance']-$request->amount;

$customer->current_balance=$balance;
$customer->save();


  $transid='TRX'.rand('00000','99999');

        $trans_date=date("Y-m-d H:i:s");


        $amount=$request->amount;

         $sendertrans1 = new Transaction();

              $sendertrans1->mainacc = $customer['id'];
              //$sendertrans1->accto = $customer['id'];
              $sendertrans1->transid = $transid;
              $sendertrans1->type = 'withdraw';
              $sendertrans1->amount = $amount;
              $sendertrans1->sign = '-';
              $sendertrans1->trans_date = $trans_date;

              $sendertrans1->save();


 require __DIR__ . '/pushnotification1.php';

if($customer['lang']=='english')
{
	$mtitle='Withdraw Money';
 $msg=$request->amount. '€ withdrawn by you';
}
else
{
	$mtitle='Retirar dinero';
	$msg=$request->amount. '€ retirado por ti';
}

 if($customer['devicetoken']!='')
 {
     if($customer['deviceid']=='ios')
     {
        $result = $push->send(array('type'=>"withdrawmoney",'read_notify'=>1,'designid'=>0,'customer'=>0,'id'=>0,'mtitle'=>$mtitle,'mdesc'=>$msg,'buzzer'=>''),$customer['devicetoken']);
     }
     else
     {
        $result = $push->send1(array('type'=>"withdrawmoney",'read_notify'=>1,'designid'=>0,'customer'=>0,'id'=>0,'mtitle'=>$mtitle,'mdesc'=>$msg,'buzzer'=>''),$customer['devicetoken']);
     }
    
 }

              $noti = new Notifications();

              $noti->userid = $request->userid;
           
              $noti->message = $msg;
              $noti->status = 0;
            

              $noti->save();



return $arr=array('status' => 'success');

 }


 

 function getwalletamount(Request $request) {

$customer = UserAccount::where('id',$request->userid)->first();

$ch = curl_init();


curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/customers/'.$customer['trustunique'].'/wallets');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 0);
//curl_setopt($ch, CURLOPT_POSTFIELDS,'order_id='.$request->order_id.'&wallet_id='.$customer['walletid'].'&type=WALLET');

//sk_production_8O4x0hg0v7eODLqnAnFCUblN

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result1 = curl_exec($ch);

$res00=explode('{"data":[{"id":',$result1);

      $res20=explode(',',$res00[1]); 

      $aa00=explode('"balance":',$res20[3]);

      //$balance=$customer['current_balance']+$aa00[1];

        $balance=$aa00[1];

        return $arr=array('status' => 'success','balance' => $balance);


 }

   function paymentout(Request $request) {

$customer = UserAccount::where('id',$request->userid)->first();

  	$ch = curl_init();
 
curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/orders/'.$request->order_id.'/accept');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);


$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);




curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/orders/'.$request->order_id.'/validate');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);


$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);





curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/payouts');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,'order_id='.$request->order_id.'&wallet_id='.$customer['walletid'].'&type=WALLET');

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);



curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/customers/'.$customer['trustunique'].'/wallets');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 0);
//curl_setopt($ch, CURLOPT_POSTFIELDS,'order_id='.$request->order_id.'&wallet_id='.$customer['walletid'].'&type=WALLET');

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result1 = curl_exec($ch);

$res00=explode('{"data":[{"id":',$result1);

			$res20=explode(',',$res00[1]); 

			$aa00=explode('"balance":',$res20[3]);

			//$balance=$customer['current_balance']+$aa00[1];

        $balance=$aa00[1];



        $transid='TRX'.rand('00000','99999');

        $trans_date=date("Y-m-d H:i:s");


        $amount=$request->amount;

         $sendertrans1 = new Transaction();

              $sendertrans1->mainacc = $customer['id'];
             // $sendertrans1->accto = $customer['id'];
              $sendertrans1->transid = $transid;
              $sendertrans1->type = 'deposit';
              $sendertrans1->amount = $amount;
              $sendertrans1->sign = '+';
              $sendertrans1->trans_date = $trans_date;

              $sendertrans1->save();




$customer->current_balance=$balance;
$customer->save();


 require __DIR__ . '/pushnotification1.php';

 if($customer['lang']=='english')
 {
 $msg=$request->amount. '€ deposited by you';
 $mtitle='Add Money';
}
else
{
	 $msg=$request->amount. '€ depositado por ti';
	 $mtitle='Agregar dinero';
}

 if($customer['devicetoken']!='')
 {
     if($customer['deviceid']=='ios')
     {
     $result = $push->send(array('type'=>"addmoney",'read_notify'=>1,'designid'=>0,'customer'=>0,'id'=>0,'mtitle'=>$mtitle,'mdesc'=>$msg,'buzzer'=>''),$customer['devicetoken']);
    }
    else
    {
       $result = $push->send1(array('type'=>"addmoney",'read_notify'=>1,'designid'=>0,'customer'=>0,'id'=>0,'mtitle'=>$mtitle,'mdesc'=>$msg,'buzzer'=>''),$customer['devicetoken']);
    }
 }

              $noti = new Notifications();

              $noti->userid = $request->userid;
           
              $noti->message = $msg;
              $noti->status = 0;
            

              $noti->save();


return $arr=array('status' => 'success');


   }
   


    function withdraw_pay(Request $request) {

    		$customer = UserAccount::where('id',$request->userid)->first();

    		$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/customers/'.$customer['trustunique'].'/wallets');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 0);
//curl_setopt($ch, CURLOPT_POSTFIELDS,'order_id='.$request->order_id.'&wallet_id='.$customer['walletid'].'&type=WALLET');

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result1 = curl_exec($ch);

$res00=explode('{"data":[{"id":',$result1);

			$res20=explode(',',$res00[1]); 

			$aa00=explode('"balance":',$res20[3]);

        $balance=$aa00[1];


 			if($balance<$request->amount)
            {
               return response()->json(['status' => 'error','message'=>"Requested amount is more than in your wallet." ],200);
            }

    		if(($customer['verifystatus']==0 && $customer['pending']==0 && $customer['accept']==0) || ($customer['verifystatus']==1 && $customer['pending']==1 && $customer['accept']==0) ) 
    		{ 
    			 $trans =DB::table('transactions')->orderby('id', 'DESC')->get();

    			 $startdate=date("Y-m-d",strtotime($customer['startdate']));
    			 $enddate=date("Y-m-d",strtotime($customer['enddate']));

    			 $amount=0;
                
                  foreach($trans as $key=>$val)
                  {
                  	$date=date("Y-m-d",strtotime($val->trans_date));

                  		if($date>=$startdate && $date<=$enddate && $val->type=='withdraw' && $val->mainacc==$request->userid)
                  		{
                  			$amount+=$val->amount;
                  		}


                  }

                   $finalamt=$request->amount+$amount;

                  $limit=250;


                    //if($amount>999)
                    	if($finalamt>$limit)
					{
						 $bal=$limit-$amount;

                         if($bal==0)
                        	$ms="You dont have anymore eligibility to withdraw";
                        else
						$ms="You have eligibility of ".$bal." euros only to withdraw";

					return $arr=array('status' => 'failed','verifystatus'=>0,'message'=>$ms);

					}
					else
					{



    		


			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/orders');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,'buyer_id='.$customer['trustid'].'&seller_id='.$customer['trustid'].'&name=Withdraw money from Wallet&value='.$request->amount);

			$headers = array();
			$headers[] = 'Accept: application/json';
			$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$result = curl_exec($ch);


			$res=explode('{"data":{"id":',$result);

			$res2=explode(',',$res[1]);

			$aa=explode('"',$res2[0]);//print_r($aa);

			if (curl_errno($ch)) {
			    echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);


			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/payins');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,'order_id='.$aa[1].'&type=WALLET&wallet_id='.$customer['walletid']);

			$headers = array();
			$headers[] = 'Accept: application/json';
			$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$result = curl_exec($ch);

			$res=explode('"direct_link":',$result);



			$res1=explode('","created_at"',$res[1]);



			$res0=explode('"',$res1[0]);



			if (curl_errno($ch)) {
			    echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);


			$link=stripslashes($res0[1]);

			return $arr=array('status' => 'success','link'=>$link,'order_id'=>$aa[1],'amount'=>$request->amount,'bank_id'=>$request->bank_id);

		

					}
    	}

    		else if(($customer['verifystatus']==1 && $customer['pending']==0 && $customer['accept']==1) || ($customer['verifystatus']==2 && $customer['pending']==1 && $customer['accept']==0) ) 
    		{
    			 $trans =DB::table('transactions')->orderby('id', 'DESC')->get();

    			 $startdate=date("Y-m-d",strtotime($customer['startdate']));
    			 $enddate=date("Y-m-d",strtotime($customer['enddate']));

    			 $amount=0;
                
                  foreach($trans as $key=>$val)
                  {
                  	$date=date("Y-m-d",strtotime($val->trans_date));

                  		if($date>=$startdate && $date<=$enddate  && $val->type=='withdraw' && $val->mainacc==$request->userid)
                  		{
                  			$amount+=$val->amount;
                  		}


                  }

                   $finalamt=$request->amount+$amount;

                  $limit=1999;

                   // if($amount>1999)
                    	if($finalamt>$limit)
					{
						 $bal=$limit-$amount;

						  if($bal==0)
                        	$ms="You dont have anymore eligibility to withdraw";
                        else
						$ms="You have eligibility of ".$bal." euros only to withdraw";

					return $arr=array('status' => 'failed','verifystatus'=>1,'message'=>$ms);

					

					}

					else
					{


    		


			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/orders');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,'buyer_id='.$customer['trustid'].'&seller_id='.$customer['trustid'].'&name=Withdraw money from Wallet&value='.$request->amount);

			$headers = array();
			$headers[] = 'Accept: application/json';
			$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$result = curl_exec($ch);


			$res=explode('{"data":{"id":',$result);

			$res2=explode(',',$res[1]);

			$aa=explode('"',$res2[0]);//print_r($aa);

			if (curl_errno($ch)) {
			    echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);


			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/payins');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,'order_id='.$aa[1].'&type=WALLET&wallet_id='.$customer['walletid']);

			$headers = array();
			$headers[] = 'Accept: application/json';
			$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$result = curl_exec($ch);

			$res=explode('"direct_link":',$result);



			$res1=explode('","created_at"',$res[1]);



			$res0=explode('"',$res1[0]);



			if (curl_errno($ch)) {
			    echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);


			$link=stripslashes($res0[1]);

			return $arr=array('status' => 'success','link'=>$link,'order_id'=>$aa[1],'amount'=>$request->amount,'bank_id'=>$request->bank_id);

		
		
					}



    	}


    		else
    		{

    		


			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/orders');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,'buyer_id='.$customer['trustid'].'&seller_id='.$customer['trustid'].'&name=Withdraw money from Wallet&value='.$request->amount);

			$headers = array();
			$headers[] = 'Accept: application/json';
			$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$result = curl_exec($ch);


			$res=explode('{"data":{"id":',$result);

			$res2=explode(',',$res[1]);

			$aa=explode('"',$res2[0]);//print_r($aa);

			if (curl_errno($ch)) {
			    echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);


			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/payins');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,'order_id='.$aa[1].'&type=WALLET&wallet_id='.$customer['walletid']);

			$headers = array();
			$headers[] = 'Accept: application/json';
			$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$result = curl_exec($ch);

			$res=explode('"direct_link":',$result);



			$res1=explode('","created_at"',$res[1]);



			$res0=explode('"',$res1[0]);



			if (curl_errno($ch)) {
			    echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);


			$link=stripslashes($res0[1]);

			return $arr=array('status' => 'success','link'=>$link,'order_id'=>$aa[1],'amount'=>$request->amount,'bank_id'=>$request->bank_id);

		}


    }



           function payment(Request $request) {


$customer = UserAccount::where('id',$request->userid)->first();

     	


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/orders');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,'buyer_id='.$customer['trustid'].'&seller_id='.$customer['trustid'].'&name=Money adding to wallet Wallet&value='.$request->amount);

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);



/*$res=explode('"buyer_link":',$result);



$res1=explode('=> "https://checkout.truust.io/',$res[1]);


$res3=explode(',"seller_link"',$res1[0]);*/



$res=explode('{"data":{"id":',$result);

$res2=explode(',',$res[1]);

$aa=explode('"',$res2[0]);//print_r($aa);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/payins');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,'order_id='.$aa[1].'&type=REDSYS');

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);

/*$res=json_decode($result);

foreach ($res as $key => $value) {
    
    $link= $value->direct_link;
}*/

$res=explode('"direct_link":',$result);//print_r($res);exit;



$res1=explode('","created_at"',$res[1]);//print_r($res1);exit;



$res0=explode('"',$res1[0]);//print_r($res0);exit;



if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

//echo $res1[0];exit;

$link=stripslashes($res0[1]);

return $arr=array('status' => 'success','link'=>$link,'order_id'=>$aa[1],'amount'=>$request->amount);

 //return response()->json(['status' => 'success','link'=>$res1[0],'confirmurl'=>$res3[0] ],200);


 }

 

    function getstripetoken(Request $request) {

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api-sandbox.stripe.com/v1/tokens');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);

curl_setopt($ch, CURLOPT_POSTFIELDS, "card[number]=4242424242424242&card[exp_month]=12&card[exp_year]=2020&card[cvc]=314");



curl_setopt($ch, CURLOPT_USERPWD, 'sk_live_hUSp6UvOBZ6g6gbbPTTLdgaH00goFwY953
' . ':' . '');

$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);

 return response()->json(['status' => 'success','token'=>$result ],200);


if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);
    }

            
    function goRandomString($length = 10) {
        
        $characters = 'abcdefghijklmnpqrstuvwxyz123456789';
        $string = '';
        $max = strlen($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, $max)];
        }
        return $string;
    }
    

     public function notificationstatus(Request $request){ 

     	$customer = UserAccount::where('id',$request->userid)->first();

     	if($customer['notification']==0)
     		$status=1;
     	else
     		$status=0;

        	   $customer_data1 = array(
                'notification'      =>  $status

              );

              DB::table('user_profiles')->where('id', $request->userid)->update($customer_data1);
              
              
              return response()->json(['status' => 'success','message'=>"Successfully changed status" ],200);


     }

        public function closeaccount(Request $request){ 

        	   $customer_data1 = array(
                'status'      =>  9

              );

              DB::table('user_profiles')->where('id', $request->userid)->update($customer_data1);
              
              
              return response()->json(['status' => 'success','message'=>"Successfully closed account" ],200);



        }
    
       public function withdrawmoney(Request $request){ 
           
               $customer = UserAccount::where('phone',$request->phone)->first();


        if($customer)
        {
            if($customer->current_balance<$request->amount)
            {
               return response()->json(['status' => 'error','message'=>"Requested amount is more than in your wallet." ],200);
            }
            
            else
            {
                
              $balance=$customer->current_balance-$request->amount;
                
              $fromid=$customer['id'];

              $transid='TRX'.rand('00000','99999');

              $amount=$request->amount;


              $type='withdraw';
              $sign='-';

              $trans_date=date("Y-m-d H:i:s");

              $sendertrans1 = new Transaction();

              $sendertrans1->mainacc = $fromid;
              $sendertrans1->transid = $transid;
              $sendertrans1->type = $type;
              $sendertrans1->amount = $amount;
              $sendertrans1->sign = $sign;
              $sendertrans1->trans_date = $trans_date;


              $sendertrans1->save();

              $trx = Transaction::where('transid',$transid)->first();

              $ex=explode(' ',$trx['trans_date']);

              $st=$ex[1];

              $ex1=explode(':',$st);

              if($ex1[0]>1 && $ex1[0]<12)
                $t='AM';
              else
                $t='PM';

              $time1=$ex1[0].':'.$ex1[1].' '.$t;
              $time2=date("d M Y",strtotime($trx['trans_date']));


              
              $sendertrans = new Withdraw();

              $sendertrans->acc = $fromid;
              $sendertrans->amount = $amount;
              $sendertrans->status = 'pending';

              $sendertrans->save();
              
                  $customer_data1 = array(
                'current_balance'      =>  $balance

              );

              DB::table('user_profiles')->where('phone', $request->phone)->update($customer_data1);
              
              
              return response()->json(['status' => 'success','trxdate' => $time1.','.$time2,'message'=>$request->amount." has been withdrawn from your wallet" ],200);
              
              
            }
            
        }
        
        
           
       }
       

       public function getdetails(Request $request){ 

       	$customer = UserAccount::where('id',$request->userid)->first();

        if($customer['name']==null)
          $name='';
        else
          $name=$customer['name'];

        if($customer['photo']==null)
          $image='';
        else
          $image='https://swiclo.eu/assets/images/'.$customer['photo'];

       	 return response()->json(['status' => 'success','name'=>$name,'image'=>$image],200);

       }

       public function updatedetails(Request $request){ 

       	 $userlog = new UserAccount();
       	 $name = $request->name;

       	     if(!empty($request->file('profile')))
              {
                $file = $request->file('profile');
                $filename = time().$request->file('profile')->getClientOriginalName();
                $file->move('assets/images',$filename);
                $photo = $filename;

                $customer_data1 = array(
                'name'      =>  $name,
                'photo'      =>  $photo,

             	 );

              }
              else
              {
              	$customer_data1 = array(
                'name'      =>  $name

             		 );
              }

               

              DB::table('user_profiles')->where('id', $request->userid)->update($customer_data1);

               
               return response()->json(['status' => 'success','message' => 'Successfully updated details'],200);

       }


       public function withdrawlimit(Request $request){ 

         $userlog = new Withdrawls();

          $userlog->acc = $request->userid;
          $userlog->amount = $request->amount;

           $userlog->type = $request->type;

          if(!empty($request->file('profile')))
              {
                $file = $request->file('profile');
                $filename = time().$request->file('profile')->getClientOriginalName();
                $file->move('assets/images',$filename);
                $userlog->image = $filename;

              }
              
               $userlog->save();

               $customer = UserAccount::where('id',$request->userid)->first();

               if($customer['verifystatus']==1 && $customer['accept']==1)
               {
               	    $customer_data = array(
                'pending'      =>  1,
                'accept'      =>  0,
                'verifystatus'      =>  2

                   );

               }

               else  if($customer['verifystatus']==0 && $customer['accept']==0)
               {
               	    $customer_data = array(
                'pending'      =>  1,
                'accept'      =>  0,
                'verifystatus'      =>  1

                   );

               }

               else
               {


                $customer_data = array(
                'pending'      =>  1,
                'accept'      =>  0

                 );

               }

    DB::table('user_profiles')->where('id', $request->userid)->update($customer_data);

                return response()->json(['status' => 'success','message'=>"Successfully raised the withdraw limit" ],200);

       }
    
      public function register(Request $request){ 
          
          $userlog = new UserAccount();
          
  /*        if($request->name=='' || $request->password=='' || $request->phone=='' || $request->email=='')
          {
               return response()->json(['status' => 'Failed','message' => 'Please provide all info'],200);
          }
          else
          {*/


          	$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/customers');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,'email='.$request->email);

			$headers = array();
			$headers[] = 'Accept: application/json';
			$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		   $result = curl_exec($ch);

			$res=explode('{"data":{"id":',$result);

			$res2=explode(',',$res[1]); 

			$aa=explode('"',$res2[0]);

			$aa1=explode('customers\/',$res2[1]);

			$aa2=explode('"',$aa1[1]);

			curl_close($ch);



			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/customers/'.$aa2[0].'/wallets');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 0);
			//curl_setopt($ch, CURLOPT_POSTFIELDS,'email=='.$request->email);

			$headers = array();
			$headers[] = 'Accept: application/json';
			$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$result1 = curl_exec($ch);

			$res00=explode('{"data":[{"id":',$result1);

			$res20=explode(',',$res00[1]); 

			$aa00=explode('"',$res20[0]);




			$userlog->trustid=$aa[0];
			$userlog->trustunique=$aa2[0];
			$userlog->walletid=$aa00[0];

            $userlog->lang = $request->lang;

            $userlog->deviceid = $request->deviceid;
            $userlog->devicetoken = $request->devicetoken;
            $userlog->type = $request->type;

            $userlog->email = $request->email;

              $userlog->name = $request->name;
              $userlog->phone = $request->phone;
              $userlog->password=Hash::make($request->password);
              $userlog->status = 1;
              $userlog->qrcode = $this->goRandomString(5);
               

                if(!empty($request->type_doc))
              {
                $userlog->type_doc = $request->type_doc;

              }
              
              if(!empty($request->file('profile')))
              {
                $file = $request->file('profile');
                $filename = time().$request->file('profile')->getClientOriginalName();
                $file->move('assets/images',$filename);
                $userlog->document = $filename;

              }

                 $userlog->startdate= date('Y-m-d H:i:s'); 

                 $userlog->enddate= date('Y-m-d H:i:s', strtotime(' + 30 days')); 
              
               $userlog->save();

               $settings= DB::table('page_settings')->where('id',1)->get();

            $terms=$settings[0]->welcomeemail;

            $email=$request->email;

               Mail::send('admin.welcomemail',array(
            'name' => $terms),function($message)  use ($email) { $message->from('info@swiclo.com')->to($email)->subject('Welcome Mail'); });

               $customers = UserAccount::where('phone',$request->phone)->first();
               
               return response()->json(['status' => 'success','userid' => $customers['id'],'trustid' => $customers['trustid'],'walletid' => $customers['trustunique'],'qrcode' => $customers['qrcode'],'message' => 'Successfully Registered'],200);
         // }
          
      }
      
      
        public function otpverify(Request $request){ 
            
            $customer = PhoneOtp::where('phone',$request->phone)->first();
            
           if($customer['otp']==$request->otp)
           {
                return response()->json(['status' => 'success','userid' => $customer['id'],'phone' => $customer['phone'],'message'=>"Successfully verified." ],200);
           }
           else
           {
                return response()->json(['status' => 'error','message'=>"Invalid OTP." ],200);
           }
           
        }
      
      


        public function getqrcode(Request $request){ 

          $customer = UserAccount::where('id',$request->userid)->first();


           $img='<img src="https://api-sandbox.qrserver.com/v1/create-qr-code/?data='.$customer['qrcode'].'&amp;size=100x100" width="100" height="100" />';

          return response()->json(['status' => 'success','qrcode' => $customer['qrcode'],'image' => $img],200);

        }
        
        
        
        
           public function requestmoney(Request $request){ 

          $customer = UserAccount::where('phone',$request->phone)->first();

          $to = UserAccount::where('phone',$request->tophone)->first();

        if($customer)
        {
            if(empty($to))
            {
               return response()->json(['status' => 'error','message'=>"To phone number does not exist." ],200);
            }
            else if($request->phone==$request->tophone)
            {
               return response()->json(['status' => 'error','message'=>"Cant request to yourself." ],200);
            }
            else
            {

              $tobalance=$request->amount+$to->current_balance;
              $frombalance=$customer->current_balance-$request->amount;

              $fromid=$customer['id'];
              $toid=$to['id'];
              $transid='TRX'.rand('00000','99999');

              $type='requestmoney';
              $sign='';
              $amount=$request->amount;
              $trans_date=date("Y-m-d H:i:s");

              $sendertrans1 = new Transaction();

              $sendertrans1->accfrom = $fromid;
              $sendertrans1->accto = $toid;
              $sendertrans1->transid = $transid;
              $sendertrans1->type = $type;
              $sendertrans1->amount = $amount;
              $sendertrans1->sign = $sign;
              $sendertrans1->trans_date = $trans_date;

              $sendertrans1->message = $request->message;

              $sendertrans1->save();

              $trx = Transaction::where('transid',$transid)->first();


    $ex=explode(' ',$trx['trans_date']);

    $st=$ex[1];

    $ex1=explode(':',$st);

    if($ex1[0]>1 && $ex1[0]<12)
      $t='AM';
    else
      $t='PM';

    $time1=$ex1[0].':'.$ex1[1].' '.$t;
    $time2=date("d M Y",strtotime($trx['trans_date']));



              $amount=$request->amount;
              $trans_date=date("Y-m-d H:i:s");

              $sendertrans = new Requests();
              $sendertrans->message = $request->message;
              $sendertrans->accfrom = $fromid;
              $sendertrans->accto = $toid;
              $sendertrans->reference = $transid;
              $sendertrans->amount = $amount;
              $sendertrans->req_date = $trans_date;

              $sendertrans->save();

              $trx1 = Requests::where('reference',$transid)->first();


               require __DIR__ . '/pushnotification1.php';

$fromcustomer = UserAccount::where('id',$fromid)->first();

 if($to['lang']=='english')
 {
 	$mtitle='Recieved Request';

 	$msg='Received request from '.ucwords($fromcustomer->name);

 }
 else
 {
 	$mtitle='Solicitud Recibida';
 	$msg='Solicitud recibida de '.ucwords($fromcustomer->name);
 }
 

 if($to['devicetoken']!='')
 {
    if($to['deviceid']=='ios')
     {
     $result = $push->send(array('type'=>"requestmoney",'read_notify'=>1,'designid'=>0,'customer'=>0,'id'=>0,'mtitle'=>$mtitle,'mdesc'=>$msg,'buzzer'=>''),$to['devicetoken']);
     }
     else
     {
        $result = $push->send1(array('type'=>"requestmoney",'read_notify'=>1,'designid'=>0,'customer'=>0,'id'=>0,'mtitle'=>$mtitle,'mdesc'=>$msg,'buzzer'=>''),$to['devicetoken']);
     }
 }

              $noti = new Notifications();

              $noti->userid = $toid;
           
              $noti->message = $msg;
              $noti->status = 0;
            

              $noti->save();


   

              return response()->json(['status' => 'success','req_date' => $time1.','.$time2,'trxid' => $trx['transid'],'message'=>"Request sent to ".ucwords($to['name']) ],200);
            }

        }


       }
       
        public function addmoney(Request $request){ 

        	$customer = UserAccount::where('phone',$request->phone)->first();


              $fromid=$customer['id'];

              $transid='TRX'.rand('00000','99999');
              $type='deposit';
              $sign='+';
              $amount=$request->amount;
              $trans_date=date("Y-m-d H:i:s");

              $sendertrans = new Transaction();

              $sendertrans->mainacc = $fromid;

              $sendertrans->transid = $transid;
              $sendertrans->type = $type;
              $sendertrans->amount = $amount;
              $sendertrans->sign = $sign;
              $sendertrans->trans_date = $trans_date;

              $sendertrans->save();

              $trx = Transaction::where('transid',$transid)->first();

              $ex=explode(' ',$trx['trans_date']);

    $st=$ex[1];

    $ex1=explode(':',$st);

    if($ex1[0]>1 && $ex1[0]<12)
      $t='AM';
    else
      $t='PM';

    $time1=$ex1[0].':'.$ex1[1].' '.$t;
    $time2=date("d M Y",strtotime($trx['trans_date']));




        	$amount=$request->amount+$customer['current_balance'];

        	  $customer_data = array(
                'current_balance'      =>  $amount

              );

    		DB::table('user_profiles')->where('phone', $request->phone)->update($customer_data);


              return response()->json(['status' => 'success','trxdate' => $time1.','.$time2,'message'=>$request->amount." has been add to your wallet." ],200);


        }


public function deleteiban(Request $request){

 $customer = Useriban::where('id',$request->id)->delete();

  return response()->json(['status' => 'success','message'=>"Successfully deleted iban" ],200);

}


public function deletecard(Request $request){

 $customer = Cards::where('id',$request->id)->delete();

  return response()->json(['status' => 'success','message'=>"Successfully deleted card" ],200);

}

public function updateiban(Request $request){

  $customer_data = array(
                'number'      =>  $request->number,
                'code'      =>  $request->code,
                'bic'      =>  $request->bic

              );

    DB::table('user_banks')->where('id', $request->id)->update($customer_data);

     return response()->json(['status' => 'success','message'=>"Successfully updated iban" ],200);

}


 public function clearnotifications(Request $request){

  $customer_data = array(
                'status'      =>  1

              );

    DB::table('notifications')->where('userid', $request->userid)->update($customer_data);

     return response()->json(['status' => 'success','message'=>"Successfully cleared notifications" ],200);

 }

 public function getnotifications(Request $request){


$arr=array();

      $trans =DB::table('notifications')->where('userid', $request->userid)->where('status', 0)->orderby('id', 'DESC')->get();
     
                  foreach($trans as $key=>$val)
                  {
                    

                       array_push($arr,array('id'=>$val->id,'message'=>$val->message,'created_at'=>$val->created_at));
                    
                  }

  if(!empty($arr))  
              return response()->json(['status' => 'success','data'=>$arr ],200);

            else
               return response()->json(['status' => 'error','message'=>"No Notifications Yet" ],200);

 }

 public function getibanlist(Request $request){

   $arr=array();

      $trans =DB::table('user_banks')->where('userid', $request->userid)->get();
     
                  foreach($trans as $key=>$val)
                  {
                    

                       array_push($arr,array('id'=>$val->id,'bank_id'=>$val->bank_id,'code'=>$val->code,'number'=>$val->number,'bic'=>$val->bic,'status'=>$val->status));
                    
                  }


                if(!empty($arr))  
              return response()->json(['status' => 'success','data'=>$arr ],200);

            else
               return response()->json(['status' => 'error','message'=>"No iban has been added yet" ],200);


 }

 public function getibandetails(Request $request){

  $trans =DB::table('user_banks')->where('id',$request->id)->get();
     
                  foreach($trans as $key=>$val)
                  {
 return response()->json(['status' => 'success','number'=>$val->number,'code'=>$val->code,'bic'=>$val->bic,'status'=>$val->status ],200);
                  }

 }

 public function primaryiban(Request $request){

   $customer_data = array(
                'status'      =>  1

              );

    DB::table('user_banks')->where('id', $request->id)->where('userid', $request->userid)->update($customer_data);

     $trans =DB::table('user_banks')->get();
     
                  foreach($trans as $key=>$val)
                  {
                    if($val->id!=$request->id)
                    {
                                      $customer_data1 = array(
                                'status'      =>  0

                              );

                    DB::table('user_banks')->where('id', $val->id)->where('userid', $request->userid)->update($customer_data1);

                    }
                  }
     

    return response()->json(['status' => 'success','message'=>"Successfully maked iban primary" ],200);


 }


 public function primarycard(Request $request){

   $customer_data = array(
                'status'      =>  1

              );

    DB::table('user_cards')->where('id', $request->id)->update($customer_data);


    $trans =DB::table('user_cards')->get();
     
                  foreach($trans as $key=>$val)
                  {
                    if($val->id!=$request->id)
                    {
                                      $customer_data1 = array(
                                'status'      =>  0

                              );

                    DB::table('user_cards')->where('id', $val->id)->update($customer_data1);

                    }
                  }
     

    return response()->json(['status' => 'success','message'=>"Successfully maked card primary" ],200);


 }

 public function listcards(Request $request){

    $arr=array();

      $trans =DB::table('user_cards')->where('userid', $request->userid)->get();
     
                  foreach($trans as $key=>$val)
                  {
                    

                       array_push($arr,array('id'=>$val->id,'month'=>$val->month,'year'=>$val->year,'name'=>$val->name,'number'=>$val->number,'cvv'=>$val->cvv,'status'=>$val->status));
                    
                  }


                if(!empty($arr))  
              return response()->json(['status' => 'success','data'=>$arr ],200);

            else
               return response()->json(['status' => 'error','message'=>"No cards has been added yet" ],200);

 }


      public function addiban(Request $request){

         $userlog = new Useriban();

           $trans1 =DB::table('user_banks')->where('number', $request->number)->get();
           $trans2 =DB::table('user_banks')->where('bic', $request->cvv)->get();
          
          if($request->number=='' || $request->code=='' || $request->bic=='')
          {
               return response()->json(['status' => 'Failed','message' => 'Please provide all info'],200);
          }
          else if(count($trans1)!=0 || count($trans2)!=0)
          {
               return response()->json(['status' => 'Failed','message' => 'Same bank cannot be added'],200);
          }
          else
          {
             $userlog->number = $request->number;
            $userlog->code = $request->code;
            $userlog->bic = $request->bic;
             $userlog->userid = $request->userid;

             $trans =DB::table('user_banks')->where('userid', $request->userid)->get();

             if(count($trans)!=0)
               $userlog->status = 0;
              else
              $userlog->status = 1;


             $us=$userlog->save();
            if($us)
              return response()->json(['status' => 'success','message'=>"Successfully added iban" ],200);    

          }

        }

         public function addcard(Request $request){

           $userlog = new Cards();

           $trans1 =DB::table('user_cards')->where('number', $request->number)->get();
           $trans2 =DB::table('user_cards')->where('cvv', $request->cvv)->get();
          
          if($request->number=='' || $request->name=='' || $request->cvv=='' || $request->month=='' || $request->year=='')
          {
               return response()->json(['status' => 'Failed','message' => 'Please provide all info'],200);
          }
          else if(count($trans1)!=0 || count($trans2)!=0)
          {
               return response()->json(['status' => 'Failed','message' => 'Same card cannot be added'],200);
          }
          else
          {
            //echo $request->number.'aaa'.$request->name.'bbb'.$request->userid;exit;
            $userlog->number = $request->number;
            $userlog->name = $request->name;
            $userlog->cvv = $request->cvv;

              $userlog->month = $request->month;
              $userlog->year = $request->year;

           /*   if($request->expirydate!='')
              $userlog->expirydate = $request->expirydate;*/

              $userlog->userid = $request->userid;

               $trans =DB::table('user_cards')->where('userid', $request->userid)->get();
              // print_r($trans);exit;
               
               /*foreach($trans as $key=>$val)
                  {
                    if($val->id!='')
                       echo $userlog->status = 0;
                     else
                       echo $userlog->status = 1;
                  }*/
              if(count($trans)!=0)
               $userlog->status = 0;
              else
              $userlog->status = 1;

             $us=$userlog->save();
            if($us)
   return response()->json(['status' => 'success','message'=>"Successfully added card" ],200);               


          }


         }

        public function sendmoneyqrscanner(Request $request){ 

        	 $customer = UserAccount::where('phone',$request->phone)->first();

        	 $to = UserAccount::where('qrcode',$request->qrcode)->first();

       

        /*
         
            if($customer->current_balance<$request->amount)
            {
               return response()->json(['status' => 'error','message'=>"Insufficient balance in your wallet." ],200);
            }
            else
            {
              if($request->amount==0 || $request->amount<0)
              {
                return response()->json(['status' => 'error','message'=>"Enter valid amount." ],200);
              }
              else
              {

              
              $tobalance=$request->amount+$to->current_balance;
              $frombalance=$customer->current_balance-$request->amount;

              $fromid=$customer['id'];
              $toid=$to['id'];
              $transid='TRX'.rand('00000','99999');
              $type='sendmoney';
              $sign='-';
              $amount=$request->amount;
              $trans_date=date("Y-m-d h:i:s");

              $sendertrans = new Transaction();

              $sendertrans->accfrom = $fromid;
              $sendertrans->accto = $toid;
              $sendertrans->transid = $transid;
              $sendertrans->type = $type;
              $sendertrans->amount = $amount;
              $sendertrans->sign = $sign;
              $sendertrans->trans_date = $trans_date;

              $sendertrans->message = $request->message;

              $sendertrans->save();

              $trx = Transaction::where('transid',$transid)->first();

                $customer_data = array(
                'current_balance'      =>  $frombalance

              );

    DB::table('user_profiles')->where('phone', $request->phone)->update($customer_data);

              $customer_data1 = array(
                'current_balance'      =>  $tobalance

              );

    DB::table('user_profiles')->where('id', $request->toid)->update($customer_data1);

              return response()->json(['status' => 'success','trxdate' => $trx['trans_date'],'trxid' => $trx['transid'],'message'=>$request->amount." has been transfered to ".$to['name']." wallet." ],200);

              }
            }
	*/

        return response()->json(['status' => 'success','id' => $to['id'],'name' => $to['name'],'image' => 'https://swiclo.eu/assets/images'.$to['photo'],'phone'=>$to['phone'] ],200);

      

        }


         public function checkwallet(Request $request)
         { 
                   $customer = UserAccount::where('phone',$request->phone)->first();


            $ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/customers/'.$customer['trustunique'].'/wallets');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 0);
//curl_setopt($ch, CURLOPT_POSTFIELDS,'order_id='.$request->order_id.'&wallet_id='.$customer['walletid'].'&type=WALLET');

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result1 = curl_exec($ch);

$res00=explode('{"data":[{"id":',$result1);

      $res20=explode(',',$res00[1]); 

      $aa00=explode('"balance":',$res20[3]);

        $balance=$aa00[1];

    if($balance<$request->amount)
            {
               return response()->json(['status' => 'error','message'=>"Requested amount is more than in your wallet." ],200);
            }
            else
            {
               return response()->json(['status' => 'success'],200);
            }

         }


           public function reload_payin(Request $request){ 

          $customer = UserAccount::where('phone',$request->phone)->first();

        //  $to = UserAccount::where('phone',$request->tophone)->first();


            $ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/customers/'.$customer['trustunique'].'/wallets');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 0);
//curl_setopt($ch, CURLOPT_POSTFIELDS,'order_id='.$request->order_id.'&wallet_id='.$customer['walletid'].'&type=WALLET');

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result1 = curl_exec($ch);

$res00=explode('{"data":[{"id":',$result1);

      $res20=explode(',',$res00[1]); 

      $aa00=explode('"balance":',$res20[3]);

        $balance=$aa00[1];

   /*     if($balance<$request->amount)
            {
               return response()->json(['status' => 'error','message'=>"Requested amount is more than in your wallet." ],200);
            }*/
            
          
         /*   if($customer->current_balance<$request->amount)
            {
               return response()->json(['status' => 'error','message'=>"Insufficient balance in your wallet." ],200);
            }
            else
            {*/
              
             /* if($request->amount==0 || $request->amount<0)
              {
                return response()->json(['status' => 'error','message'=>"Enter valid amount." ],200);
              }*/


              $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/orders');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS,'buyer_id='.$customer['trustid'].'&seller_id=864&name=Send Money&value='.$request->amount);

      $headers = array();
      $headers[] = 'Accept: application/json';
      $headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

      $result = curl_exec($ch);


          $res=explode('{"data":{"id":',$result);

        $res2=explode(',',$res[1]);

        $aa=explode('"',$res2[0]);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/payins');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,'order_id='.$aa[1].'&type=WALLET&wallet_id='.$customer['walletid']);

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        $res=explode('"direct_link":',$result);



        $res1=explode('","created_at"',$res[1]);



        $res0=explode('"',$res1[0]);



        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);


        $link=stripslashes($res0[1]);

        return $arr=array('status' => 'success','link'=>$link,'order_id'=>$aa[1],'phone'=>$request->phone,'amount'=>$request->amount);



            //}

      



        }

        
        public function sendmoney_payin(Request $request){ 

        	$customer = UserAccount::where('phone',$request->phone)->first();

          $to = UserAccount::where('phone',$request->tophone)->first();



          if($request->phone!=$request->tophone)
          {

          	$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/customers/'.$customer['trustunique'].'/wallets');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 0);
//curl_setopt($ch, CURLOPT_POSTFIELDS,'order_id='.$request->order_id.'&wallet_id='.$customer['walletid'].'&type=WALLET');

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result1 = curl_exec($ch);

$res00=explode('{"data":[{"id":',$result1);

			$res20=explode(',',$res00[1]); 

			$aa00=explode('"balance":',$res20[3]);

        $balance=$aa00[1];

        if($balance<$request->amount)
            {
               return response()->json(['status' => 'error','message'=>"Requested amount is more than in your wallet." ],200);
            }
            
          
         /*   if($customer->current_balance<$request->amount)
            {
               return response()->json(['status' => 'error','message'=>"Insufficient balance in your wallet." ],200);
            }
            else
            {*/
              
              if($request->amount==0 || $request->amount<0)
              {
                return response()->json(['status' => 'error','message'=>"Enter valid amount." ],200);
              }


              $ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/orders');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,'buyer_id='.$customer['trustid'].'&seller_id='.$to['trustid'].'&name=Send Money&value='.$request->amount);

			$headers = array();
			$headers[] = 'Accept: application/json';
			$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$result = curl_exec($ch);


			    $res=explode('{"data":{"id":',$result);

				$res2=explode(',',$res[1]);

				$aa=explode('"',$res2[0]);

				if (curl_errno($ch)) {
				    echo 'Error:' . curl_error($ch);
				}
				curl_close($ch);


				$ch = curl_init();

				curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/payins');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,'order_id='.$aa[1].'&type=WALLET&wallet_id='.$customer['walletid']);

				$headers = array();
				$headers[] = 'Accept: application/json';
				$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				$result = curl_exec($ch);

				$res=explode('"direct_link":',$result);



				$res1=explode('","created_at"',$res[1]);



				$res0=explode('"',$res1[0]);



				if (curl_errno($ch)) {
				    echo 'Error:' . curl_error($ch);
				}
				curl_close($ch);


				$link=stripslashes($res0[1]);

				return $arr=array('status' => 'success','link'=>$link,'order_id'=>$aa[1],'phone'=>$request->phone,'tophone'=>$request->tophone,'amount'=>$request->amount);



            //}

          }
          else
          {
          
            return response()->json(['status' => 'error','message'=>"Not supposed to send to your own account" ],200);
          
          }



        }


  public function reload_payout(Request $request)
  { 
     

          $customer = UserAccount::where('phone',$request->phone)->first();

         // $to = UserAccount::where('phone',$request->tophone)->first();


            $ch = curl_init();
 
curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/orders/'.$request->order_id.'/accept');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);


$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);




curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/orders/'.$request->order_id.'/validate');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);


$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);





curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/payouts');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,'order_id='.$request->order_id.'&wallet_id=1135&type=WALLET');

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);


  $transid='TRX'.rand('00000','99999');

        $trans_date=date("Y-m-d H:i:s");


        $amount=$request->amount;

         $sendertrans1 = new Transaction();

              $sendertrans1->mainacc = $customer['id'];
              //$sendertrans1->accto = $customer['id'];
              $sendertrans1->transid = $transid;
              $sendertrans1->type = 'recharge';
              $sendertrans1->amount = $amount;
              $sendertrans1->sign = '-';
              $sendertrans1->trans_date = $trans_date;

              $sendertrans1->rechargephone = $request->code.''.$request->rechargephone;

              $sendertrans1->save();



               require __DIR__ . '/pushnotification1.php';



if($customer['lang']=='english')
{
  $mtitle='Recharge';
 $msg=$request->amount. '€ Recharge done for '.$request->code.''.$request->rechargephone;
}
else
{
  $mtitle='Recargar';
  $msg=$request->amount. '€ Recarga realizada por '.$request->code.''.$request->rechargephone;
}

 if($customer['devicetoken']!='')
 {
     if($customer['deviceid']=='ios')
     {
        $result = $push->send(array('type'=>"withdrawmoney",'read_notify'=>1,'designid'=>0,'customer'=>0,'id'=>0,'mtitle'=>$mtitle,'mdesc'=>$msg,'buzzer'=>''),$customer['devicetoken']);
     }
     else
     {
        $result = $push->send1(array('type'=>"withdrawmoney",'read_notify'=>1,'designid'=>0,'customer'=>0,'id'=>0,'mtitle'=>$mtitle,'mdesc'=>$msg,'buzzer'=>''),$customer['devicetoken']);
     }
    
 }

              $noti = new Notifications();

              $noti->userid = $customer['id'];
           
              $noti->message = $msg;
              $noti->status = 0;
            

              $noti->save();



     
             /* $tobalance=$request->amount+$to->current_balance;
              $frombalance=$customer->current_balance-$request->amount;

              $fromid=$customer['id'];
              $toid=$to['id'];
              $transid='TRX'.rand('00000','99999');
              $type='sendmoney';
              $sign='-';
              $amount=$request->amount;
              $trans_date=date("Y-m-d H:i:s");

              $sendertrans = new Transaction();

              $sendertrans->accfrom = $fromid;
              $sendertrans->accto = $toid;
              $sendertrans->transid = $transid;
              $sendertrans->type = $type;
              $sendertrans->amount = $amount;
              $sendertrans->sign = $sign;
              $sendertrans->trans_date = $trans_date;

              $sendertrans->message = $request->message;

              $sendertrans->save();

              $trx = Transaction::where('transid',$transid)->first();

              if($request->reqid !='')
              {
                  $d = array(
                'paid'      =>  1

              );

    DB::table('transactions')->where('id', $request->reqid)->update($d);

              }

                $customer_data = array(
                'current_balance'      =>  $frombalance

              );

    DB::table('user_profiles')->where('phone', $request->phone)->update($customer_data);

             $customer_data1 = array(
                'current_balance'      =>  $tobalance

              );

    DB::table('user_profiles')->where('phone', $request->tophone)->update($customer_data1);

   $ex=explode(' ',$trx['trans_date']);

    $st=$ex[1];

    $ex1=explode(':',$st);

    if($ex1[0]>1 && $ex1[0]<12)
      $t='AM';
    else
      $t='PM';

    $time1=$ex1[0].':'.$ex1[1].' '.$t;
    $time2=date("d M Y",strtotime($trx['trans_date']));




     require __DIR__ . '/pushnotification1.php';

     $fromcustomer = UserAccount::where('id',$customer['id'])->first();

 if($to['lang']=='english')
 {
  $mtitle='Received Money';
 $msg='Received money from '.ucwords($fromcustomer->name);
}

else
{
  $mtitle='Dinero recibido';
  $msg='Recibió dinero de '.ucwords($fromcustomer->name);
}

 if($to['devicetoken']!='')
 {
      if($to['deviceid']=='ios')
     {
     $result = $push->send(array('type'=>"sendmoney",'read_notify'=>1,'designid'=>0,'customer'=>0,'id'=>0,'mtitle'=>$mtitle,'mdesc'=>$msg,'buzzer'=>''),$to['devicetoken']);
     }
     else
     {
        $result = $push->send1(array('type'=>"sendmoney",'read_notify'=>1,'designid'=>0,'customer'=>0,'id'=>0,'mtitle'=>$mtitle,'mdesc'=>$msg,'buzzer'=>''),$to['devicetoken']);
     }
 }

              $noti = new Notifications();

              $noti->userid = $to['id'];
           
              $noti->message = $msg;
              $noti->status = 0;
            

              $noti->save();*/




              return response()->json(['status' => 'success'],200);

           
       

  }


       public function sendmoney(Request $request){ 

          $customer = UserAccount::where('phone',$request->phone)->first();

          $to = UserAccount::where('phone',$request->tophone)->first();


           	$ch = curl_init();
 
curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/orders/'.$request->order_id.'/accept');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);


$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);




curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/orders/'.$request->order_id.'/validate');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);


$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);





curl_setopt($ch, CURLOPT_URL, 'https://api.truust.io/2.0/payouts');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,'order_id='.$request->order_id.'&wallet_id='.$to['walletid'].'&type=WALLET');

$headers = array();
$headers[] = 'Accept: application/json';
$headers[] = 'Authorization: Bearer sk_production_8O4x0hg0v7eODLqnAnFCUblN';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);



     
              $tobalance=$request->amount+$to->current_balance;
              $frombalance=$customer->current_balance-$request->amount;

              $fromid=$customer['id'];
              $toid=$to['id'];
              $transid='TRX'.rand('00000','99999');
              $type='sendmoney';
              $sign='-';
              $amount=$request->amount;
              $trans_date=date("Y-m-d H:i:s");

              $sendertrans = new Transaction();

              $sendertrans->accfrom = $fromid;
              $sendertrans->accto = $toid;
              $sendertrans->transid = $transid;
              $sendertrans->type = $type;
              $sendertrans->amount = $amount;
              $sendertrans->sign = $sign;
              $sendertrans->trans_date = $trans_date;

              $sendertrans->message = $request->message;

              $sendertrans->save();

              $trx = Transaction::where('transid',$transid)->first();

              if($request->reqid !='')
              {
                  $d = array(
                'paid'      =>  1

              );

    DB::table('transactions')->where('id', $request->reqid)->update($d);

              }

                $customer_data = array(
                'current_balance'      =>  $frombalance

              );

    DB::table('user_profiles')->where('phone', $request->phone)->update($customer_data);

              $customer_data1 = array(
                'current_balance'      =>  $tobalance

              );

    DB::table('user_profiles')->where('phone', $request->tophone)->update($customer_data1);

    $ex=explode(' ',$trx['trans_date']);

    $st=$ex[1];

    $ex1=explode(':',$st);

    if($ex1[0]>1 && $ex1[0]<12)
      $t='AM';
    else
      $t='PM';

    $time1=$ex1[0].':'.$ex1[1].' '.$t;
    $time2=date("d M Y",strtotime($trx['trans_date']));




     require __DIR__ . '/pushnotification1.php';

     $fromcustomer = UserAccount::where('id',$customer['id'])->first();

 if($to['lang']=='english')
 {
 	$mtitle='Received Money';
 $msg='Received money from '.ucwords($fromcustomer->name);
}

else
{
	$mtitle='Dinero recibido';
	$msg='Recibió dinero de '.ucwords($fromcustomer->name);
}

 if($to['devicetoken']!='')
 {
      if($to['deviceid']=='ios')
     {
     $result = $push->send(array('type'=>"sendmoney",'read_notify'=>1,'designid'=>0,'customer'=>0,'id'=>0,'mtitle'=>$mtitle,'mdesc'=>$msg,'buzzer'=>''),$to['devicetoken']);
     }
     else
     {
        $result = $push->send1(array('type'=>"sendmoney",'read_notify'=>1,'designid'=>0,'customer'=>0,'id'=>0,'mtitle'=>$mtitle,'mdesc'=>$msg,'buzzer'=>''),$to['devicetoken']);
     }
 }

              $noti = new Notifications();

              $noti->userid = $to['id'];
           
              $noti->message = $msg;
              $noti->status = 0;
            

              $noti->save();




              return response()->json(['status' => 'success','trxdate' => $time1.','.$time2,'trxid' => $trx['transid'],'message'=>$request->amount." has been transfered to ".ucwords($to['name'])." wallet." ],200);

           
       }
      
       public function phoneotp_register(Request $request){ 
           
              
         $customer = UserAccount::where('phone',$request->phone)->first();

         $customer1 = UserAccount::where('email',$request->email)->first();

        if($customer)
        {
            return response()->json(['status' => 'error','message'=>"Already registered phone number." ],200);
        }
          else if($customer1)
        {
            return response()->json(['status' => 'error','message'=>"Already registered email." ],200);
        }

        else
        {
             //$otp=rand('0000','9999');

               $i = 0; 
                $otp = ""; 
                while($i < 4){
                    
                    $otp .= mt_rand(0, 9);
                    $i++;
                }
       
             $phone = PhoneOtp::where('phone',$request->phone)->first();


             $phones = $request->phone;

          $data = ['phone' => '+34'.$phones, 'text' => 'OTP :'.$otp];

            $sid = 'AC51b09fcdafdd4dfb8c908ee9891d525e';
            $token = 'd754bbb142a2f3f71f10643edbae86c3';
            
            
            
            $client = new Client($sid, $token);
            
          
             $msg= $client->messages->create(
                
                $data['phone'],
                array(
                    
                    "from" => "Swiclo",
                   
                    'body' => $data['text']
                )
            );
             
             if($phone)
             {
                 
             
            
             
            
             
             
             	$customer_data = array(
			'phone'			 =>  $request->phone,
			'otp'			 =>  $otp

		);

		DB::table('phoneotp')->where('phone', $request->phone)->update($customer_data);
             
             }
             
             else
             {
                   $userlog1 = new PhoneOtp();
                
              $userlog1->phone=$request->phone;
               $userlog1->otp=$otp;
               
               $userlog1->save();
                 
             }
         
               
               return response()->json(['status' => 'success','otp' => $otp],200);
               
            
             
        }
             
             
       }
       
       
       
           public function resetpassword(Request $request)
           
           { 
           
              
                   //  $customer = UserAccount::where('phone',$request->phone)->first();
                    if($request->newpassword == $request->confirmpassword)
                    {
                           
                                	$customer_data = array(
                            			'password'			 =>  Hash::make($request->newpassword)
                            
                            		);

		                            DB::table('user_profiles')->where('phone', $request->phone)->update($customer_data);
		                            
		                             return response()->json(['status' => 'success','message' => 'Successfully updated password.'],200);
                           
                            
                          
                        
                    }
                    else
                    {
                          return response()->json(['status' => 'error','message' => 'Passwords should match.'],200);
                    }
        
        
           }




        public function gdpr(Request $request){ 

           $settings= DB::table('page_settings')->where('id',1)->get();

          $customer = UserAccount::where('id',$request->userid)->first();

          if($customer['lang']=='english')
          {
            $terms=$settings[0]->gdpr;
          }
          else
            $terms=$settings[0]->gdpr_spanish;

          return response()->json(['status' => 'success','data' => $terms],200);

        }
       
       
        public function termsandconditions(Request $request){ 

           $settings= DB::table('page_settings')->where('id',1)->get();

          $customer = UserAccount::where('id',$request->userid)->first();

          if($customer['lang']=='english')
          {
            $terms=$settings[0]->terms;
          }
          else
            $terms=$settings[0]->terms_spanish;

          return response()->json(['status' => 'success','data' => $terms],200);

        }


            public function complaints(Request $request){ 

              $settings= DB::table('page_settings')->where('id',1)->get();

              $customer = UserAccount::where('id',$request->userid)->first();

                if($customer['lang']=='english')
          {
            $complaints=$settings[0]->complaints;
          }
          else
            $complaints=$settings[0]->complaints_spanish;

          return response()->json(['status' => 'success','data' => $complaints],200);

        }

               public function fraud(Request $request){ echo 'raju';exit;

                 $settings= DB::table('page_settings')->where('id',1)->get();

                 $customer = UserAccount::where('id',$request->userid)->first();

           if($customer['lang']=='english')
          {
            $fraud=$settings[0]->fraud;
          }
          else
            $fraud=$settings[0]->fraud_spanish;
        

          return response()->json(['status' => 'success','data' => $fraud],200);

        }

               public function privacy(Request $request){ 

                $settings= DB::table('page_settings')->where('id',1)->get();

                  $customer = UserAccount::where('id',$request->userid)->first();

          if($customer['lang']=='english')
          {
            $privacy=$settings[0]->privacy;
          }
          else
            $privacy=$settings[0]->privacy_spanish;

         
        

          return response()->json(['status' => 'success','data' => $privacy],200);

        }

         public function phoneotp_forgot(Request $request){ 
           
              
         $customer = UserAccount::where('phone',$request->phone)->first();
        if($customer)
        {
            
               //$otp=rand('0000','9999');

                $i = 0; 
                $otp = ""; 
                while($i < 4){
                    
                    $otp .= mt_rand(0, 9);
                    $i++;
                }
       
             $phone = PhoneOtp::where('phone',$request->phone)->first();


               $phones = $request->phone;

          $data = ['phone' => '+34'.$phones, 'text' => 'OTP :'.$otp];

            $sid = 'AC51b09fcdafdd4dfb8c908ee9891d525e';
            $token = 'd754bbb142a2f3f71f10643edbae86c3';
            
            
            
            $client = new Client($sid, $token);
            
          
             $msg= $client->messages->create(
                
                $data['phone'],
                array(
                    
                    "from" => "Swiclo",
                   
                    'body' => $data['text']
                )
            );
             
             
             if($phone)
             {
                 
             
            
             
            
             
             
             	$customer_data = array(
			'phone'			 =>  $request->phone,
			'otp'			 =>  $otp

		);

		DB::table('phoneotp')->where('phone', $request->phone)->update($customer_data);
             
             }
             
             else
             {
                   $userlog1 = new PhoneOtp();
                
              $userlog1->phone=$request->phone;
               $userlog1->otp=$otp;
               
               $userlog1->save();
                 
             }
         
               
               return response()->json(['status' => 'success','otp' => $otp],200);
               
           
        }
        else
        {
          
                return response()->json(['status' => 'error','message'=>"Not a registered phone number." ],200);
            
             
        }
             
             
       }
      
       public function close_ticket(Request $request){ 

          $customer_data1 = array(
                'status'      =>  'closed'

              );

        DB::table('support_tickets')->where('id', $request->ticketid)->update($customer_data1);

        return response()->json(['status' => 'success','message'=>"Successfully closed the ticket." ],200);    

       }


           public function support(Request $request){ 

           $userlog = new Support();

           $userlog->userid = $request->userid;
            $userlog->subject = $request->reason;
            $userlog->message = $request->message;
            

             $userlog->save();

              $customer1 = UserAccount::where('id',$request->userid)->first();

            $email= $customer1->email;
            $subject= $request->reason;
            $message= $request->message;

            $data=array("email"=>$email,"subject"=>$subject);

              Mail::send('admin.welcomemail',array(
            'name' => $message),function($message)  use ($data) { $message->from($data['email'])->to('info@swiclo.com')->subject($data['subject']); });
               
               return response()->json(['status' => 'success','message' => 'Successfully send message'],200);

        }


         public function support_ticket(Request $request){ 

           $userlog = new SupportTicket();

            $userlog->trxid = $request->trxid;
            $userlog->userid = $request->userid;
            $userlog->subject = $request->reason;
            $userlog->description = $request->description;
            $userlog->status = 'open';

             $userlog->save();

             $customer1 = UserAccount::where('id',$request->userid)->first();

            $email= $customer1->email;

            $message=$customer1->name.'('.$customer1->phone.') raised the support ticket';


                Mail::send('admin.welcomemail',array(
            'name' => $message),function($message)  use ($email) { $message->from($email)->to('info@swiclo.com')->subject('Support Ticket'); });

               
               return response()->json(['status' => 'success','message' => 'Successfully raised ticket'],200);

        }

        public function getreasons(Request $request){ 

        	$customer1 = UserAccount::where('id',$request->userid)->first();


 
   $arr=array();

      $trans =DB::table('reasons')->get();
     
                  foreach($trans as $key=>$val)
                  {
                    
                  	 if($customer1->lang=='english')
                  	 	$reason=$val->reason;
                  	 else
                  	 	$reason=$val->reason_spanish;

                       array_push($arr,array('id'=>$val->id,'reason'=>$reason));
                    
                  }


          // $trans = Reasons::get();

        return response()->json(['status' => 'success','data' => $arr],200);

        }

        public function getnotificationstatus(Request $request){ 

           $customer1 = UserAccount::where('id',$request->userid)->first();

            return response()->json(['status' => 'success','notificationstatus'=>$customer1->notification],200);

        }

     public function orderhistory1(Request $request){ 

     	 $trans = Transaction::where('id',$request->id)->first();

     	 $customer1 = UserAccount::where('phone',$request->phone)->first();

       $arr=array();


       $raise =DB::table('support_tickets')->where('trxid',$request->id)->orderby('id', 'DESC')->get();
    
    $raisedstatus=0;

    foreach($raise as $key=>$val0)
     {
        if($val0->status=='open' || $val0->status=='processing')
          $raisedstatus=1;
        else 
           $raisedstatus=0;
         break;

     }
                    $tdate = date('Y-m-d H:i:s');

                    $date1 = strtotime($trans['trans_date']);  
                    $date2 = strtotime($tdate);

                    $diff = abs($date2 - $date1);

                    $years = floor($diff / (365*60*60*24));

                    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));

                    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

                    $hours = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60));

                    $minutes = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);

                    $seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));

                    $lastlogin ='';

                   if($days>0)
                    {
                      if($days>1)
                      {
                        if($customer1->lang=='english')
                         $lastlogin = $days.' Days';
                       else 
                         $lastlogin = $days.' Dias';

                      }
                      else
                      {
                        if($customer1->lang=='english')
                         $lastlogin = $days.' Day';
                        else
                           $lastlogin = $days.' Día';

                      }

                    }

                   else if($hours>0 && $days==0)
                    {
                       if($hours>1)
                       {
                          if($customer1->lang=='english')
                             $lastlogin= $hours.' Hrs';
                            else
                          $lastlogin= $hours.' Horas';
                       }
                       else
                       {
                        if($customer1->lang=='english')
                          $lastlogin= $hours.' Hr';
                         else
                        $lastlogin= $hours.' Hora';
                      }


                    }

                    else if($minutes>0 && $days==0 && $hours==0)
                    {
                      if($minutes>1)
                      {
                        if($customer1->lang=='english')
                        $lastlogin= $minutes.' Mins';
                        else
                       $lastlogin= $minutes.' Minutos';
                      }

                      else
                      {
                        if($customer1->lang=='english')
                        $lastlogin= $minutes.' Min';
                        else
                       $lastlogin= $minutes.' Min';
                      }

                    }

                  /*  if($seconds>0)
                    {
                    $lastlogin.= $seconds. 'Seconds';
                    }*/

                     $dd1=date("d",strtotime($trans['trans_date']));
                      $dd2=date("M",strtotime($trans['trans_date']));
                      $dd3=date("Y",strtotime($trans['trans_date']));

                      $dd=$dd1.','.$dd2.' '.$dd3;

                    if($trans['type']=='sendmoney')
                    {

                    

     	                if($trans['accfrom']['id'] == $customer1->id)
	                  	{

	                  		if($customer1->lang=='english')
	                  			$ms='Sent money to';
	                  		else
	                  			$ms='Enviado dinero a';
	                  		//echo $val->accfrom;exit;
	                  		$tocustomer = UserAccount::where('id',$trans['accto']['id'])->first();

                              if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo=$tocustomer->photo;

                         $now = time(); // or your date as well
                      $your_date = strtotime($trans['trans_date']);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                           $arr=array("id"=>$trans['id'],"days"=>$lastlogin,"status"=>'-',"image"=>$photo,"phone"=>$request->phone,"orderno"=>$trans['transid'],"text"=>$ms,"text1"=>ucwords($tocustomer->name),"amount"=>'- '.$trans['amount'],"date"=>$dd);
	                  	}
	                  	else if($trans['accto']['id'] == $customer1->id)
	                  	{

	                  			if($customer1->lang=='english')
	                  			$ms='Received money from';
	                  		else
	                  			$ms='Recibio dinero de';

	                  		$tocustomer = UserAccount::where('id',$trans['accfrom']['id'])->first();

                         if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo=$tocustomer->photo;

                            $now = time(); // or your date as well
                      $your_date = strtotime($trans['trans_date']);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                         $arr=array("id"=>$trans['id'],"days"=>$lastlogin,"status"=>'+',"orderno"=>$trans['transid'],"phone"=>$request->phone,"image"=>$photo,"orderno"=>$trans['transid'],"text"=>$ms,"text1"=>ucwords($tocustomer->name),"amount"=>'+ '.$trans['amount'],"date"=>$dd);
	                  	}

                    }

                    else if($trans['type']=='requestmoney')
                    { 

                    if($trans['paid']=='1')
                      $status=1;
                    else
                      $status=0;


                      if($trans['accfrom']['id'] == $customer1->id)
                      {

                      	if($customer1->lang=='english')
	                  			$ms='Request money to';
	                  		else
	                  			$ms='Solicitar dinero a';

                        //echo $val->accfrom;exit;
                        $tocustomer = UserAccount::where('id',$trans['accto']['id'])->first();

                              if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo=$tocustomer->photo;

                         $now = time(); // or your date as well
                      $your_date = strtotime($trans['trans_date']);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                           $arr=array("id"=>$trans['id'],"status"=>$status,"days"=>$lastlogin,"image"=>$photo,"orderno"=>$trans['transid'],"phone"=>$tocustomer->phone,"text"=>$ms,"text1"=>ucwords($tocustomer->name),"amount"=>$trans['amount'],"date"=>$dd);
                      }
                      else if($trans['accto']['id'] == $customer1->id)
                      {

                      	if($customer1->lang=='english')
	                  			$ms='Received request from';
	                  		else
	                  			$ms='Solicitud recibida de';

                        $tocustomer = UserAccount::where('id',$trans['accfrom']['id'])->first();

                         if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo=$tocustomer->photo;

                            $now = time(); // or your date as well
                      $your_date = strtotime($trans['trans_date']);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                         $arr=array("id"=>$trans['id'],"status"=>$status,"days"=>$lastlogin,"orderno"=>$trans['transid'],"image"=>$photo,"phone"=>$tocustomer->phone,"orderno"=>$trans['transid'],"text"=>$ms,"text1"=>ucwords($tocustomer->name),"amount"=>$trans['amount'],"date"=>$dd);
                      }

                    }


                    else if($trans['type']=='deposit')
                    {

                    	if($customer1->lang=='english')
	                  			$ms='Money deposited by';
	                  		else
	                  			$ms='Dinero depositado por';

                      if($trans['mainacc']['id'] == $customer1->id)
                      {
                        //echo $val->accfrom;exit;
                        $tocustomer = UserAccount::where('id',$trans['mainacc']['id'])->first();

                              if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo=$tocustomer->photo;

                         $now = time(); // or your date as well
                      $your_date = strtotime($trans['trans_date']);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                      $comm=$trans['amount']*(1.8/100);

                      $amt=$trans['amount']-$comm;

                           $arr=array("id"=>$trans['id'],"days"=>$lastlogin,"status"=>'',"image"=>$photo,"orderno"=>$trans['transid'],"phone"=>$request->phone,"text"=>$ms,"text1"=>'You',"amount"=>$amt,"fee"=>$comm,"date"=>$dd);
                      }
                    

                    }


                       else if($trans['type']=='withdraw')
                    {

                    	if($customer1->lang=='english')
	                  			$ms='Money withdrawn To Bank';
	                  		else
	                  			$ms='Dinero retirado al banco';

                      if($trans['mainacc']['id'] == $customer1->id)
                      {
                        //echo $val->accfrom;exit;
                        $tocustomer = UserAccount::where('id',$trans['mainacc']['id'])->first();

                              if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo=$tocustomer->photo;

                         $now = time(); // or your date as well
                      $your_date = strtotime($trans['trans_date']);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                           $arr=array("id"=>$trans['id'],"days"=>$lastlogin,"status"=>'',"image"=>$photo,"orderno"=>$trans['transid'],"phone"=>$request->phone,"text"=>$ms,"text1"=>'',"amount"=>'- '.$trans['amount'],"date"=>$dd);
                      }
                    

                    }

                        else if($trans['type']=='recharge')
                    {

                      if($customer1->lang=='english')
                          $ms='Recharge done for';
                        else
                          $ms='Recarga realizada por';

                      if($trans['mainacc']['id'] == $customer1->id)
                      {
                        //echo $val->accfrom;exit;
                        $tocustomer = UserAccount::where('id',$trans['mainacc']['id'])->first();

                              if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo=$tocustomer->photo;

                         $now = time(); // or your date as well
                      $your_date = strtotime($trans['trans_date']);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                      if($trans['rechargephone']==null)
                        $rc='';
                      else
                        $rc=$trans['rechargephone'];

                           $arr=array("id"=>$trans['id'],"days"=>$lastlogin,"status"=>'',"image"=>$photo,"orderno"=>$trans['transid'],"phone"=>$request->phone,"text"=>$ms,"text1"=>$rc,"amount"=>'- '.$trans['amount'],"date"=>$dd);
                      }
                    

                    }

     	
     	 return response()->json(['status' => 'success','data'=>$arr,'raisedstatus'=>$raisedstatus],200);
     }



  public function showticket(Request $request){

$arr=array();

 $trans =DB::table('ticket_replies')->where('ticket_id',$request->ticketid)->orderby('id', 'DESC')->get();

  foreach($trans as $key=>$val)
     { 

      array_push($arr,array("id"=>$val->id,"message"=>$val->message,"reply_by"=>$val->reply_by));


     }

       return response()->json(['status' => 'success','data' => $arr],200);

  }


  public function replyticket(Request $request){

  $userlog = new Ticketreplies();

  $userlog->userid=$request->userid;
   $userlog->ticket_id=$request->ticket_id;
    $userlog->reply_by='user';
     $userlog->message=$request->message;
     $userlog->reply_time=date("Y-m-d H:i:s");


      $userlog->save();


       $customer = SupportTicket::where('id',$request->ticket_id)->first();
       $customer->status='processing';
       $customer->save();

 return response()->json(['status' => 'success','message' =>'Successfully sent message'],200);

  }


  public function get_tickets(Request $request){

$arr=array();$arr1=array();

 $trans =DB::table('support_tickets')->where('userid',$request->userid)->orderby('id', 'DESC')->get();

    foreach($trans as $key=>$val1)
     { 
      $arr1=array();


      $customer1 = UserAccount::where('id',$request->userid)->first();

                $trans1 =DB::table('transactions')->where('id',$val1->trxid)->get();

                $trxamount=0;

                  foreach($trans1 as $key=>$val)
                  { 

                    $trxamount=$val->amount;

                    $trxid=$val->transid;

                    $customer1 = UserAccount::where('id',$request->userid)->first();
                   
                  

                    $tdate = date('Y-m-d H:i:s');

                    $date1 = strtotime($val->trans_date);  
                    $date2 = strtotime($tdate);

                    $diff = abs($date2 - $date1);

                    $years = floor($diff / (365*60*60*24));

                    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));

                    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

                    $hours = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60));

                    $minutes = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);

                    $seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));

                    $lastlogin ='';

                    if($days>0)
                    {
                      if($days>1)
                      $lastlogin = $days.' Days';
                      else
                      $lastlogin = $days.' Day';
                    }

                   else if($hours>0 && $days==0)
                    {
                       if($hours>1)
                       $lastlogin= $hours.' Hrs';
                       else
                       $lastlogin= $hours.' Hr';


                    }

                    else if($minutes>0 && $days==0 && $hours==0)
                    {
                      if($minutes>1)
                      $lastlogin= $minutes.' Mins';
                      else
                      $lastlogin= $minutes.' Min';
                    }

                  
                    if($val->type=='sendmoney')
                    {

                      if($val->accfrom == $customer1->id)
                      {
                        //echo $val->accfrom;exit;
                        $tocustomer = UserAccount::where('id',$val->accto)->first();

                        if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo='https://swiclo.eu/assets/images/'.$tocustomer->photo;

                      $now = time(); // or your date as well
                      $your_date = strtotime($val->trans_date);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                           array_push($arr1,array("id"=>$val->id,"status"=>'-',"days"=>$lastlogin,"image"=>$photo,"text"=>'Sent money to',"text1"=>ucwords($tocustomer->name),"amount"=>$val->amount,"date"=>date("Y-m-d",strtotime($val->trans_date))));
                      }

                      else if($val->accto == $customer1->id)
                      
                      {
                        $tocustomer = UserAccount::where('id',$val->accfrom)->first();

                         if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo='https://swiclo.eu/assets/images/'.$tocustomer->photo;

                          $now = time(); // or your date as well
                      $your_date = strtotime($val->trans_date);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                          array_push($arr1,array("id"=>$val->id,"status"=>'+',"days"=>$lastlogin,"image"=>$photo,"text"=>'Received money from',"text1"=>ucwords($tocustomer->name),"amount"=>$val->amount,"date"=>date("Y-m-d",strtotime($val->trans_date))));
                      }

                    }
                    else if($val->type=='requestmoney')
                    {

                      if($val->accfrom == $customer1->id)
                      {
                        //echo $val->accfrom;exit;
                        $tocustomer = UserAccount::where('id',$val->accto)->first();

                        if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo='https://swiclo.eu/assets/images/'.$tocustomer->photo;

                      $now = time(); // or your date as well
                      $your_date = strtotime($val->trans_date);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                           array_push($arr1,array("id"=>$val->id,"status"=>'-',"days"=>$lastlogin,"image"=>$photo,"text"=>'Request money from',"text1"=>ucwords($tocustomer->name),"amount"=>$val->amount,"date"=>date("Y-m-d",strtotime($val->trans_date))));
                      }

                      else if($val->accto == $customer1->id)
                      
                      {
                        $tocustomer = UserAccount::where('id',$val->accfrom)->first();

                         if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo='https://swiclo.eu/assets/images/'.$tocustomer->photo;

                          $now = time(); // or your date as well
                      $your_date = strtotime($val->trans_date);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                          array_push($arr1,array("id"=>$val->id,"status"=>'+',"days"=>$lastlogin,"image"=>$photo,"text"=>'Received request from',"text1"=>ucwords($tocustomer->name),"amount"=>$val->amount,"date"=>date("Y-m-d",strtotime($val->trans_date))));
                      }

                    }

                    else if($val->type=='deposit')
                    {

                      if($val->mainacc == $customer1->id)
                      {
                        
                        $tocustomer = UserAccount::where('id',$val->mainacc)->first();

                        if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo='https://swiclo.eu/assets/images/'.$tocustomer->photo;

                      $now = time(); // or your date as well
                      $your_date = strtotime($val->trans_date);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                           array_push($arr1,array("id"=>$val->id,"status"=>'+',"days"=>$lastlogin,"image"=>$photo,"text"=>'Money deposited by',"text1"=>'You',"amount"=>$val->amount,"date"=>date("Y-m-d",strtotime($val->trans_date))));
                      }

                   

                    }

                        else if($val->type=='withdraw')
                    {

                      if($val->mainacc == $customer1->id)
                      {
                        
                        $tocustomer = UserAccount::where('id',$val->mainacc)->first();

                        if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo='https://swiclo.eu/assets/images/'.$tocustomer->photo;

                      $now = time(); // or your date as well
                      $your_date = strtotime($val->trans_date);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                           array_push($arr1,array("id"=>$val->id,"status"=>'-',"days"=>$lastlogin,"image"=>$photo,"text"=>'Money withdrawn by',"text1"=>'You',"amount"=>$val->amount,"date"=>date("Y-m-d",strtotime($val->trans_date))));
                      }

                   

                    }



                  }


                  $sub='';$status='';

                   if($customer1->lang=='english')
                   {
                   	 if($val1->subject=='Transaccion tardia')
                   	 	$sub='Late Transaction';
                   	 else if($val1->subject=='La cantidad no se transfiere.')
                   	 	$sub='Amount is not Transferred.';
                   	 else if($val1->subject=='Late Transaction.')
                   	 	$sub='Late Transaction';
                   	 else if($val1->subject=='Amount is not Transferred.')
                   	 	$sub='Amount is not Transferred.';
                   }
                   else
                   {

                   	 if($val1->subject=='Transaccion tardia')
                   	 	$sub='Transaccion tardia';
                   	 else if($val1->subject=='La cantidad no se transfiere.')
                   	 	$sub='La cantidad no se transfiere.';
                   	 else if($val1->subject=='Late Transaction.')
                   	 	$sub='Transaccion tardia';
                   	 else if($val1->subject=='Amount is not Transferred.')
                   	 	$sub='La cantidad no se transfiere.';


                   }


                      if($customer1->lang=='english')
                   {
                   	 if($val1->status=='open')
                   	 	$status='open';
                   	 else if($val1->status=='closed')
                   	 	$status='closed';
                   	 else if($val1->status=='processing')
                   	 	$status='processing';
                   	 else if($val1->status=='abierta')
                   	 	$status='open';
                   	 else if($val1->status=='cerrada')
                   	 	$status='closed';
                   	 else if($val1->status=='Procesando')
                   	 	$status='processing';
                   }
                   else
                   {

                     if($val1->status=='open')
                   	 	$status='abierta';
                   	 else if($val1->status=='closed')
                   	 	$status='cerrada';
                   	 else if($val1->status=='processing')
                   	 	$status='Procesando';
                   	 else if($val1->status=='abierta')
                   	 	$status='abierta';
                   	 else if($val1->status=='cerrada')
                   	 	$status='cerrada';
                   	 else if($val1->status=='Procesando')
                   	 	$status='Procesando';
                   	 

                   }


      array_push($arr,array("id"=>$val1->id,"trxamount"=>$trxamount,"trxid"=>$trxid,"date"=>$val1->created_at,"transaction"=>$arr1,"subject"=>$sub,"status"=>$status));


     }

 if(!empty($arr))
return response()->json(['status' => 'success','data' => $arr],200);
else
  return response()->json(['status' => 'error','data' => $arr],200);


  }



		public function transactions(Request $request){

		          $arr=array();

                  //$trans = Transaction::where('type','sendmoney')->get();

               $trans =DB::table('transactions')->orderby('id', 'DESC')->get();

                
                  foreach($trans as $key=>$val)
                  {
                  	$dt=date("Y-m-d",strtotime($val->trans_date));

                    $tdate = date('Y-m-d H:i:s');

                    $date1 = strtotime($val->trans_date);  
                    $date2 = strtotime($tdate);

                    $diff = abs($date2 - $date1);

                    $years = floor($diff / (365*60*60*24));

                    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));

                    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

                    $hours = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60));

                    $minutes = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);

                    $seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));

                    $lastlogin ='';

                    $customer1 = UserAccount::where('phone',$request->phone)->first();

                                if($days>0)
                    {
                      if($days>1)
                      {
                        if($customer1->lang=='english')
                         $lastlogin = $days.' Days';
                       else 
                         $lastlogin = $days.' Dias';

                      }
                      else
                      {
                        if($customer1->lang=='english')
                         $lastlogin = $days.' Day';
                        else
                           $lastlogin = $days.' Día';

                      }

                    }

                   else if($hours>0 && $days==0)
                    {
                       if($hours>1)
                       {
                          if($customer1->lang=='english')
                             $lastlogin= $hours.' Hrs';
                            else
                          $lastlogin= $hours.' Horas';
                       }
                       else
                       {
                        if($customer1->lang=='english')
                          $lastlogin= $hours.' Hr';
                         else
                        $lastlogin= $hours.' Hora';
                      }


                    }

                    else if($minutes>0 && $days==0 && $hours==0)
                    {
                      if($minutes>1)
                      {
                        if($customer1->lang=='english')
                        $lastlogin= $minutes.' Mins';
                        else
                       $lastlogin= $minutes.' Minutos';
                      }

                      else
                      {
                        if($customer1->lang=='english')
                        $lastlogin= $minutes.' Min';
                        else
                       $lastlogin= $minutes.' Min';
                      }

                    }

                     $dd1=date("d",strtotime($val->trans_date));
                      $dd2=date("M",strtotime($val->trans_date));
                      $dd3=date("Y",strtotime($val->trans_date));

                      $dd=$dd1.', '.$dd2.' '.$dd3;

                   /* if($seconds>0)
                    {
                    $lastlogin.= $seconds. 'Seconds';
                    }*/


                 
                  	
                     if($val->type=='sendmoney')
                    {

	                  	if($val->accfrom == $customer1->id)
	                  	{
	                  		if($customer1->lang=='english')
	                  			$ms='Sent money to';
	                  		else
	                  			$ms='Enviado dinero a';


	                  		$tocustomer = UserAccount::where('id',$val->accto)->first();

                         if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo='https://swiclo.eu/assets/images/'.$tocustomer->photo;

                         $now = time(); // or your date as well
                      $your_date = strtotime($val->trans_date);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                     

		                  if($request->fromdate!='' && $request->todate!='')
		                  { 
	                  		if($request->fromdate<=$dt && $request->todate>=$dt)
	                  		{ 
                           array_push($arr,array("id"=>$val->id,"status"=>'-',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>ucwords($tocustomer->name),"amount"=>$val->amount,"date"=>$dd));
                            }
                          }

                          else if($request->searchtext!='')
                          {
                          
                          	if (strpos(strtolower($tocustomer->name), strtolower($request->searchtext)) !== false) 
                          	{
                          		array_push($arr,array("id"=>$val->id,"status"=>'-',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>ucwords($tocustomer->name),"amount"=>$val->amount,"date"=>$dd));
                          	}
                          }

                          else
                          {
                          	array_push($arr,array("id"=>$val->id,"status"=>'-',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>ucwords($tocustomer->name),"amount"=>$val->amount,"date"=>$dd));
                          }

	                  	}

	                  else if($val->accto == $customer1->id)
	                  {

	                  	if($customer1->lang=='english')
	                  			$ms='Received money from';
	                  		else
	                  			$ms='Recibio dinero de';

	                  		$tocustomer = UserAccount::where('id',$val->accfrom)->first();

                        if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo='https://swiclo.eu/assets/images/'.$tocustomer->photo;

	                  		 if($request->fromdate!='' && $request->todate!='')
		                  { 
		                  		if($request->fromdate<=$dt && $request->todate>=$dt)
		                  		{ 
	                          array_push($arr,array("id"=>$val->id,"status"=>'+',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>ucwords($tocustomer->name),"amount"=>$val->amount,"date"=>$dd));
	                            }
                          }

                           else if($request->searchtext!='')
                          {
                          	if (strpos(strtolower($tocustomer->name), strtolower($request->searchtext)) !== false) 
                          	{
                          		array_push($arr,array("id"=>$val->id,"status"=>'+',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>ucwords($tocustomer->name),"amount"=>$val->amount,"date"=>$dd));
                          	}
                          }

                          else
                          {
                          	array_push($arr,array("id"=>$val->id,"status"=>'+',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>ucwords($tocustomer->name),"amount"=>$val->amount,"date"=>$dd));
                          }
	                  }

                  }

                    else if($val->type=='requestmoney')
                    {

                    	if($customer1->lang=='english')
	                  			$ms='Request money to';
	                  		else
	                  			$ms='Solicitar dinero a';

                      if($val->accfrom == $customer1->id)
                      {
                        

                        $tocustomer = UserAccount::where('id',$val->accto)->first();

                         if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo='https://swiclo.eu/assets/images/'.$tocustomer->photo;

                         $now = time(); // or your date as well
                      $your_date = strtotime($val->trans_date);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));


                      if($request->fromdate!='' && $request->todate!='')
                      { 
                        if($request->fromdate<=$dt && $request->todate>=$dt)
                        { 
                           array_push($arr,array("id"=>$val->id,"status"=>'',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>ucwords($tocustomer->name),"amount"=>$val->amount,"date"=>$dd));
                            }
                          }

                          else if($request->searchtext!='')
                          {
                          
                            if (strpos(strtolower($tocustomer->name), strtolower($request->searchtext)) !== false) 
                            {
                              array_push($arr,array("id"=>$val->id,"status"=>'',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>ucwords($tocustomer->name),"amount"=>$val->amount,"date"=>$dd));
                            }
                          }

                          else
                          {
                            array_push($arr,array("id"=>$val->id,"status"=>'-',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>ucwords($tocustomer->name),"amount"=>$val->amount,"date"=>$dd));
                          }

                      }

                    else if($val->accto == $customer1->id)
                    {

                    	if($customer1->lang=='english')
	                  			$ms='Received request from';
	                  		else
	                  			$ms='Solicitud recibida de';

                        $tocustomer = UserAccount::where('id',$val->accfrom)->first();

                        if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo='https://swiclo.eu/assets/images/'.$tocustomer->photo;

                         if($request->fromdate!='' && $request->todate!='')
                      { 
                          if($request->fromdate<=$dt && $request->todate>=$dt)
                          { 
                            array_push($arr,array("id"=>$val->id,"status"=>'+',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>ucwords($tocustomer->name),"amount"=>$val->amount,"date"=>$dd));
                              }
                          }

                           else if($request->searchtext!='')
                          {
                            if (strpos(strtolower($tocustomer->name), strtolower($request->searchtext)) !== false) 
                            {
                              array_push($arr,array("id"=>$val->id,"status"=>'+',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>ucwords($tocustomer->name),"amount"=>$val->amount,"date"=>$dd));
                            }
                          }

                          else
                          {
                            array_push($arr,array("id"=>$val->id,"status"=>'+',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>ucwords($tocustomer->name),"amount"=>$val->amount,"date"=>$dd));
                          }
                    }


                    }

                     else if($val->type=='deposit')
                    {

                    	if($customer1->lang=='english')
	                  			$ms='Money deposited by';
	                  		else
	                  			$ms='Dinero depositado por';

                      if($val->mainacc == $customer1->id)
                      {
                        

                        $tocustomer = UserAccount::where('id',$val->mainacc)->first();

                         if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo='https://swiclo.eu/assets/images/'.$tocustomer->photo;

                         $now = time(); // or your date as well
                      $your_date = strtotime($val->trans_date);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));


                      if($request->fromdate!='' && $request->todate!='')
                      { 
                        if($request->fromdate<=$dt && $request->todate>=$dt)
                        { 
                           array_push($arr,array("id"=>$val->id,"status"=>'+',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>'You',"amount"=>$val->amount,"date"=>$dd));
                            }
                          }

                          else if($request->searchtext!='')
                          {
                          
                            if (strpos(strtolower($tocustomer->name), strtolower($request->searchtext)) !== false) 
                            {
                              array_push($arr,array("id"=>$val->id,"status"=>'+',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>'You',"amount"=>$val->amount,"date"=>$dd));
                            }
                          }

                          else
                          {
                            array_push($arr,array("id"=>$val->id,"status"=>'+',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>'You',"amount"=>$val->amount,"date"=>$dd));
                          }

                      }

                  

                    }

                     else if($val->type=='withdraw')
                    {

                    	if($customer1->lang=='english')
	                  			$ms='Money withdrawn To Bank';
	                  		else
	                  			$ms='Dinero retirado al banco';



                      if($val->mainacc == $customer1->id)
                      {
                        

                        $tocustomer = UserAccount::where('id',$val->mainacc)->first();

                         if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo='https://swiclo.eu/assets/images/'.$tocustomer->photo;

                         $now = time(); // or your date as well
                      $your_date = strtotime($val->trans_date);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));


                      if($request->fromdate!='' && $request->todate!='')
                      { 
                        if($request->fromdate<=$dt && $request->todate>=$dt)
                        { 
                           array_push($arr,array("id"=>$val->id,"status"=>'+',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>'',"amount"=>$val->amount,"date"=>$dd));
                            }
                          }

                          else if($request->searchtext!='')
                          {
                          
                            if (strpos(strtolower($tocustomer->name), strtolower($request->searchtext)) !== false) 
                            {
                              array_push($arr,array("id"=>$val->id,"status"=>'+',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>'',"amount"=>$val->amount,"date"=>$dd));
                            }
                          }

                          else
                          {
                            array_push($arr,array("id"=>$val->id,"status"=>'+',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>'',"amount"=>$val->amount,"date"=>$dd));
                          }

                      }



                    }

                      else if($val->type=='recharge')
                    {

                      if($customer1->lang=='english')
                          $ms='Recharge done for';
                        else
                          $ms='Recarga realizada por';



                      if($val->mainacc == $customer1->id)
                      {
                        

                        $tocustomer = UserAccount::where('id',$val->mainacc)->first();

                         if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo='https://swiclo.eu/assets/images/'.$tocustomer->photo;

                         $now = time(); // or your date as well
                      $your_date = strtotime($val->trans_date);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                      if($val->rechargephone==null)
                        $rc='';
                      else
                        $rc=$val->rechargephone;

                      if($request->fromdate!='' && $request->todate!='')
                      { 
                        if($request->fromdate<=$dt && $request->todate>=$dt)
                        { 
                           array_push($arr,array("id"=>$val->id,"status"=>'-',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>$rc,"amount"=>$val->amount,"date"=>$dd));
                            }
                          }

                          else if($request->searchtext!='')
                          {
                          
                            if (strpos(strtolower($tocustomer->name), strtolower($request->searchtext)) !== false) 
                            {
                              array_push($arr,array("id"=>$val->id,"status"=>'-',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>$rc,"amount"=>$val->amount,"date"=>$dd));
                            }
                          }

                          else
                          {
                            array_push($arr,array("id"=>$val->id,"status"=>'-',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>$rc,"amount"=>$val->amount,"date"=>$dd));
                          }

                      }



                    }


                  }
                  //print_r($arr);exit;
                if(!empty($arr)) 
                {
                 return response()->json(['status' => 'success','transactions' => $arr],200); 
                }
                else
                {
                  return response()->json(['status' => 'error','transactions' => $arr,'message' => "No Transactions Found"],200);
                }


		}

    public function searchphone(Request $request){ 
      
      $arr=array();

      $trans =DB::table('user_profiles')->get();
     
                  foreach($trans as $key=>$val)
                  {

                    if($val->id!=$request->userid)
                    {

                    
                        if(strpos($val->phone, $request->phone) !== false)
                        {
                          if($val->photo==null)
                            $image='';
                          else
                            $image=$val->photo;

                           array_push($arr,array('id'=>$val->id,'name'=>$val->name,'phone'=>$val->phone,'image'=>$image));
                        }

                    }

                  }

                  return $arr;

    }


     public function getphones(Request $request){ 

     	  $users = DB::table('user_profiles')->get();
     	  $arr=array();
          
       

                  foreach($users as $key=>$val)
                  {

                  	   if($val->qrcode==null)
          	$qrcode='';
          else
          	$qrcode=$val->qrcode;
          
                  	if($request->userid!=$val->id)
                    {
                      if($request->phone!=$val->phone)
                  	   array_push($arr,array("id"=>$val->id,"phone"=>$val->phone,"qrcode"=>$qrcode));
                    }


                  }

          return response()->json(['status' => 'success','data' => $arr],200);

     }

     public function gethome(Request $request){ 

      $customer = UserAccount::where('phone',$request->phone)->first();

          $arr=array();
          $i=1;
                  //$trans = Transaction::where('type','sendmoney')->where('type','requestmoney')->where('type','deposit')->where('type','withdraw')->orderby('id', 'DESC')->get();

                   $trans =DB::table('transactions')->orderby('id', 'DESC')->get();

                 

                  foreach($trans as $key=>$val)
                  { 

                      $dd1=date("d",strtotime($val->trans_date));
                      $dd2=date("M",strtotime($val->trans_date));
                      $dd3=date("Y",strtotime($val->trans_date));

                      $dd=$dd1.', '.$dd2.' '.$dd3;


                     $customer1 = UserAccount::where('phone',$request->phone)->first();
                   
                  

                    $tdate = date('Y-m-d H:i:s');

                    $date1 = strtotime($val->trans_date);  
                    $date2 = strtotime($tdate);

                    $diff = abs($date2 - $date1);

                    $years = floor($diff / (365*60*60*24));

                    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));

                    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

                    $hours = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60));

                    $minutes = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);

                    $seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));

                    $lastlogin ='';



                    if($days>0)
                    {
                      if($days>1)
                      {
                      	if($customer1->lang=='english')
                         $lastlogin = $days.' Days';
                       else 
                       	 $lastlogin = $days.' Dias';

                      }
                      else
                      {
                      	if($customer1->lang=='english')
                         $lastlogin = $days.' Day';
                        else
                        	 $lastlogin = $days.' Día';

                      }

                    }

                   else if($hours>0 && $days==0)
                    {
                       if($hours>1)
                       {
                       		if($customer1->lang=='english')
                             $lastlogin= $hours.' Hrs';
                            else
                         	$lastlogin= $hours.' Horas';
                       }
                       else
                       {
                       	if($customer1->lang=='english')
                          $lastlogin= $hours.' Hr';
                         else
                      	$lastlogin= $hours.' Hora';
                      }


                    }

                    else if($minutes>0 && $days==0 && $hours==0)
                    {
                      if($minutes>1)
                      {
                      	if($customer1->lang=='english')
                        $lastlogin= $minutes.' Mins';
                        else
                    	 $lastlogin= $minutes.' Minutos';
                      }

                      else
                      {
                      	if($customer1->lang=='english')
                        $lastlogin= $minutes.' Min';
                        else
                       $lastlogin= $minutes.' Min';
                      }

                    }

                  
                    if($val->type=='sendmoney')
                    {

                      if($val->accfrom == $customer1->id)
                      {

                      		if($customer1->lang=='english')
	                  			$ms='Sent money to';
	                  		else
	                  			$ms='Enviado dinero a';


                        //echo $val->accfrom;exit;
                        $tocustomer = UserAccount::where('id',$val->accto)->first();

                        if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo='https://swiclo.eu/assets/images/'.$tocustomer->photo;

                      $now = time(); // or your date as well
                      $your_date = strtotime($val->trans_date);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                           array_push($arr,array("id"=>$val->id,"status"=>'-',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>ucwords($tocustomer->name),"amount"=>$val->amount,"date"=>$dd));
                      }

                      else if($val->accto == $customer1->id)
                      
                      {

                      		if($customer1->lang=='english')
	                  			$ms='Received money from';
	                  		else
	                  			$ms='Recibio dinero de';

                        $tocustomer = UserAccount::where('id',$val->accfrom)->first();

                         if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo='https://swiclo.eu/assets/images/'.$tocustomer->photo;

                          $now = time(); // or your date as well
                      $your_date = strtotime($val->trans_date);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                          array_push($arr,array("id"=>$val->id,"status"=>'+',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>ucwords($tocustomer->name),"amount"=>$val->amount,"date"=>$dd));
                      }

                    }
                    else if($val->type=='requestmoney')
                    {

                    	if($customer1->lang=='english')
	                  			$ms='Request money to';
	                  		else
	                  			$ms='Solicitar dinero a';

                      if($val->accfrom == $customer1->id)
                      {
                        //echo $val->accfrom;exit;
                        $tocustomer = UserAccount::where('id',$val->accto)->first();//print_r($tocustomer);echo $tocustomer->photo;exit;

                        if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo='https://swiclo.eu/assets/images/'.$tocustomer->photo;

                      $now = time(); // or your date as well
                      $your_date = strtotime($val->trans_date);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                           array_push($arr,array("id"=>$val->id,"status"=>'-',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>ucwords($tocustomer->name),"amount"=>$val->amount,"date"=>$dd));
                      }

                      else if($val->accto == $customer1->id)
                      
                      {

                      	if($customer1->lang=='english')
	                  			$ms='Received request from';
	                  		else
	                  			$ms='Solicitud recibida de';

                        $tocustomer = UserAccount::where('id',$val->accfrom)->first();

                         if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo='https://swiclo.eu/assets/images/'.$tocustomer->photo;

                          $now = time(); // or your date as well
                      $your_date = strtotime($val->trans_date);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                          array_push($arr,array("id"=>$val->id,"status"=>'+',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>ucwords($tocustomer->name),"amount"=>$val->amount,"date"=>$dd));
                      }

                    }

                    else if($val->type=='deposit')
                    {
                    		if($customer1->lang=='english')
	                  			$ms='Money deposited by';
	                  		else
	                  			$ms='Dinero depositado por';

                      if($val->mainacc == $customer1->id)
                      {
                        
                        $tocustomer = UserAccount::where('id',$val->mainacc)->first();

                        if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo='https://swiclo.eu/assets/images/'.$tocustomer->photo;

                      $now = time(); // or your date as well
                      $your_date = strtotime($val->trans_date);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                           array_push($arr,array("id"=>$val->id,"status"=>'+',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>'You',"amount"=>$val->amount,"date"=>$dd));
                      }

                   

                    }

                        else if($val->type=='withdraw')
                    {

                    	if($customer1->lang=='english')
	                  			$ms='Money withdrawn To Bank';
	                  		else
	                  			$ms='Dinero retirado al banco';


                      if($val->mainacc == $customer1->id)
                      {
                        
                        $tocustomer = UserAccount::where('id',$val->mainacc)->first();

                        if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo='https://swiclo.eu/assets/images/'.$tocustomer->photo;

                      $now = time(); // or your date as well
                      $your_date = strtotime($val->trans_date);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));

                           array_push($arr,array("id"=>$val->id,"status"=>'-',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>'',"amount"=>$val->amount,"date"=>$dd));
                      }

                   

                    }

                        else if($val->type=='recharge')
                    {

                      if($customer1->lang=='english')
                          $ms='Recharge done for';
                        else
                          $ms='Recarga realizada por';


                      if($val->mainacc == $customer1->id)
                      {
                        
                        $tocustomer = UserAccount::where('id',$val->mainacc)->first();

                        if($tocustomer->photo==null)
                          $photo='';
                        else
                           $photo='https://swiclo.eu/assets/images/'.$tocustomer->photo;

                      $now = time(); // or your date as well
                      $your_date = strtotime($val->trans_date);
                      $datediff = $now - $your_date;

                      $days=round($datediff / (60 * 60 * 24));


                      if($val->rechargephone==null)
                        $rc='';
                      else
                        $rc=$val->rechargephone;

                           array_push($arr,array("id"=>$val->id,"status"=>'-',"days"=>$lastlogin,"image"=>$photo,"text"=>$ms,"text1"=>$rc,"amount"=>$val->amount,"date"=>$dd));
                      }

                   

                    }


                    if(count($arr)==11)
                      break;
                     // $i++;
                  }
                  
            if(!empty($arr))
            return response()->json(['status' => 'success','transactions' => $arr,'balance'=>$customer['current_balance']],200);
           else
            return response()->json(['status' => 'error','transactions' => $arr,'balance'=>$customer['current_balance']],200);


     }

     public function withdrawbalance(Request $request){ 

    $customer = UserAccount::where('id',$request->userid)->first();


           $trans =DB::table('transactions')->orderby('id', 'DESC')->get();

           $startdate=date("Y-m-d",strtotime($customer['startdate']));
           $enddate=date("Y-m-d",strtotime($customer['enddate']));

           $amount=0;
                
                  foreach($trans as $key=>$val)
                  {
                    $date=date("Y-m-d",strtotime($val->trans_date));

                      if($date>=$startdate && $date<=$enddate && $val->type=='withdraw' && $val->mainacc==$request->userid)
                      {
                          $amount+=$val->amount;
                      }


                  }

                  if(($customer['verifystatus']==0 && $customer['pending']==0 && $customer['accept']==0) || ($customer['verifystatus']==1 && $customer['pending']==1 && $customer['accept']==0) ) 
                 {
                    $limit=250;

                    $bal=$limit-$amount;

                  }
                  else if(($customer['verifystatus']==1 && $customer['pending']==0 && $customer['accept']==1) || ($customer['verifystatus']==2 && $customer['pending']==1 && $customer['accept']==0) )
                 {
                    $limit=1999;

                    $bal=$limit-$amount;

                  }
                  else
                  {
                      $bal='no limit';
                  }

                  if($customer['pending']==1 && $customer['accept']==0)
                  {
                    $stat='Pending';
                  }
                  else if($customer['pending']==0 && $customer['accept']==1)
                  {
                    $stat='Accepted';
                  }
                  else
                  {
                    $stat='';
                  }


                   return response()->json(['status' => 'success','balance'=>$bal,'verificationstatus'=>$customer['verifystatus'],'pendingstatus'=>$stat ],200);

     }

    public function apiLogin(Request $request){ 
        
        
        
        
         $customer = UserAccount::where('phone',$request->phone)->first();
        if($customer)
        {

        	if($customer['status']==9)
            {
              if($request->lang=='english')
               return response()->json(['status' => 'error','message' => "Account closed by you."],200);	
             else
               return response()->json(['status' => 'error','message' => "Cuenta Cerrado"],200);  
            }
            else
            {


            if(password_verify($request->password, $customer->password))
            {

                    $customer_data = array(
      'deviceid'      =>  $request->deviceid,
       'lang'      =>  $request->lang,
      'devicetoken'      =>   $request->devicetoken

    );

                    if($customer['status']==0)
                    {
                       return response()->json(['status' => 'error','message' => "Account Banned by admin."],200);
                    }

                    else
                    {
                        DB::table('user_profiles')->where('phone', $request->phone)->update($customer_data);

    		
    			 $trans =DB::table('transactions')->orderby('id', 'DESC')->get();

    			 $startdate=date("Y-m-d",strtotime($customer['startdate']));
    			 $enddate=date("Y-m-d",strtotime($customer['enddate']));

    			 $amount=0;
                
                  foreach($trans as $key=>$val)
                  {
                  	$date=date("Y-m-d",strtotime($val->trans_date));

                  		if($date>=$startdate && $date<=$enddate && $val->type=='withdraw' && $val->mainacc==$customer['id'])
                  		{
                  			 $amount+=$val->amount;
                  		}


                  }

                  if($customer['verifystatus']==0)
                 {
                    $limit=250;

                    $bal=$limit-$amount;

                  }
                  else   if($customer['verifystatus']==1)
                 {
                    $limit=1999;

                    $bal=$limit-$amount;

                  }
                  else
                  {
                      $bal='no limit';
                  }
                
                 
            return response()->json(['status' => 'success','withdrawbalance'=>$bal,'message' => "Login Success",'id'=>$customer['id'],'trustid' => $customer['trustid'],'walletid'=>$customer['trustunique'],'qrcode'=>$customer['qrcode'],'lang'=>$request->lang,'name'=>$customer['name'],'phone'=>$customer['phone'],'balance'=>'$ '.$customer['current_balance']],200);
                    }

  
            }
         

            else 
            {
               if($request->lang=='english')
            return response()->json(['status' => 'error','message'=>"Incorrect Credentials" ],200);
          else
             return response()->json(['status' => 'error','message'=>"credenciales incorrectas" ],200);
           
            }

          }

        }

        else 
        {

        return response()->json(['status' => 'error','message'=>"Not a register member" ],200);
        
        }
        
        
        
        

       /* if (Auth::guard('profile')->attempt(['email' => $request->email,'password' => $request->password], false)){

            $ip = $_SERVER['REMOTE_ADDR'];
            $browser = $_SERVER['HTTP_USER_AGENT'];

            $userlog = new UserLogins();
            $userlog->userid = Auth::guard('profile')->user()->id;
            $userlog->ip = $ip;
            $userlog->agent = $browser;
            $userlog->login_time = date('Y-m-d H:i:s');
            $userlog->save();

            if (Auth::guard('profile')->user()->status == 0) {
                Auth::guard('profile')->logout();

                return response()->json(['status' => 'Failed','message' => 'Your Account is Suspended'],200);
            }

            return response()->json(['status' => 'Success','message' => 'Login Successful','data' =>'<form id="payNow" method="POST" action="'.route('api.payment').'">
                            '.csrf_field().'
                            <p id="resp"></p>
                            <div class="paypal-pay-area">
                                <h3>Hi, '.Auth::guard('profile')->user()->name.'</h3>
                                <div class="paypal-ship-area">
                                    <h3>You paying '.session('amount').' to '.session('payee').' account.</h3>
                                </div>
                            </div>
                            <div class="form-group">
                                    <button type="submit" class="btn btn-block paypal_btn" id="ConfirmButton"><strong>Confirm Payment</strong></button>
                                
                            </div>
                        </form>'], 200);

        }

        return response()->json(['status' => 'Failed','message' => 'Incorrect Username Or Password'],200);*/
        
    }


    public function eXpressApiForm(){
        $token = Input::get('token');
        $value = session('token');
        if ($token == $value){
            return view('express-checkout-api');
        }else{
            return view('express-checkout-api-error');
        }
    }


    public function TransactionVerifyError(Request $request){

        return response()->json([
            'details' =>[],
            'success' => false,
            'error' => true,
            'message' => 'Post Method is not Allowed'
        ],200);

    }

    public function TransactionVerify(){
    
        $apikey = Input::get('api_key');
        $txn = Input::get('txn');
        $apiowner = UserAccount::where('api_key',$apikey)->first();
        if (UserAccount::where('api_key','=',$apikey)->exists()){

            if (Transaction::where('transid',$txn)->where('mainacc',$apiowner->id)->count() > 0){
                $trans = Transaction::where('transid',$txn)->where('mainacc',$apiowner->id)->first();
                $status = 'pending';
                $info = array();
                if ($trans->status == 1){
                    $status = 'completed';
                }

                if ($trans->type == "credit"){
                    $info = [
                        'credit_from' => $trans->accfrom->email,
                        'debit_to' => null,
                        'withdraw_method' => null,
                        'deposit_method' => null
                    ];
                }

                if ($trans->type == "debit"){
                    $info = [
                        'credit_from' => null,
                        'debit_to' => $trans->accto->email,
                        'withdraw_method' => null,
                        'deposit_method' => null
                        ];
                }

                if ($trans->type == "withdraw"){
                    $info = [
                        'credit_from' => null,
                        'debit_to' => null,
                        'withdraw_method' => $trans->withdrawid->method,
                        'deposit_method' => null
                        ];
                }

                if ($trans->type == "deposit"){
                    $info = [
                        'credit_from' => null,
                        'debit_to' => null,
                        'withdraw_method' => null,
                        'deposit_method' => $trans->deposit_method
                    ];
                }

                return response()->json([
                    'details' =>[
                        'txn_id' => $trans->transid,
                        'txn_reason' => $trans->reason,
                        'txn_amount' => $trans->amount,
                        'txn_fee' => $trans->fee,
                        'txn_type' => $trans->type,
                        'txn_reference' => $trans->reference,
                        'txn_status' => $status,
                        'txn_date' => $trans->trans_date,
                        'txn_info' => $info
                    ],
                    'success' => true,
                    'error' => false,
                    'message' => 'Transaction Verified'
                ],200);
            }

            return response()->json([
                'details' =>[],
                'success' => false,
                'error' => true,
                'message' => 'No Transaction Found.'
            ],200);

        }else{

            return response()->json([
                'details' =>[],
                'success' => false,
                'error' => true,
                'message' => 'Invalid API KEY'
            ],200);

        }

    }

    public function CompletePaymentApi(Request $request){

        if (Auth::guard('profile')->check()) {
            $payee = session('payee');
            $pay_amount = session('amount');
            $item_code = session('item_code');
            $item_name = session('item_name');
            $custom = session('custom');
            $origin = session('origin');

            $from = UserAccount::findOrFail(Auth::guard('profile')->user()->id);
            $toacc = UserAccount::where('email', $payee)->first();

            $transcharge = Settings::findOrFail(1);
            $charge = $transcharge->transfer_charge;
            $extra = $transcharge->extra_charge;
            $charge_total = ($extra / 100) * $pay_amount + $charge;
            $charge_final = number_format((float)$charge_total, 2, '.', '');

            $amount = $pay_amount + $charge_total;
            $amount = number_format((float)$amount, 2, '.', '');


            if (Auth::guard('profile')->user()->current_balance >= $amount){


                if($payee == $from->email){

                    return response()->json([
                            'status' => 'failed',
                            'amount' => $amount,
                            'message' => 'Sorry, You can not pay on your own.'
                        ]
                        ,200);
                }



                $transid = strtoupper($this->goRandomString(4) . str_random(3) . $this->goRandomString(4));

                $redirect_url = session('success_return')."?amount=".$pay_amount."&fee=".$charge_final."&txn=".$transid."&paid_by=".$from->email."&custom=".$custom;

                $sendertrans = new Transaction();
                $sendertrans['transid'] = $transid;
                $sendertrans['mainacc'] = $from->id;
                $sendertrans['accto'] = $toacc->id;
                $sendertrans['accfrom'] = $from->id;
                $sendertrans['type'] = "debit";
                $sendertrans['sign'] = "-";
                $sendertrans['reason'] = "Payment Sent";
                $sendertrans['amount'] = $pay_amount;
                $sendertrans['item_name'] = $item_name;
                $sendertrans['item_code'] = $item_code;
                $sendertrans['origin'] = $origin;
                $sendertrans['used_api'] = 'yes';
                $sendertrans['fee'] = $charge_final;
                $sendertrans['reference'] = $request->reference;
                $sendertrans['trans_date'] = date('Y-m-d H:i:s');
                $sendertrans['status'] = 1;
                $sendertrans->save();


                $receivertrans = new Transaction();
                $receivertrans['transid'] = $transid;
                $receivertrans['mainacc'] = $toacc->id;
                $receivertrans['accto'] = $toacc->id;
                $receivertrans['accfrom'] = $from->id;
                $receivertrans['type'] = "credit";
                $receivertrans['sign'] = "+";
                $receivertrans['reason'] = "Payment Received";
                $receivertrans['amount'] = $pay_amount;
                $receivertrans['item_name'] = $item_name;
                $receivertrans['item_code'] = $item_code;
                $receivertrans['origin'] = $origin;
                $receivertrans['used_api'] = 'yes';
                $receivertrans['fee'] = "0";
                $receivertrans['reference'] = $request->reference;
                $receivertrans['trans_date'] = date('Y-m-d H:i:s');
                $receivertrans['status'] = 1;
                $receivertrans->save();

                $balance1['current_balance'] = $from->current_balance - $amount;
                $from->update($balance1);

                $balance2['current_balance'] = $toacc->current_balance + $pay_amount;
                $toacc->update($balance2);

                //return redirect()->back()->with('message', 'Amount Sent Successfully.');


                $url = session('notify_url');
                $myvars = "payment_status=Completed&amount=".$pay_amount."&fee=".$charge_final."&txn=".$transid."&paid_by=".$from->email."&receiver=".$payee."&item_code=".$item_code."&item_name=".$item_name."&custom=".$custom;


                $ch = curl_init($url);
//                    if ($ch == FALSE) {
//                        return "URL PROBLEM....";
//                    }
                curl_setopt( $ch, CURLOPT_POST, 1);
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
                curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt( $ch, CURLOPT_HEADER, 0);
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSLVERSION, 6);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close', 'User-Agent: '.$transcharge->title));

                curl_exec( $ch );




                session()->forget('token');

                return response()->json([
                        'status' => 'completed',
                        'amount' => $amount,
                        'item_code' => $item_code,
                        'item_name' => $item_name,
                        'redirect_url' => $redirect_url,
                        'data' => '<div class="paypal-pay-area">
                                            <h3>Hi, '.$from->name.'</h3>
                                            <div class="paypal-ship-area">
                                                <h3>Congratulation.</h3>
                                                <h3>Your Payment Completed Successfully.</h3>
                                                    <p>Please wait for redirect or Click on Back Button.</p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                                <a href="'.$redirect_url.'" class="btn btn-block paypal_btn"><strong>Go Back</strong></a>
                                        </div>',
                        'message' => 'Payment Completed Successfully.',
                    ]
                    ,200);
            }else{
                return response()->json([
                        'status' => 'failed',
                        'amount' => $amount,
                        'message' => 'Insufficient Account Balance.'
                    ]
                    ,200);
            }


        }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'Please Login First.'
                ]
                ,200);
        }
    }

    public function eXpressApi(Request $request){

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With, Application');

        if ($request->method() == "GET"){

            if (session('token') != null){
                $token = Input::get('token');
                $value = session('token');
                if ($token == $value){
                    if (Auth::guard('profile')->check()){
                        return redirect('/express/web/pay?token='.$token);
                    }else{
                        return redirect('/express/web/signin?token='.$token);
                    }
                }else{
                    return view('express-checkout-api-error');
                }
            }else{
            	return view('express-checkout-api-error');
            
            }

        }

        if ($request->cmd == '_verify'){
        
            $cmd_verify = Transaction::where('transid',$request->txn);

            if ($cmd_verify->count() > 0) {
		if($cmd_verify->first()->amount == $request->amount){
                return "VERIFIED";
                }
                return "UNVERIFIED";
            }else{

            	return "UNVERIFIED";
            }

        }else {
            
            $post_from = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            $cancel_url = $request->cancel_return;
            $payee = $request->business_account;
            $business_count = UserAccount::where('email',$payee)->where('acctype','business')->count();
            $token = str_random(12).uniqid();
            if ($business_count > 0){
            	if($request->amount > 0){
	            	session([
	                    'token' => $token,
	                    'item_name' => $request->item_name,
                        'item_code' => $request->item_code,
                        'amount' => round($request->amount,2),
	                    'custom' => $request->custom,
	                    'success_return' => $request->success_return,
	                    'notify_url' => $request->notify_url,
	                    'cancel_return' => $cancel_url,
	                    'payee' => $payee,
	                    'origin' => $post_from,
	                    'message' => ''
	                ]);
	
	                if (Auth::guard('profile')->check()){
	                    return redirect('/express/web/pay?token='.$token);
	                }else{
	                    return redirect('/express/web/signin?token='.$token);
	                }
            	}
              	session([
	                'message' => "Amount is not valid.",
	                'cancel_return' => $cancel_url
	            ]);
	            return redirect('/express/web/pay?token='.$token);  
            }
            session([
                'message' => "No Business Account Found.",
                'cancel_return' => $cancel_url
            ]);
            return redirect('/express/web/pay?token='.$token);
        }
    }

}
