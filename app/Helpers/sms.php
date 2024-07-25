<?php

public static function smsParser($dataArr){
        try{
            $content = $dataArr['body'];
            $pat = '/^TK ?(?<so_tk>\d+) GD: ?[\-|\+]?(?<so_tien>[\d,]+)VND (?<thoi_gian>\d{2}\/\d{2}\/\d{2,4} \d{2}:\d{2}) (?<so_du>SD: ?[\d,]+VND) ND: ?(?<noi_dung>.*?)$/i';
            if (preg_match($pat, $content, $sms)) {
                foreach ($sms as $key => $val) {
                    if (is_numeric($key)) {
                        unset($sms[$key]);
                    }
                }
                $amount = str_replace([','], [''], $sms['so_tien']);
                $noiDung = trim($sms['noi_dung']);
                $contentToSave = $content;
                $pay_date = $sms['thoi_gian'];
                isset($sms['so_du']) && ($contentToSave = str_replace($sms['so_du'], 'SD:0VND', $contentToSave));
                $created_at = $updated_at = date('Y-m-d H:i:s', time());
                $account_no = $sms['so_tk'];

                $collecter_id = 2;
                if($account_no == '0938766885'){
                    $collecter_id = 7;
                }elseif($account_no == '0949350752'){
                    $collecter_id = 6;
                }elseif($account_no == '0364503454'){
                    $collecter_id = 12;
                }

                //Nếu là single booking
                $pat = '/PT[TVHXC]([\d]+)/i';
                if (preg_match_all($pat, $sms[$_ = 'noi_dung'], $m)) {
                    $bookingIds = (array) $m[1];
                    $booking_id  = @$bookingIds[0];
                    if($booking_id > 0){
                        $arrInsert = [
                            'account_no' => $account_no,
                            'booking_id' => $booking_id,
                            'amount' => $amount,
                            'pay_date' => $pay_date,
                            'status' => 1,
                            'type' => 2,
                            'created_at' => $created_at,
                            'updated_at' => $updated_at,
                            'collecter_id' => $collecter_id,
                            'sms' => $contentToSave
                        ];
                        echo "<pre>";
                        print_r($arrInsert);
                        echo "</pre>";
                        \Illuminate\Support\Facades\DB::connection('mysql')->table('booking_payment')->insert($arrInsert);
                    }
                    return true;
                } // $pat = '/PT[TVHXC]([\d]+)/i';
                $smsError = true;
                //Nếu là cost booking
                $pat = '/^(?<useless>.*?)ACC ?(?<noi_dung>.*?) [FT|Trace|CT|Tu|Den](?<ending>.*?)$/i';
                if (preg_match($pat, $dataArr['body'], $m)) {
                    $smsError = false;
                    $bookingIds = $m['noi_dung'];
                    $bookingIds = array_map('trim', Helper::multiexplode([',', ' ', ';'], $bookingIds));
                    if(!empty($bookingIds)){
                        $remaining = floatval($amount);
                        foreach ($bookingIds as $booking_id){
                            if(empty($remaining)){
                                continue;
                            }
                            $booking = Booking::find($booking_id);
                            //Check if booking is exists
                            if(!empty($booking)){
                                $useDate = new Carbon($booking->use_date);
                                
                                $amount = $remaining > $booking->tien_thuc_thu ? $booking->tien_thuc_thu : $remaining;
                                
                                $arrInsert = [
                                        'type' => 2,
                                        'account_no' => $account_no,
                                        'booking_id' => $booking_id,
                                        'amount' => $amount,
                                        'pay_date' => $pay_date,
                                        'status' => 1,
                                        'created_at' => $created_at,
                                        'updated_at' => $updated_at,
                                        'collecter_id' => $collecter_id,
                                        'sms' => "[+".number_format($amount)."] ".$contentToSave,
                                        'flow' => 1  // thu
                                    ];
                                echo "<pre>";
                                print_r($arrInsert);
                                echo "</pre>";
                                \Illuminate\Support\Facades\DB::connection('mysql')->table('booking_payment')->insert($arrInsert);
                                $remaining = $remaining - $amount;
                            }
                        }
                    }
                    return true;
                } // $pat = '/^(?<useless>.*?)ACC ?(?<noi_dung>.*?) [FT|Trace|CT|Tu|Den](?<ending>.*?)$/i';

                //Nếu là cost booking
                $pat = '/^(?<useless>.*?)TCOC ?(?<noi_dung>.*?) [FT|Trace|CT|Tu|Den](?<ending>.*?)$/i';

                if (preg_match($pat, $dataArr['body'], $m)) {

                    $smsError = false;
                    $bookingIds = $m['noi_dung'];
                    $bookingIds = array_map('trim', Helper::multiexplode([',', ' ', ';'], $bookingIds));
                    if(!empty($bookingIds)){
                        $remaining = floatval($amount);
                        foreach ($bookingIds as $booking_id){
                            if(empty($remaining)){
                                continue;
                            }
                            $booking = Booking::find($booking_id);
                            //Check if booking is exists
                            if(!empty($booking)){
                                $useDate = new Carbon($booking->use_date);
                                
                                $amount = $remaining > $booking->tien_coc ? $booking->tien_coc : $remaining;
                                var_dump($remaining, $booking->tien_coc);

                                if($remaining >= $booking->tien_coc){
                                   
                                    $amount = $booking->tien_coc;                                   
                                    
                                   
                                }else{
                                    $old_tien_coc = $booking->tien_coc;

                                    $amount = $remaining;
                                    $booking->tien_coc = $amount;
                                    $booking->save();

                                }
                                $arrInsert = [
                                        'type' => 2,
                                        'account_no' => $account_no,
                                        'booking_id' => $booking_id,
                                        'amount' => $amount,
                                        'pay_date' => $pay_date,
                                        'status' => 1,
                                        'created_at' => $created_at,
                                        'updated_at' => $updated_at,
                                        'collecter_id' => $collecter_id,
                                        'sms' => "COC [+".number_format($amount)."] ".$contentToSave,
                                        'flow' => 1  // thu
                                    ];
                                echo "<pre>";
                                print_r($arrInsert);
                                echo "</pre>";
                                \Illuminate\Support\Facades\DB::connection('mysql')->table('booking_payment')->insert($arrInsert);
                                $remaining = $remaining - $amount;
                            }
                        }
                    }
                    return true;
                } // $pat = '/^(?<useless>.*?)ACC ?(?<noi_dung>.*?) [FT|Trace|CT|Tu|Den](?<ending>.*?)$/i';

                //Nếu là cost booking                
                $pat = '/^(?<useless>.*?)COMM ?(?<code_chi_tien>.*?) (?<ending>.*?)$/i';
                if (preg_match($pat, $noiDung, $noiDungArr)) {
                    $smsError = false;

                     $codeChiTien = $noiDungArr['code_chi_tien'];

                    if(!empty($codeChiTien)){

                        $remaining = floatval($amount);
                        $bookings = Booking::where('code_chi_tien', $codeChiTien)->get();

                        foreach ($bookings as $booking){
                            if(empty($remaining)){
                                continue;
                            }
                            
                            //Check if booking is exists
                            if(!empty($booking)){
                                $booking_id = $booking->id;    
                                $amount = $remaining > $booking->hoa_hong_sales ? $booking->hoa_hong_sales : $remaining;
                                
                                $booking->thuc_chi_hh = $amount;
                                $booking->save();

                                $arrInsert = [
                                        'type' => 2,
                                        'account_no' => $account_no,
                                        'booking_id' => $booking_id,
                                        'amount' => $amount,
                                        'pay_date' => $pay_date,
                                        'status' => 1,
                                        'created_at' => $created_at,
                                        'updated_at' => $updated_at,
                                        'collecter_id' => $collecter_id,
                                        'sms' => "[-".number_format($amount)."] ".$contentToSave,
                                        'flow' => 2  // thu
                                    ];
                                echo "<pre>";
                                print_r($arrInsert);
                                echo "</pre>";
                                \Illuminate\Support\Facades\DB::connection('mysql')->table('booking_payment')->insert($arrInsert);
                                $remaining = $remaining - $amount;
                            }
                        }
                    }
                    return true;
                } // $pat = '/^(?<useless>.*?)ACC ?(?<noi_dung>.*?) [FT|Trace|CT|Tu|Den](?<ending>.*?)$/i';

                //Nếu là chi tiền theo mã
                $pat = '/^(?<useless>.*?)COST ?(?<code_ids>.*?) [FT|Trace|CT|Tu|Den|-](?<ending>.*?)$/i';
                if (preg_match($pat, $noiDung, $noiDungArr)) {
                    $smsError = false;
                    $costIds = $noiDungArr['code_ids'];
                    if(!empty($costIds)){
                        $costIds = explode(' ', $costIds);
                        $remaining = abs(floatval($amount));
                        $runningIndex = 0;
                        $timeChiTien = date("Y/m/d H:i:s");
                        foreach ($costIds as $costId){
                            if(empty($remaining)){
                                continue;
                            }
                            $cost = Cost::find($costId);
                            //Check if booking is exists
                            if(!empty($cost)){
                                try{
                                    if($runningIndex == count($costIds) - 1){ //Always set remaining to the last booking
                                        $amount = $remaining;
                                    } else{
                                        $amount =  $remaining > $cost->total_money ? $cost->total_money : $remaining;
                                    }
                                    $cost->thuc_chi = $amount;
                                    //$cost->image_url = $contentToSave;
                                    $cost->unc_type = 2;
                                    $cost->status = 2;
                                    $cost->nguoi_chi = $collecter_id;
                                    $cost->time_chi_tien = $timeChiTien;
                                    $cost->sms_chi = $contentToSave;
                                    $cost->save();

                                    $remaining = $remaining - $amount;

                                    // luu booking_payment
                                    if($cost->booking_id){
                                        $tmpArrBk = explode(',', $cost->booking_id);
                                        foreach($tmpArrBk as $booking_id){
                                            $booking_id = strtolower($booking_id);
                                            $booking_id = str_replace("ptt", "", $booking_id);
                                            $booking_id = str_replace("pth", "", $booking_id);
                                            $booking_id = str_replace("ptv", "", $booking_id);
                                            $booking_id = str_replace("ptx", "", $booking_id);
                                            $booking_id = str_replace("ptc", "", $booking_id);
                                            if($booking_id > 0){
                                                $arrInsert = [
                                                    'type' => 2,
                                                    'account_no' => $account_no,
                                                    'booking_id' => $booking_id,
                                                    'amount' => $amount,
                                                    'pay_date' => $pay_date,
                                                    'status' => 1,
                                                    'created_at' => $created_at,
                                                    'updated_at' => $updated_at,
                                                    'collecter_id' => $collecter_id,
                                                    'sms' => "[-".number_format($amount)."] ".$contentToSave,
                                                    'flow' => 2 // chi
                                                ];
                                                echo "<pre>";
                                                print_r($arrInsert);
                                                echo "</pre>";
                                                \Illuminate\Support\Facades\DB::connection('mysql')->table('booking_payment')->insert($arrInsert);
                                            }
                                        }



                                    } // end luu booking_payment

                                }  catch (\Exception $ex){
                                    $myfile = fopen("logs.txt", "a") or die("Unable to open file!");
                                    fwrite($myfile, 'Errors: ' . date("Y/m/d H:i:s") . " " .$ex->getMessage()."\n");
                                    fclose($myfile);
                                    return;
                                }
                            }
                            $runningIndex++;
                        }
                    }
                    return true;
                } // //Nếu là chi tiền theo mã

                //Nếu là EXPPAY
                $pat = '/^(?<useless>.*?)EXPPAY ?(?<code_chi_tien>.*?) (?<ending>.*?)$/i';
                if (preg_match($pat, $noiDung, $noiDungArr)) {
                    $smsError = false;
                    $codeChiTien = $noiDungArr['code_chi_tien'];

                    if(!empty($codeChiTien)){
                        $remaining = abs(floatval($amount));

                        $runningIndex = 0;
                        $paymentRequests = PaymentRequest::where('code_chi_tien', $codeChiTien)->get();
                        $time = date("Y/m/d H:i:s");
                        foreach ($paymentRequests as $paymentRequest){
                            if(empty($remaining)){
                                continue;
                            }

                            //Check if booking is exists
                            if(!empty($paymentRequest)){
                                try{
                                    if($runningIndex == count($paymentRequests) - 1){ //Always set remaining to the last booking
                                        $amount = $remaining;
                                    } else{
                                        $amount =  $remaining > $paymentRequest->total_money ? $paymentRequest->total_money : $remaining;
                                    }
                                    $paymentRequest->thuc_chi = $amount;
                                    $paymentRequest->image_url = $contentToSave;
                                    $paymentRequest->nguoi_chi = $collecter_id;
                                    $paymentRequest->time_chi_tien = $time;
                                    $paymentRequest->date_pay = $time;
                                    $paymentRequest->status = 2;
                                    $paymentRequest->sms_chi = "[-".number_format($amount)."] ".$contentToSave;
                                    $paymentRequest->save();

                                    $remaining = $remaining - $amount;

                                    // luu booking_payment
                                    if($paymentRequest->booking_id){
                                        $tmpArrBk = explode(',', $paymentRequest->booking_id);
                                        foreach($tmpArrBk as $booking_id){
                                            $booking_id = strtolower($booking_id);
                                            $booking_id = str_replace("ptt", "", $booking_id);
                                            $booking_id = str_replace("pth", "", $booking_id);
                                            $booking_id = str_replace("ptv", "", $booking_id);
                                            $booking_id = str_replace("ptx", "", $booking_id);
                                            $booking_id = str_replace("ptc", "", $booking_id);

                                            if($booking_id > 0){
                                                $arrInsert = [
                                                    'type' => 2,
                                                    'account_no' => $account_no,
                                                    'booking_id' => $booking_id,
                                                    'amount' => $amount,
                                                    'pay_date' => $pay_date,
                                                    'status' => 1,
                                                    'created_at' => $created_at,
                                                    'updated_at' => $updated_at,
                                                    'collecter_id' => $collecter_id,
                                                    'sms' => $contentToSave,
                                                    'flow' => 2 // chi
                                                ];
                                                echo "<pre>";
                                                print_r($arrInsert);
                                                echo "</pre>";
                                                \Illuminate\Support\Facades\DB::connection('mysql')->table('booking_payment')->insert($arrInsert);
                                            }
                                        }



                                    } // end luu booking_payment
                                }  catch (\Exception $ex){
                                    $myfile = fopen("logs.txt", "a") or die("Unable to open file!");
                                    fwrite($myfile, 'Errors: ' . date("Y/m/d H:i:s") . " " .$ex->getMessage()."\n");
                                    fclose($myfile);
                                    return;
                                }
                            }
                            $runningIndex++;
                        }
                    }

                    return true;
                } //Nếu là EXPPAY

                //Nếu là chi tiền
                $pat = '/^(?<useless>.*?)EXP ?(?<code_chi_tien>.*?) (?<ending>.*?)$/i';
                if (preg_match($pat, $noiDung, $noiDungArr)) {
                    $smsError = false;
                    $codeChiTien = $noiDungArr['code_chi_tien'];
                    if(!empty($codeChiTien)){
                        $remaining = abs(floatval($amount));
                        $runningIndex = 0;
                        $costs = Cost::where('code_chi_tien', $codeChiTien)->get();
                        $timeChiTien = date("Y/m/d H:i:s");
                        foreach ($costs as $cost){
                            if(empty($remaining)){
                                continue;
                            }
                            try{
                                if($runningIndex == count($costs) - 1){ //Always set remaining to the last booking
                                    $amount = $remaining;
                                } else{
                                    $amount =  $remaining > $cost->total_money ? $cost->total_money : $remaining;
                                }
                                $cost->thuc_chi = $amount;
                                $cost->image_url = $contentToSave;
                                $cost->unc_type = 2;
                                $cost->status = 2;
                                $cost->nguoi_chi = $collecter_id;
                                $cost->time_chi_tien = $timeChiTien;
                                $cost->sms_chi = $contentToSave;
                                $cost->save();

                                $remaining = $remaining - $amount;

                                // luu booking_payment
                                if($cost->booking_id){
                                    $tmpArrBk = explode(',', $cost->booking_id);
                                    foreach($tmpArrBk as $booking_id){
                                        $booking_id = strtolower($booking_id);
                                        $booking_id = str_replace("ptt", "", $booking_id);
                                        $booking_id = str_replace("pth", "", $booking_id);
                                        $booking_id = str_replace("ptv", "", $booking_id);
                                        $booking_id = str_replace("ptx", "", $booking_id);
                                        $booking_id = str_replace("ptc", "", $booking_id);
                                        if($booking_id > 0){
                                            $arrInsert = [
                                                'type' => 2,
                                                'account_no' => $account_no,
                                                'booking_id' => $booking_id,
                                                'amount' => $amount,
                                                'pay_date' => $pay_date,
                                                'status' => 1,
                                                'created_at' => $created_at,
                                                'updated_at' => $updated_at,
                                                'collecter_id' => $collecter_id,
                                                'sms' => "[-".number_format($amount)."] ".$contentToSave,
                                                'flow' => 2
                                            ];
                                            echo "<pre>";
                                            print_r($arrInsert);
                                            echo "</pre>";
                                            \Illuminate\Support\Facades\DB::connection('mysql')->table('booking_payment')->insert($arrInsert);
                                        }

                                    }


                                } // end luu booking_payment
                            }  catch (\Exception $ex){
                                $myfile = fopen("logs.txt", "a") or die("Unable to open file!");
                                fwrite($myfile, 'Errors: ' . date("Y/m/d H:i:s") . " " .$ex->getMessage()."\n");
                                fclose($myfile);
                                return;
                            }
                            $runningIndex++;
                        }
                    }
                    return true;
                }//Nếu là chi tiền

                //Nếu là payment request
                $pat = '/^(?<useless>.*?)ADVPAY ?(?<code_ung_tien>.*?) (?<ending>.*?)$/i';
                if (preg_match($pat, $noiDung, $noiDungArr)) {
                    $smsError = false;
                    $costNopTien = $noiDungArr['code_ung_tien'];
                    if(!empty($costNopTien)){
                        $remaining = abs(floatval($amount));
                        $runningIndex = 0;
                        $paymentRequests = PaymentRequest::where('code_ung_tien', $costNopTien)->get();
                        $time = date("Y/m/d H:i:s");
                        foreach ($paymentRequests as $paymentRequest){
                            if(empty($remaining)){
                                continue;
                            }

                            //Check if booking is exists
                            if(!empty($paymentRequest)){
                                try{
                                    if($runningIndex == count($paymentRequests) - 1){ //Always set remaining to the last booking
                                        $amount = $remaining;
                                    } else{
                                        $amount =  $remaining > $paymentRequest->total_money ? $paymentRequest->total_money : $remaining;
                                    }
                                    $paymentRequest->thuc_chi = $amount;
                                    //$paymentRequest->image_url = $contentToSave;
                                    $paymentRequest->nguoi_nop = $collecter_id;
                                    $paymentRequest->sms_ung = "[-".number_format($amount)."] ".$contentToSave;
                                    $paymentRequest->time_ung_tien = $time;

                                    //$paymentRequest->status = 2;
                                    $paymentRequest->save();

                                    $remaining = $remaining - $amount;
                                }  catch (\Exception $ex){
                                    $myfile = fopen("logs.txt", "a") or die("Unable to open file!");
                                    fwrite($myfile, 'Errors: ' . date("Y/m/d H:i:s") . " " .$ex->getMessage()."\n");
                                    fclose($myfile);
                                    return;
                                }
                            }
                            $runningIndex++;
                        }
                    }

                    return true;
                }//Nếu là payment request

                //Nếu là nộp tiền
                $pat = '/^(?<useless>.*?)ADV ?(?<code_ung_tien>.*?) (?<ending>.*?)$/i';
                if (preg_match($pat, $noiDung, $noiDungArr)) {
                    $smsError = false;
                    $costNopTien = $noiDungArr['code_ung_tien'];
                    if(!empty($costNopTien)){
                        $remaining = abs(floatval($amount));
                        $runningIndex = 0;
                        $costs = Cost::where('code_ung_tien', $costNopTien)->get();
                        $time = date("Y/m/d H:i:s");
                        foreach ($costs as $cost){
                            if(empty($remaining)){
                                continue;
                            }

                            //Check if booking is exists
                            if(!empty($cost)){
                                try{
                                    if($runningIndex == count($costs) - 1){ //Always set remaining to the last booking
                                        $amount = $remaining;
                                    } else{
                                        $amount =  $remaining > $cost->total_money ? $cost->total_money : $remaining;
                                    }
                                    $cost->thuc_chi = $amount;
                                    //$cost->image_url = $contentToSave;
                                    $cost->sms_ung = "[-".number_format($amount)."] ".$contentToSave;
                                    $cost->nguoi_nop = $collecter_id;
                                    $cost->time_ung_tien = $time;
                                    $cost->save();

                                    $remaining = $remaining - $amount;
                                }  catch (\Exception $ex){
                                    $myfile = fopen("logs.txt", "a") or die("Unable to open file!");
                                    fwrite($myfile, 'Errors: ' . date("Y/m/d H:i:s") . " " .$ex->getMessage()."\n");
                                    fclose($myfile);
                                    return;
                                }
                            }
                            $runningIndex++;
                        }
                    }

                    return true;
                }//Nếu là nộp tiền

                //Nếu là chi tiền theo mã
                $pat = '/^(?<useless>.*?)PAY ?(?<code_ids>.*?) [FT|Trace|CT|Tu|Den|-](?<ending>.*?)$/i';
                if (preg_match($pat, $noiDung, $noiDungArr)) {
                    $smsError = false;
                    $costIds = $noiDungArr['code_ids'];
                    if(!empty($costIds)){
                        $costIds = explode(' ', $costIds);
                        $remaining = abs(floatval($amount));
                        $runningIndex = 0;
                        $timeChiTien = date("Y/m/d H:i:s");
                        foreach ($costIds as $costId){
                            if(empty($remaining)){
                                continue;
                            }
                            $paymentRequest = PaymentRequest::find($costId);
                            //Check if booking is exists
                            if(!empty($paymentRequest)){
                                try{
                                    if($runningIndex == count($costIds) - 1){ //Always set remaining to the last booking
                                        $amount = $remaining;
                                    } else{
                                        $amount =  $remaining > $paymentRequest->total_money ? $paymentRequest->total_money : $remaining;
                                    }
                                    $paymentRequest->thuc_chi = $amount;
                                    //$paymentRequest->image_url = $contentToSave;
                                    $paymentRequest->unc_type = 2;
                                    $paymentRequest->nguoi_chi = $collecter_id;
                                    $paymentRequest->time_chi_tien = $timeChiTien;
                                    $paymentRequest->date_pay = $timeChiTien;
                                    $paymentRequest->sms_chi = $contentToSave;
                                    $paymentRequest->status = 2;
                                    $paymentRequest->save();

                                    $remaining = $remaining - $amount;
                                    // luu booking_payment
                                    if($paymentRequest->booking_id){
                                        $tmpArrBk = explode(',', $paymentRequest->booking_id);
                                        foreach($tmpArrBk as $booking_id){
                                            $booking_id = strtolower($booking_id);
                                            $booking_id = str_replace("ptt", "", $booking_id);
                                            $booking_id = str_replace("pth", "", $booking_id);
                                            $booking_id = str_replace("ptv", "", $booking_id);
                                            $booking_id = str_replace("ptx", "", $booking_id);
                                            $booking_id = str_replace("ptc", "", $booking_id);
                                            if($booking_id > 0){
                                                $arrInsert = [
                                                    'type' => 2,
                                                    'account_no' => $account_no,
                                                    'booking_id' => $booking_id,
                                                    'amount' => $amount,
                                                    'pay_date' => $pay_date,
                                                    'status' => 1,
                                                    'created_at' => $created_at,
                                                    'updated_at' => $updated_at,
                                                    'collecter_id' => $collecter_id,
                                                    'sms' => "[-".number_format($amount)."] ".$contentToSave,
                                                    'flow' => 2 // chi
                                                ];
                                                echo "<pre>";
                                                print_r($arrInsert);
                                                echo "</pre>";
                                                \Illuminate\Support\Facades\DB::connection('mysql')->table('booking_payment')->insert($arrInsert);
                                            }
                                        }

                                    } // end luu booking_payment
                                }  catch (\Exception $ex){
                                    $myfile = fopen("logs.txt", "a") or die("Unable to open file!");
                                    fwrite($myfile, 'Errors: ' . date("Y/m/d H:i:s") . " " .$ex->getMessage()."\n");
                                    fclose($myfile);
                                    return;
                                }
                            }
                            $runningIndex++;
                        }
                    }
                    return true;
                }//Nếu là chi tiền theo mã


                //Nếu là DPS code
                $pat = '/^(?<useless>.*?)DPDT ?(?<code_nop_tien>.*?) (?<ending>.*?)$/i';
                if (preg_match($pat, $noiDung, $noiDungArr)) {                
                    $smsError = false;
                    $codeNopTien = $noiDungArr['code_nop_tien'];
                    if(!empty($codeNopTien)){
                        $amount = abs(floatval($amount));
                        $bookings = Booking::where('code_nop_tien_dt', $codeNopTien)->get();
                        if($bookings){
                            try{
                                //Update booking                              
                                $time = date("Y/m/d H:i:s");
                                $remaining = $amount;
                                $runningIndex = 0;
                                foreach ($bookings as $booking){
                                    if(empty($remaining)){
                                        continue;
                                    }

                                    //Check if booking is exists
                                    if(!empty($booking)){
                                        try{
                                            if($runningIndex == count($bookings) - 1){ //Always set remaining to the last booking
                                                $amount = $remaining;
                                            } else{
                                                $amount =  $remaining > $booking->tien_thuc_thu ? $booking->tien_thuc_thu : $remaining;
                                            }
                                            
                                            $booking->time_nop_tien_dt = $time;
                                            $booking->save();

                                            $remaining = $remaining - $amount;

                                            $arrInsert = [
                                                'account_no' => $account_no,
                                                'booking_id' => $booking->id,
                                                'amount' => $amount,
                                                'pay_date' => $pay_date,
                                                'status' => 1,
                                                'type' => 2,
                                                'created_at' => $created_at,
                                                'updated_at' => $updated_at,
                                                'collecter_id' => $collecter_id,
                                                'sms' => "[+".number_format($amount)."] ".$contentToSave,
                                                'flow' => 1 // thu
                                            ];
                                            echo "<pre>";
                                            print_r($arrInsert);
                                            echo "</pre>";
                                            \Illuminate\Support\Facades\DB::connection('mysql')->table('booking_payment')->insert($arrInsert);

                                        }  catch (\Exception $ex){
                                            $myfile = fopen("logs.txt", "a") or die("Unable to open file!");
                                            fwrite($myfile, 'Errors: ' . date("Y/m/d H:i:s") . " " .$ex->getMessage()."\n");
                                            fclose($myfile);
                                            return;
                                        }
                                    }
                                    $runningIndex++;
                                }

                            }  catch (\Exception $ex){
                                $myfile = fopen("logs.txt", "a") or die("Unable to open file!");
                                fwrite($myfile, 'Errors: ' . date("Y/m/d H:i:s") . " " .$ex->getMessage()."\n");
                                fclose($myfile);
                                return;
                            }
                        }
                    }

                    return true;
                }//Nếu là DPS code


                //Nếu là DPS code
                $pat = '/^(?<useless>.*?)DPS ?(?<code_nop_tien>.*?) (?<ending>.*?)$/i';
                if (preg_match($pat, $noiDung, $noiDungArr)) {

                    $smsError = false;
                    $codeNopTien = $noiDungArr['code_nop_tien'];
                    if(!empty($codeNopTien)){
                        $amount = abs(floatval($amount));
                        $bookings = Booking::where('code_nop_tien', $codeNopTien)->get();
                        if($bookings){
                            try{
                                //Update booking                              
                                $time = date("Y/m/d H:i:s");
                                $remaining = $amount;
                                $runningIndex = 0;
                                foreach ($bookings as $booking){
                                    if(empty($remaining)){
                                        continue;
                                    }

                                    //Check if booking is exists
                                    if(!empty($booking)){
                                        try{
                                            if($runningIndex == count($bookings) - 1){ //Always set remaining to the last booking
                                                $amount = $remaining;
                                            } else{
                                                $amount =  $remaining > $booking->tien_thuc_thu ? $booking->tien_thuc_thu : $remaining;
                                            }
                                            $booking->thuc_nop = $amount;
                                            $booking->time_nop_tien = $time;
                                            $booking->save();

                                            $remaining = $remaining - $amount;

                                            $arrInsert = [
                                                'account_no' => $account_no,
                                                'booking_id' => $booking->id,
                                                'amount' => $amount,
                                                'pay_date' => $pay_date,
                                                'status' => 1,
                                                'type' => 2,
                                                'created_at' => $created_at,
                                                'updated_at' => $updated_at,
                                                'collecter_id' => $collecter_id,
                                                'sms' => "[+".number_format($amount)."] ".$contentToSave,
                                                'flow' => 1 // thu
                                            ];
                                            echo "<pre>";
                                            print_r($arrInsert);
                                            echo "</pre>";
                                            \Illuminate\Support\Facades\DB::connection('mysql')->table('booking_payment')->insert($arrInsert);

                                        }  catch (\Exception $ex){
                                            $myfile = fopen("logs.txt", "a") or die("Unable to open file!");
                                            fwrite($myfile, 'Errors: ' . date("Y/m/d H:i:s") . " " .$ex->getMessage()."\n");
                                            fclose($myfile);
                                            return;
                                        }
                                    }
                                    $runningIndex++;
                                }

                            }  catch (\Exception $ex){
                                $myfile = fopen("logs.txt", "a") or die("Unable to open file!");
                                fwrite($myfile, 'Errors: ' . date("Y/m/d H:i:s") . " " .$ex->getMessage()."\n");
                                fclose($myfile);
                                return;
                            }
                        }
                    }

                    return true;
                }//Nếu là DPS code

                

                //Nếu là chi tiền theo mã
                $pat = '/^(?<useless>.*?)KTK ?(?<code_ids>.*?) [FT|Trace|CT|Tu|Den|-](?<ending>.*?)$/i';
                if (preg_match($pat, $noiDung, $noiDungArr)) {
                    $smsError = false;
                    $costIds = $noiDungArr['code_ids'];
                    if(!empty($costIds)){
                        $costIds = explode(' ', $costIds);
                   
                        $runningIndex = 0;
                        $timeChiTien = date("Y/m/d H:i:s");
                        foreach ($costIds as $costId){
                            
                            $revenue = Revenue::find($costId);
                            //Check if booking is exists
                            if(!empty($revenue)){
                                try{ 
                                    $revenue->unc_type = 2;
                                    $revenue->nguoi_thu_tien = $collecter_id;                                    
                                    //$revenue->pay_date = $timeChiTien;
                                    $revenue->sms = $contentToSave;
                                    $revenue->status = 1;
                                    $revenue->save();
                                    
                                }  catch (\Exception $ex){
                                    $myfile = fopen("logs.txt", "a") or die("Unable to open file!");
                                    fwrite($myfile, 'Errors: ' . date("Y/m/d H:i:s") . " " .$ex->getMessage()."\n");
                                    fclose($myfile);
                                    return;
                                }
                            }
                            $runningIndex++;
                        }
                    }
                    return true;
                }//Nếu là NỘP KHOẢN THU KHÁC
                if($smsError){

                }

                return true;
            } else {
                $myfile = fopen("logs.txt", "a") or die("Unable to open file!");
                fwrite($myfile, 'V2 Errors: ' . date("Y/m/d H:i:s") . " Can not parse content "."\n");
                fclose($myfile);
                return false;
            }
        }catch(\Exception $ex){
            $myfile = fopen("logs.txt", "a") or die("Unable to open file!");
            fwrite($myfile, 'Errors: ' . date("Y/m/d H:i:s") . " " .$ex->getMessage()."\n");
            fclose($myfile);
            return;
        }
    }