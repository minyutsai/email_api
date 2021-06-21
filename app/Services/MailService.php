<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailService
{


    public function relay_aws($request)
    {
        
        $url ='http://abc.com.tw/API/smtp.aspx';

        $client = new Client();
        $response = $client->request(
            'POST',
            $url,
            [
                'form_params' => [
                    'body' => $request['mail_body'],
                    'subject' => $request['subject'],
                    'to'=> $request['to_address'],
                    'toname'=> $request['to_name'],
                    'from'=> $request['from_address'],
                    'fromname'=> $request['from_name'],
                ],
                'exceptions' => false,
            ]
        );
      
        
        $res = json_decode($response->getBody());

        if( $res->status == "success" ){
            $res = ['status'=>'success'];
        }else{
            $res = ['status' => false , 'err' => '信件寄送失敗' ];
        }
        
        return  response()->json($res);
    }

    public function relay_gcp($request)
    {
        try {
           Mail::send([],[], function ($message) use($request) { 
                $message->to($request['to_address'])
                ->from($request['from_address'])
                ->subject($request['subject'])
                ->setBody($request['mail_body'] , 'text/html'); 
            });
        } catch (Throwable $e) {
            $err = $e;
        }

        
        $res = ['status'=>'success'];
        
        if (isset($err) ) {
            $res = ['status' => false , 'err' => $err->getMessage() ];
        }

        return  response()->json($res);
    }
}
