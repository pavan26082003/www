<?php
class Sms_model extends CI_Model{
    
    public function send_sms($no, $message){
        $msg=str_replace(" ","%20",$message);
       // $smsurl='http://164.52.195.161/API/SendMsg.aspx?uname=20182026&pass=TZUySf99&send=Garuda';
        //$url="https://sms.nettyfish.com/vendorsms/pushsms.aspx?apikey=2f302862-76dc-4013-b2ee-889baabd1ede&clientId=d5bce248-0a68-46bb-a906-ebf743e9efca&msisdn=91".$no."&sid=masqat&msg=".$msg."&fl=0&gwid=2";
		
        //your%20Masqati%20Dairy%20OTP%20is%20%7B%23var%23%7D;
        $url="https://sms.nettyfish.com/api/v2/SendSMS?SenderId=masqat&Is_Unicode=false&Is_Flash=false&Message=".$msg."&MobileNumbers=".$no."&ApiKey=2f302862-76dc-4013-b2ee-889baabd1ede&ClientId=d5bce248-0a68-46bb-a906-ebf743e9efca";
		
        //$url=$smsurl.'&dest='.$no.'&msg=Dear%20'.$msg.'';

      // $url= "https://sms.nettyfish.com/vendorsms/pushsms.aspx?apikey=2f302862-76dc-4013-b2ee-889baabd1ede&clientId=d5bce248-0a68-46bb-a906-ebf743e9efca&msisdn=".$no."&sid=masqat&msg=".$msg."&fl=0&gwid=2";


        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curl_scraped_page = curl_exec($ch);
        curl_close($ch);
        
        if(@file($curl_scraped_page)) {
        }
                
        return true;
    }
   
}
?>