<?php
	function getJsSDK($url)
	{
		$appid='wxaa91b5adc09cac9e';
		$appsecret='2b59ef08f90063c70f5c1ec81474ed46';
		$time=file_get_contents("upload/time.txt");
		$ticket=file_get_contents("upload/ticket.txt");
		if (!$time || (time() - $time >= 3600)){
			$rs = file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid .'&secret=' . $appsecret);
			$rs = json_decode($rs,true);
			if(isset($rs['access_token'])){
				$time = time();
				$access_token = $rs['access_token'];
				$ticketfile = file_get_contents("https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi");
				$ticketfile = json_decode($ticketfile, true);
				$ticket = $ticketfile['ticket'];
				$fp = fopen("upload/time.txt", "w");
				fwrite($fp,$time);
				fclose($fp);
				$fp = fopen("upload/access_token.txt", "w");
				fwrite($fp,$access_token);
				fclose($fp);
				$fp = fopen("upload/ticket.txt", "w");
				fwrite($fp,$ticket);
				fclose($fp);
			}else{
				throw new Exception($rs['errcode']);
			}
		}
		$str = '1234567890abcdefghijklmnopqrstuvwxyz';
		$noncestr = '';
		for($i=0;$i<8;$i++){
			$randval = mt_rand(0,35);
			$noncestr .= $str[$randval];
		}
		$ticketstr="jsapi_ticket=". $ticket ."&noncestr=". $noncestr ."&timestamp=". $time ."&url=". $url;
		$sign = sha1($ticketstr);
		return json_encode(array("appid" => $appid, "time" => $time, "noncestr" => $noncestr, "sign" => $sign, "url" => $url));	
	}
?>