<?php
class ControllerPaymentSn extends Controller {
	public function index() {
		$this->language->load('payment/sn');

		$data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
       $WebService = $this->config->get('sn_webservice');
		if ($order_info) {
			$amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
			if($this->currency->getCode() == 'RLS') {
			$amount = $amount / 10;
			$amount = ceil ($amount);
			}
			$Description = $order_info['comment'];
			$Paymenter =  $order_info['firstname'] .' ' . $order_info['lastname'];
			$Email = $order_info['email'];
			$Mobile =  $order_info['telephone'];
			
					// Security
					@session_start();
					$sec = uniqid();
					$md = md5($sec.'vm');
					// Security
									

						if ($WebService == 1){
						    
				    if($Email==''){$Email='0'; }
				     if($Paymenter==''){$Paymenter='0';}
				      if($Mobile==''){$Mobile='0';}
				       if($Description==''){$Description='0';}
				       
					   	$data_string = json_encode(array(
					'pin'=> $this->config->get('sn_spin'),
					'price'=> $amount,
					'callback'=>$this->url->link('payment/sn/callback&sec=' . $sec . '&md=' . $md, '', 'SSL'),
					'order_id'=> $this->session->data['order_id'],
					'email'=> $Email,
					'description'=> $Description,
					'name'=> $Paymenter,
					'mobile'=> $Mobile,
					'ip'=> $_SERVER['REMOTE_ADDR'],
					'callback_type'=>2
					));
				    
			        }
					else
					{
					   	$data_string = json_encode(array(
					'pin'=> $this->config->get('sn_spin'),
					'price'=> $amount,
					'callback'=>$this->url->link('payment/sn/callback&sec=' . $sec . '&md=' . $md, '', 'SSL'),
					'order_id'=> $this->session->data['order_id'],
					'email'=> '0',
					'description'=> $Description,
					'name'=> '0',
					'mobile'=> '0',
					'ip'=> $_SERVER['REMOTE_ADDR'],
					'callback_type'=>2
					));
					    
					}

					
					$ch = curl_init('https://developerapi.net/api/v1/request');
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'Content-Length: ' . strlen($data_string))
					);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
					curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 20);
					$result = curl_exec($ch);
					curl_close($ch);
					$json = json_decode($result,true);				
				    					$res=$json['result'];
	                 switch ($res) {
						    case -1:
						    $msg = "پارامترهای ارسالی برای متد مورد نظر ناقص یا خالی هستند . پارمترهای اجباری باید ارسال گردد";
						    break;
						     case -2:
						    $msg = "دسترسی api برای شما مسدود است";
						    break;
						     case -6:
						    $msg = "عدم توانایی اتصال به گیت وی بانک از سمت وبسرویس";
						    break;
						     case -9:
						    $msg = "خطای ناشناخته";
						    break;
						     case -20:
						    $msg = "پین نامعتبر";
						    break;
						     case -21:
						    $msg = "ip نامعتبر";
						    break;
						     case -22:
						    $msg = "مبلغ وارد شده کمتر از حداقل مجاز میباشد";
						    break;
						    case -23:
						    $msg = "مبلغ وارد شده بیشتر از حداکثر مبلغ مجاز هست";
						    break;
						      case -24:
						    $msg = "مبلغ وارد شده نامعتبر";
						    break;
						      case -26:
						    $msg = "درگاه غیرفعال است";
						    break;
						      case -27:
						    $msg = "آی پی مسدود شده است";
						    break;
						      case -28:
						    $msg = "آدرس کال بک نامعتبر است ، احتمال مغایرت با آدرس ثبت شده";
						    break;
						      case -29:
						    $msg = "آدرس کال بک خالی یا نامعتبر است";
						    break;
						      case -30:
						    $msg = "چنین تراکنشی یافت نشد";
						    break;
						      case -31:
						    $msg = "تراکنش ناموفق است";
						    break;
						      case -32:
						    $msg = "مغایرت مبالغ اعلام شده با مبلغ تراکنش";
						    break;
						      case -35:
						    $msg = "شناسه فاکتور اعلامی order_id نامعتبر است";
						    break;
						      case -36:
						    $msg = "پارامترهای برگشتی بانک bank_return نامعتبر است";
						    break;
						        case -38:
						    $msg = "تراکنش برای چندمین بار وریفای شده است";
						    break;
						      case -39:
						    $msg = "تراکنش در حال انجام است";
						    break;
                            case 1:
						    $msg = "پرداخت با موفقیت انجام گردید.";
						    break;
						    default:
						       $msg = $josn['result'];
						}
				if(!empty($json['result']) AND $json['result']==1)
				{
				// Set Session
				$_SESSION['sec']=$sec;
				$_SESSION[$sec] = [
					'price'=>$amount ,
					'order_id'=>$this->session->data['order_id'] ,
					'au'=>$json['au'] ,
				];	
					return '<div style="display:none">'.$json['form'].'</div>Please wait ... <script language="javascript">document.payment.submit(); </script>';
				}
				else
				{
					return "خطایی در اتصال رخ داده است : ".$msg;
				}
		
		}
	}

	public function callback() {
					// Security
					$sec=$_GET['sec'];
					$mdback = md5($sec.'vm');
					$mdurl=$_GET['md'];
					$sec=$_SESSION['sec'];
					$transData = $_SESSION[$sec];
					$amount=$transData['price'];
					// Security
	
		if (isset($transData['order_id'])) {
			$order_id = $transData['order_id'];
		} else {
			$order_id = 0;
		}
		if (isset($transData['au'])) {
			$au = $transData['au'];
		} else {
			$au = 0;
		}
		$this->session->data['au'] = NULL;

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($order_id);
	if(!empty($_GET['sec']) AND !empty($_GET['md']) AND !empty($_GET['au']) AND $mdback == $mdurl){

		if ($order_info) {
			$ok = false;
			$order_status_id = $this->config->get('config_order_status_id');
			$total = $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);
			if ($this->config->get('sn_debug'))
			{
				$this->log->write('sn :: OrderID='.$order_id.' ::  au='.$au.' :: POST=' . implode($this->request->post).' :: GET=' . implode($this->request->get));
			}
			try
			{
				
					$bank_return = $_POST + $_GET ;
					$data_string = json_encode(array (
					'pin' => $this->config->get('sn_spin'),
					'price' => $amount,
					'order_id' => $order_id,
					'au' => $au,
					'bank_return' =>$bank_return,
					));
					
					$ch = curl_init('https://developerapi.net/api/v1/verify');
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'Content-Length: ' . strlen($data_string))
					);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
					curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 20);
					$result = curl_exec($ch);
					curl_close($ch);
					$json = json_decode($result,true);				
				if($json['result']==1)
				{
					$ok = true;
					$order_status_id = $this->config->get('sn_completed_status_id');
				}
				else
				{
					$order_status_id = $this->config->get('sn_failed_status_id');
					if ($this->config->get('sn_debug'))
					{
						$this->log->write('sn :: OrderID='.$order_id.' :: error in verify ' . $json['result'] );
					}
				}
			}
			catch (SoapFault $ex)
			{
				die ('Error2: error in get data from bank.');
			}
			if (!$order_info['order_status_id']) {
				$this->model_checkout_order->addOrderHistory($order_id, $order_status_id);
			} else {
				$this->model_checkout_order->addOrderHistory($order_id, $order_status_id);
			}
			if ($ok == true)
			{
				header('location: '.$this->url->link('checkout/success'));
			}
			else
			{
				header('location: '.$this->url->link('checkout/checkout', '', 'SSL'));
			}
		}
		}
		else
		{
			$order_status_id = $this->config->get('sn_failed_status_id');
			if ($this->config->get('sn_debug'))
			{
				$this->log->write('sn :: OrderID='.$order_id.' :: error in verify ' . $json['result'] );
			}
		}
	}
}