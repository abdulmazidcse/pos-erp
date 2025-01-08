<?php

namespace App\Myclass;
use App\Models\GeneralSetting;
use DB;

class SMS 
{
    public function send($contacts, $msg)
    {

        $data = GeneralSetting::first();

        $api_key  = $data ? $data->api_key : "C20016585b5d65039143f5.68321617";
        $senderid = $data ? $data->sender_id : '8809601000774';//'Super Star';

        $URL      = "http://bangladeshsms.com/smsapi?api_key=" . urlencode($api_key) . "&type=text&contacts=" . urlencode($contacts) . "&senderid=" . urlencode($senderid) . "&msg=" . urlencode($msg);
         // dd($URL);

        $ch       = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 0);
        try {
            $output = $content = curl_exec($ch);
        } catch (Exception $ex) {
            $output = "-100";
        }
        return $output;
    }
}
