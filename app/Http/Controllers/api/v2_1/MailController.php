<?php

namespace App\Http\Controllers\api\v2_1;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MailService;
use Validator;
use Response;
use GuzzleHttp\Client;


class MailController extends Controller
{

    public function sendAPI(Request $request)
    {

        $v = Validator::make($request->all(), [
            'subject' => 'required',
            'to_address' => 'required',
            'to_name' => 'required',
            'from_address' => 'required',
            'from_name' => 'required',
            'mail_body' => 'required',           
            ]);

        if ($v->fails()) {
            $m = $v->messages()->first(); 
            $this->resp['status'] = false;
            $this->resp['err_no'] = null;
            $this->resp['err_msg'] = $m;
            $this->resp['data'] = null;
            $this->resp['msg'] = '寄送失敗,參數有誤';
            return Response::json($this->resp);
        }


        $context = [
           'subject' => $request->subject,
           'to_address' => $request->to_address,
           'to_name' => $request->to_name,
           'from_address' => $request->from_address,
           'from_name' => $request->from_name,
           'mail_body' => $request->mail_body,
       ];

        // dump( $context  );

        preg_match("/@abc.com.tw$/", $request->to_address, $matches);
        $count_gigamedia_email = count($matches);


        switch ($count_gigamedia_email){
            case 0 :
                $service = new MailService;
                $result = $service->relay_aws( (array) $context );
                break;
            case 1:
                $service = new MailService;
                $result = $service->relay_gcp((array) $context );
                break;
        }


        $res = json_decode( $result->getContent() ) ;

        if( isset($res->err) ){
            $this->resp['status'] = false;
            $this->resp['err_no'] = null;
            $this->resp['err_msg'] = $res->err;
            $this->resp['data'] = null;
            $this->resp['msg'] = '寄送失敗,參數有誤';
            return Response::json($this->resp);
        }

        $this->resp['status'] = true;
            $this->resp['err_no'] = null;
            $this->resp['err_msg'] = null;
            $this->resp['data'] = null;
            $this->resp['msg'] = '信件寄送成功';
        return Response::json($this->resp);
        
    
    }

 
}
