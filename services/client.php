<?php
require_once(realpath(dirname(__FILE__) . "/tools/rest.php"));
require_once(realpath(dirname(__FILE__) . "/tools/mail_handler.php"));

class CLIENT extends REST{

    private $mysqli = NULL;
    private $db = NULL;
    private $product                = NULL;
    private $product_category       = NULL;
    private $product_order          = NULL;
    private $product_order_detail   = NULL;
    private $product_image          = NULL;
    private $category               = NULL;
    private $user                   = NULL;
    private $fcm                    = NULL;
    private $news_info              = NULL;
    private $currency               = NULL;
    private $config                 = NULL;
    private $mail_handler           = NULL;
    public $conf                    = NULL;

    public function __construct($db) {
        parent::__construct();
        $this->db = $db;
        $this->mysqli = $db->mysqli;
        $this->user = new User($this->db);
        $this->product = new Product($this->db);
        $this->product_category = new ProductCategory($this->db);
        $this->product_order = new ProductOrder($this->db);
        $this->product_order_detail = new ProductOrderDetail($this->db);
        $this->product_image = new ProductImage($this->db);
        $this->category = new Category($this->db);
        $this->fcm = new Fcm($this->db);
        $this->news_info = new NewsInfo($this->db);
        $this->currency = new Currency($this->db);
        $this->config = new Config($this->db);
        $this->app_version = new AppVersion($this->db);
        $this->mail_handler = new MailHandler($this->db);
        $this->conf = new CONF();
    }


    /*TRANSAKSI*/
    public function nomorVA(){
        if($this->get_request_method() != "POST") $this->response('', 406);
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        var_dump($data->order_id);
        $orderid = $data->order_id;
        $statuspay = $data->status_pay;
        $q = "SELECT * FROM product_order WHERE code='".$orderid."' LIMIT 1";
        $query = $this->mysqli->query($q) or die($this->mysqli->error.__LINE__);
        if($query->num_rows > 0){
            $uporder = $this->mysqli->query("UPDATE product_order SET status_va='".$statuspay."' WHERE code='".$orderid."'");
            if($uporder){
                $up = array('status'=> 'sukses','msg'=>'Update Status Pembayaran','bayar'=>$statuspay);
            }else{
                $up = array('status'=> 'gagal','msg'=>'Gagal Update Status Pembayaran');    
            }
        }else{
            $up = array('status'=> 'gagal','msg'=>'Gagal Update Status Pembayaran');
        }
        //$up = array('order_id'=> $orderid,'status'=>$statuspay, 'msg'=> 'ini dari bob');
        $this->show_response($up);
    }

    public function http_build_query_for_curl( $arrays, &$new = array(), $prefix = null ) {

        if ( is_object( $arrays ) ) {
            $arrays = get_object_vars( $arrays );
        }

        foreach ( $arrays AS $key => $value ) {
            $k = isset( $prefix ) ? $prefix . '[' . $key . ']' : $key;
            //$new[$k] = $value;
            if ( is_array( $value ) OR is_object( $value )  ) {
               $this->http_build_query_for_curl( $value, $new, $k );
            } else {
               $new[$k] = $value;
            }
        }
    }
    
    public function requestEzpy(){
        if($this->get_request_method() != "POST") $this->response('',406);
        $idorder = $this->_request['order_id'];
        $iduser = $this->_request['email'];
        $namebeli = $this->_request['name'];
        $methodpayment = $this->_request['method'];

        $rEZ = "SELECT a.code,a.total_fees,a.id, b.order_id, b.product_id, b.product_name, b.amount, b.price_item FROM product_order as a INNER JOIN product_order_detail as b on a.id=b.order_id WHERE a.code='".$idorder."'";
        $qEZ = $this->mysqli->query($rEZ) or die($this->mysqli->error.__LINE__);
        $produkid = "";
        $produkharga = ""; 
        $emailpenjual ="";
        $keyserver="";
        $detailtransaksi = [];
        if($qEZ->num_rows > 0){
            while ($rporder=$qEZ->fetch_assoc()) {
                $produkorder = $rporder['id'];
                $produkharga = $rporder['total_fees'];
                $produkid = $rporder['product_id'];
                $namaproduk = $rporder['product_name'];
                $jumlahproduk = $rporder['amount'];
                $hargaproduk = $rporder['price_item'];

                $sendbody = array(
                        'nama'=>$namaproduk, 
                        'jumlah'=>$jumlahproduk, 
                        'harga'=>$hargaproduk 
                    );
                array_push($detailtransaksi, $sendbody);
            }
            $stringJsonEncode = json_encode($detailtransaksi);
            //echo urlencode($stringJsonEncode);
            //print_r($detailtransaksi);
            $tokenuser = $this->mysqli->query("SELECT token FROM user WHERE email='".$iduser."' LIMIT 1");
            while ($rtoken = $tokenuser->fetch_assoc()) {
                $keyserver = $rtoken['token'];
            }
            $quser = $this->mysqli->query("SELECT a.iduser, a.id, b.id, b.email FROM product as a INNER JOIN user as b on a.iduser=b.id WHERE a.id='".$produkid."' LIMIT 1");
            while($rowuser = $quser->fetch_assoc()){
                $emailpenjual = $rowuser['email'];
            }
            if($methodpayment == "ezpy"){
                $newArray = array(
                    'penjual' => $emailpenjual,
                    'pembeli' => $iduser,
                    'nominal' => $produkharga,
                    'detil_transaksi' => $detailtransaksi
                );
                $url = "http://ezpy.advlop.com/api/v1/transaksi";
                $this->http_build_query_for_curl( $newArray, $post );
                $kirim = http_build_query($post);
                $headers = array();
                $headers[] = 'Authorization: Bearer '. $keyserver;
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch,CURLOPT_POST, true);
                curl_setopt($ch,CURLOPT_POSTFIELDS, $kirim);
                curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                //Send the request
                $response = curl_exec($ch);
                //Close request
                if ($response === FALSE) {
                    die('ERROR: ' . curl_error($ch));
                }
                curl_close($ch);
                $hasil = json_decode($response);
                $up = array('status'=> 'sukses','msg'=>'Sukses Update Status Pembayaran','data'=>$hasil);
                
            }else if($methodpayment == "va"){
                $newArray = array(
                    'name' => $namebeli,
                    'email' => $iduser,
                    'penjual' => $emailpenjual,
                    'pembeli' => $iduser,
                    'nominal' => $produkharga,
                    'order_id' => $idorder,
                    'detil_transaksi' => $detailtransaksi
                );
                $url = "http://ezpy.advlop.com/api/v1/charge/midtrans/transaksi";
                $this->http_build_query_for_curl( $newArray, $post );
                $isiva = http_build_query($post);
                $headers = array();
                $headers[] = 'Authorization: Bearer '. $keyserver;
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch,CURLOPT_POSTFIELDS, $isiva);
                curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                //Send the request
                $response = curl_exec($ch);
                //Close request
                if ($response === FALSE) {
                    die('ERROR: ' . curl_error($ch));
                }
                curl_close($ch);
                $hasil = json_decode($response);
                $nomorVA = $hasil->{"bill_key"};
                $nomorRek = $hasil->{"biller_code"};
                $ststransaksi = $hasil->{"transaction_status"};

                $qVA = $this->mysqli->query("UPDATE product_order SET va_pay='".$nomorVA."', status_va='".$ststransaksi."' WHERE code='".$idorder."'");
                if($qVA){

                    $up = array('status'=> 'sukses','msg'=>'Sukses Update Status Pembayaran','data'=>$hasil);
                }else{
                    $up = array('status'=> 'gagal','msg'=>'Gagal Update Status Pembayaran','data'=>$hasil);
                }
            }
        }else{
            $up = array('status'=> 'gagal','msg'=>'Gagal Update Status Pembayaran');
        }        
        
        $this->show_response($up);
    }

    /*transaksi*/
    /*ADMIN*/
    public function findAllUMKM(){
        if($this->get_request_method() != "GET") $this->response('',406);
        $findUMKM = $this->user->listAllUMKM();
        $respumkm = array('status' => 'sukses', 'listumkm' =>  $findUMKM);
        $this->show_response($respumkm);
    }
    public function findUnverfikasiUMKM(){
        if($this->get_request_method() != "GET") $this->response('',406);
        $findUnverfikasi = $this->user->unconfirmedumkm();
        $respumkm = array('status' => 'sukses', 'listumkm' =>  $findUnverfikasi);
        $this->show_response($respumkm);   
    }
    public function DelStatusUMKM(){
        if($this->get_request_method() != "POST") $this->response('',406);
        $id = $this->_request['iduser'];
        $pk = 'id';
        $table_name='user';
        $delumkm = $this->db->delete_one($id, $pk, $table_name);
        $this->show_response($delumkm);
    }
    public function verificationUMKM(){
        if($this->get_request_method() != "POST") $this->response('', 406);
        $data = $this->_request['isistatus'];
        $idumkm = $this->_request['idumkm'];
        $serialumkm = $this->_request['serial'];
        $status = "Notification for verivication process";
        $selectuser =$this->mysqli->query("SELECT * FROM user WHERE id='".$idumkm."' LIMIT 1");
        if($selectuser->num_rows>0){
            while ($rowumkm = $selectuser->fetch_assoc()) {
                $serialumkm = $rowumkm['serial'];
                $q="UPDATE user SET status='".$data."' WHERE id='".$idumkm."'";
                $r = $this->mysqli->query($q) or die($this->mysqli->error.__LINE__);
                if($r){
                    /*$regid = $this->fcm->findBySerial($serialumkm);
                    $registration_ids = $regid['regid'];
                    $updateumkm = array('status' => 'sukses', 'id' =>$idumkm, 'msg' => 'Sukses Verfikasi');*/
                    $regid = $this->fcm->findBySerial($serialumkm);
                    $registration_ids = array($regid['regid']);
                    $data = array(
                        'title' => 'Verification for UMKM',
                        'content' => 'Success for verification',
                        'type' => 'VERIFICATION_NOTIF',
                        'code' => $idumkm,
                        'status' => $status
                    );
                    $this->fcm->sendPushNotification($registration_ids, $data);
                    $this->show_response($data);
                }else{
                    $updateumkm = array('status' => 'gagal', 'id' =>$idumkm, 'msg' => 'Gagal verifikasi');
                    
                }
                
            }
        }else{
            $updateumkm = array('status' => 'gagal', 'msg' => 'Gagal verifikasi');
        }
        $this->show_response($updateumkm);
        
    }
    public function detailInfoUMKM(){
        if($this->get_request_method() != "POST") $this->response('', 406);
        $idumkm = $this->_request['idumkm'];
        $id = $this->_request['id'];
        $detailUMKM = "SELECT * FROM doc_umkm WHERE id='".$id."' AND iduser='".$idumkm."'";
        $qumkm= $this->db->get_list($detailUMKM);
        $this->show_response($qumkm);
    }
    public function upPassAdmin(){
        if($this->get_request_method() != "POST") $this->response('', 406);
        $oldpass = $this->_request['oldpass'];
        $newpass = $this->_request['newpass'];
        $iduser = $this->_request['iduser'];
        $seldata = "SELECT password FROM user WHERE id='".$iduser."'";
        $r = $this->mysqli->query($seldata) or die($this->mysqli->error.__LINE__);
        if($r->num_rows > 0){
            while($row = $r->fetch_assoc()){
                $opass = $row['password'];
                if($opass == $oldpass){
                    $update = "UPDATE user set password='".$newpass."' WHERE id='".$iduser."'";
                    $rup = $this->mysqli->query($update) or die($this->mysqli->error.__LINE__);
                    if($rup){
                        $updateumkm = array('status' => 'sukses ganti kata sandi');
                        $this->show_response($updateumkm);
                    }else{
                        $updateumkm = array('status' => 'gagal ganti kata sandi');
                        $this->show_response($updateumkm);
                    }
                }else{
                    $updateumkm = array('status' => 'Salah Mengisikan Kata Sandi Lama');
                    $this->show_response($updateumkm);
                }
            }
        }
    }

    public function cekOrder(){
        if($this->get_request_method() != "GET") $this->response('', 406);
        $query="SELECT * FROM product_order po WHERE manual_pay IS NOT NULL ORDER BY po.id DESC";
        $responn = array('status'=>'sukses','data'=>$this->db->get_list($query));
        $this->show_response($responn);
    }

    public function OrderCek(){
        if($this->get_request_method() != "POST") $this->response('', 406);
        $id=$this->_request['iduser'];
        $where = $this->_request['isi'];

        $transaksi = [];
        $query = $this->mysqli->query("SELECT b.id, a.iduser, a.id FROM user as b INNER JOIN product as a on b.id=a.iduser WHERE b.id ='".$id."'");
        if($query->num_rows>0){
             while ($roworder=$query->fetch_assoc()) {
                $product_id = $roworder['id'];
                if($where != ""){
                    $qorder = $this->mysqli->query("SELECT a.buyer,a.created_at,a.code,a.total_fees,a.id,a.status,a.status_va,a.serial, b.order_id, b.product_id, b.product_name, b.amount, b.price_item FROM product_order as a INNER JOIN product_order_detail as b on a.id=b.order_id WHERE b.product_id='".$product_id."' AND status='".$where."' ORDER BY a.created_at DESC");
                }else{
                    $qorder = $this->mysqli->query("SELECT a.buyer,a.created_at,a.code,a.total_fees,a.id,a.status,a.status_va,a.serial, b.order_id, b.product_id, b.product_name, b.amount, b.price_item FROM product_order as a INNER JOIN product_order_detail as b on a.id=b.order_id WHERE b.product_id='".$product_id."' ORDER BY a.created_at DESC");
                }
                while ($rowlist = $qorder->fetch_assoc()) {
                    //$kode = $rowlist
                    $kode = $rowlist['code'];
                    $status = $rowlist['status'];
                    $total = $rowlist['status_va'];
                    $date = $rowlist['created_at'];
                    $serial = $rowlist['serial'];
                    $pembeli = $rowlist['buyer'];

                    //$mil = 1227643821310;
                    $seconds = $date / 1000;
                    $tanggal = date("d-M-Y", $seconds);
                    $body = array(
                            'kode'=>$kode, 
                            'statusorder'=>$status, 
                            'statuspay'=>$total,
                            'serial' => $serial,
                            'tanggal' => $tanggal,
                            'pembeli' =>$pembeli
                        );
                    array_push($transaksi, $body);
                }
            }
            $show = array('status'=>'success','msg'=>'order ok','order_list'=>$transaksi);
        }else{
            $show = array('status'=>'failed','msg'=>'no order');
        }
       
        $this->show_response($show);  

    }
    public function detailOrder() {
        if($this->get_request_method() != "POST") $this->response('', 406);
        $idorder = $this->_request['order_id'];
        $user = $this->_request['iduser'];
        $tran =[];
        $query = "SELECT a.serial,a.code,a.total_fees,a.id, b.order_id, b.product_id, b.product_name, b.amount, b.price_item FROM product_order as a INNER JOIN product_order_detail as b on a.id=b.order_id WHERE a.code='".$idorder."'";
        $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
        if($r->num_rows>0){
            while ($rowdetail = $r->fetch_assoc()) {
            $idprodk = $rowdetail['product_id'];
            $product_name = $rowdetail['product_name'];
            $jml = $rowdetail['amount'];
            $price_item = $rowdetail['price_item'];
            $serial= $rowdetail['serial'];
            $queryyy = "SELECT b.id, a.iduser, a.id,a.name,a.image FROM user as b INNER JOIN product as a on b.id=a.iduser WHERE b.id ='".$user."' AND a.id='".$idprodk."'";
            $qproduk = $this->mysqli->query($queryyy) or die($this->mysqli->error.__LINE__);
                while ($rowproduk = $qproduk->fetch_assoc()) {
                    $name = $rowproduk['name'];
                    $body = array(
                        'nama'=>$name, 
                        'idproduk'=>$idprodk, 
                        'jumlah'=>$jml,
                        'hargabarang' => $price_item,
                        'serial' => $serial
                    );
                    array_push($tran, $body);
    
                }
            
            }
        }
        $isi = array('status'=>'sukses','data'=>$tran);

        $this->show_response($isi);
    }
    public function setOrder(){
         if($this->get_request_method() != "POST") $this->response('', 406);
        $idorder = $this->_request['order_id'];
        $user = $this->_request['iduser'];
        $serialorder = $this->_request['serial'];
        $tran =[];
        $query = "SELECT a.serial,a.code,a.total_fees,a.id, b.order_id, b.product_id, b.product_name, b.amount, b.price_item FROM product_order as a INNER JOIN product_order_detail as b on a.id=b.order_id WHERE a.code='".$idorder."'";
        $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
        if($r->num_rows>0){
            while ($rowdetail = $r->fetch_assoc()) {
            $idprodk = $rowdetail['product_id'];
            $product_name = $rowdetail['product_name'];
            $jml = $rowdetail['amount'];
            $price_item = $rowdetail['price_item'];
            $serial= $rowdetail['serial'];
            $queryyy = "SELECT b.id, a.iduser, a.id,a.name,a.image FROM user as b INNER JOIN product as a on b.id=a.iduser WHERE b.id ='".$user."' AND a.id='".$idprodk."'";
            $qproduk = $this->mysqli->query($queryyy) or die($this->mysqli->error.__LINE__);
                while ($rowproduk = $qproduk->fetch_assoc()) {
                    // $name = $rowproduk['name'];
                    // $body = array(
                    //     'nama'=>$name, 
                    //     'idproduk'=>$idprodk, 
                    //     'jumlah'=>$jml,
                    //     'hargabarang' => $price_item,
                    //     'serial' => $serial
                    // );
                    // array_push($tran, $body);
                    $query = "UPDATE product SET stock='".$jml."' WHERE id='".$product_id."'";
                    $this->mysqli->query($query) or die($this->mysqli->error.__LINE__); 
                    $query_2 = "UPDATE product_order SET status='PROCESSED' WHERE id='".$idorder."'";

                    $this->mysqli->query($query_2) or die($this->mysqli->error.__LINE__);
                    $regid = $this->fcm->findBySerial($serialorder);
                   
                    $registration_ids = array($regid['regid']);
                    $data = array(
                        'title' => 'Order Status Changed',
                        'content' => 'Your order ' . $idorder .' status has been change to PROCESSED',
                        'type' => 'PROCESS_ORDER',
                        'code' => $idorder,
                        'status' => 'PROCESSED'
                    );
                    $this->fcm->sendPushNotification($registration_ids, $data);
                }
            
            }
        }
        $isi = array('status'=>'sukses','msg'=>'Successfully');

        $this->show_response($isi);
    }
    public function ListOrders(){
        if($this->get_request_method() != "POST") $this->response('', 406);
        $query="SELECT * FROM product_order po ORDER BY po.id DESC";
        $this->show_response($this->db->get_list($query));
    }

    public function upOrder(){
        if($this->get_request_method() != "POST") $this->response('', 406);
        $id = $this->_request['idorder'];
        $serialuser = $this->_request['serialuser'];
        $status = $this->_request['statusorder'];
        $query="SELECT * FROM product_order WHERE id='".$id."'";
        $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
        if($r->num_rows > 0){
            $update = "UPDATE product_order set status='".$status."' WHERE id='".$id."'";
            $rup = $this->mysqli->query($update) or die($this->mysqli->error.__LINE__);
            if($rup){
                $updateumkm = array('status' => 'Transaksi Order Sukses');
                $regid = $this->fcm->findBySerial($serialuser);
                $registration_ids = array($regid['regid']);
                $data = array(
                    'title' => 'Order Status Changed',
                    'content' => 'Your order ' . $id .' status has been change to ' . $status,
                    'type' => 'PROCESS_ORDER',
                    'code' => $id,
                    'status' => $status
                );
                $this->fcm->sendPushNotification($registration_ids, $data);
                $this->show_response($updateumkm);
            }else{
                $updateumkm = array('status' => 'Transaksi Order Gagal');
                $this->show_response($updateumkm);
            }
                
        }
       
    }


    /*USER LOGIN VIEW*/

    public function findUser(){
        if($this->get_request_method() != "POST") $this->response('', 406);
        $username = $this->_request['email'];
        $password = $this->_request['password'];

        $userlogin = $this->user->processLoginUser($username,$password);

        $response = array('status' => 'sukses', 'login_info' =>  $userlogin);
           
        $this->show_response($userlogin);
    }

    public function findUserApi(){
        if($this->get_request_method() != "POST") $this->response('', 406);
        $email = $this->_request['email'];
        $password = $this->_request['password'];
        $serial = $this->_request['serial'];
        $qselect = $this->mysqli->query("SELECT * FROM user WHERE email='".$email."' AND password='".$password."' LIMIT 1");
        if($qselect->num_rows > 0){
            $url = "http://ezpy.advlop.com/api/v1/user/loginApi";
            $fields = array( 
                'email' => urlencode($email), 
                'password' => urlencode($password)
            );
            //url-ify the data for the POST
            foreach($fields as $key=>$value) { 
                $fields_string .= $key.'='.$value.'&'; 
            }
            rtrim($fields_string, '&');
            $headers = array();
            $headers[] = 'application/x-www-form-urlencoded;charset=UTF-8';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST, count($fields));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //Send the request
            $response = curl_exec($ch);
            //Close request
            if ($response === FALSE) {
                die('ERROR: ' . curl_error($ch));
            }
            curl_close($ch);
        
            $hasil = json_decode($response);
            $token = $hasil->{"token"};
           
            if($token != ""){
                $qup = $this->mysqli->query("UPDATE user SET token='".$token."', serial='".$serial."' WHERE email='".$email."'");
                if($qup){
                    while($row=$qselect->fetch_assoc()){
                        $statususer = $row['status'];
                        $idu = $row['id'];
                        $username = $row['email'];
                        $name = $row['name'];
                        $addr = $row['address'];
                        $upusr = array('name'=>$name,'email'=> $username,'addr'=>$addr,'id'=>$idu,'status' => $statususer,'msg' => 'Sukses','token' => $token);
                    }             
                }else{
                    $upusr = array('msg' => 'Gagal','token' => $token,'response'=>$response);
                }    
            }else{
                $upusr = array('msg' => 'Gagal','response'=>$response);
            }
            $this->show_response($upusr);       
        }else{
            $upusr = array('msg' => 'Gagal');
            $this->show_response($upusr);
        }
    }

    public function LogOutAPI(){
        if($this->get_request_method() != "POST") $this->response('', 406);
        $iduser = $this->_request['id'];
        $qselect = $this->mysqli->query("SELECT token FROM user WHERE id='".$iduser."'");
        if($qselect->num_rows > 0){
           while($row = $qselect->fetch_assoc()){
                //$tokem = array('token' => $row['token'],'status'=>'test');
                //$this->show_response($tokem);
                $url = "http://ezpy.advlop.com/api/v1/user/logoutApi";
                $serverKey= $row['token'];
                $headers = array();
                //$headers[] = 'application/x-www-form-urlencoded;charset=UTF-8';
                $headers[] = 'Authorization: Bearer '. $serverKey;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"GET");
                curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
                //Send the request
                $response = curl_exec($ch);
                //Close request
                if ($response === FALSE) {
                    die('ERROR: ' . curl_error($ch));
                }
                curl_close($ch);

                $this->show_response($response);
           } 
        }else{
            $token = array('status'=>'GAGAL');
            $this->show_response($token);
        }
    }

    public function registUser(){
        if($this->get_request_method() != "POST") $this->response('', 406);
        //$user = $this->_request['user'];
        $pass = $this->_request['password'];
        $name = $this->_request['name'];
        $email = $this->_request['email'];
        $addr = $this->_request['addr'];
        $gender = $this->_request['gender'];
        $birth = $this->_request['birth'];

        $user = array('name' => $name, 'email'=>$email, 'password'=> $pass,'address'=>$addr,'ttl'=>$birth,'gender'=>$gender,'status'=>'0');
        $column_names = array('name','email', 'password','address','ttl','gender','status');
        $table_name = 'user';
        $pk = 'id';
        $resp = $this->db->post_one($user, $pk, $column_names, $table_name);

        //$registuser = $this->user->processRegister($name,$user,$email,$pass,$addr,$gender,$birth);
        
        $url = "http://ezpy.advlop.com/api/v1/user/create";
        $fields = array( 
            'email' => urlencode($email), 
            'password' => urlencode($pass),
            'name' => urldecode($name),
            'role'=> urlencode('2')
        );
            //url-ify the data for the POST
        foreach($fields as $key=>$value) { 
            $fields_string .= $key.'='.$value.'&'; 
        }
        rtrim($fields_string, '&');
        $headers = array();
        $headers[] = 'application/x-www-form-urlencoded;charset=UTF-8';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //Send the request
        $response = curl_exec($ch);
            //Close request
        if ($response === FALSE) {
            die('ERROR: ' . curl_error($ch));
        }
        curl_close($ch);
        $respon_regist = array('test'=>$response,'status'=>$resp);
        //$this->show_response($registuser);
        $this->show_response($resp);
    }

    public function SaldoUser(){
        if($this->get_request_method() != "POST") $this->response('', 406);
        $iduser = $this->_request['id'];
        $qselect = $this->mysqli->query("SELECT token,email,status FROM user WHERE id='".$iduser."'");
        if($qselect->num_rows > 0){
            while($row = $qselect->fetch_assoc()){
                $email = $row['email'];
    
                $url = "http://ezpy.advlop.com/api/v1/saldo/".$email."";
                $serverKey= $row['token'];
                $headers = array();
                $headers[] = 'Authorization: Bearer '. $serverKey;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"GET");
                curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
                //Send the request
                $response = curl_exec($ch);
                //Close request
                if ($response === FALSE) {
                    die('ERROR: ' . curl_error($ch));
                }
                curl_close($ch);

                $this->show_response($response);

            }
        }else{
            $response = array('status' => 'error');
            $this->show_response($response);
        }
    }

    /*{UMKM}*/
    public function inProduct(){
        if($this->get_request_method() != "POST") $this->response('', 406);
        $target_dir = "../uploads/products/";
        $target_file_name = $target_dir .basename($_FILES["file"]["name"]);
        //$target_file_name1 = $target_dir .basename($_FILES["file"]["name"]);
        if (isset($_FILES["file"])) 
        {
           if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file_name)) 
           {
                $success = true;
                $message = "Successfully Uploaded";
                $name =$this->_request['name'];
                $image = $this->_request['image'];
                $price = $this->_request['price'];
                $price_discount = $this->_request['price_discount'];
                $stock = $this->_request['stock'];
                $draft = 0;
                $description = $this->_request['description'];
                $status = $this->_request['status'];
                $iduser = $this->_request['iduser'];
                $category = $this->_request['category'];

            
                $data = array('name'=>$name,'image'=>$image,'price'=>$price,
                    'price_discount'=>$price_discount,'stock'=>$stock,'draft'=>$draft,'description'=>$description,'status'=>$status,'iduser'=>$iduser);
                $column_names = array('name', 'image', 'price', 'price_discount', 'stock', 'draft', 'description', 'status', 'iduser');
                $table_name = 'product';
                $pk = 'id';
                $t = $this->db->post_product($data, $pk, $category, $column_names, $table_name);
                $respup = array('status'=>'success','msg'=>$message);
           }
           else 
           {
                $success = false;
                $message = "Error while uploading";
                $respup= array('status'=>'failed','msg'=>$message);
           }
        }
        else 
        {
            $success = false;
            $message = "Required Field Missing";
            $respup = array('status'=>'failed','msg'=>$message);
        }
        $this->show_response($respup);
    }

    /*DELETE PRODUCT*/
    public function delProduct(){
        if($this->get_request_method() != "POST") $this->response('', 406);
        $id =$this->_request['id'];
        $pk = 'id';
        $table_names='product';
        $pkcat ='product_id';
        $table_name='product_category';
        $resp = $this->db->delete_one($id,$pk,$table_names);
        $res = $this->db->delete_one($id,$pkcat,$table_name);
        $data = array('successdel'=>$resp,'succescat'=>$res);
        $this->show_response($data);
    }
    /*DETAIL PRODUCT*/
    public function detailsProductUser(){
        if($this->get_request_method() != "POST") $this->response('', 406);
        $id= $this->_request['id'];
        $iduser = $this->_request['iduser'];
        $resp = $this->product->findOneDetailUser($id,$iduser);
        $this->show_response($resp);
    }
    public function allProductUser(){
        if($this->get_request_method() != "POST") $this->response('', 406);
        $iduser = $this->_request['iduser'];
        $resp = $this->product->findProductUser($iduser);
        $respon = array('status'=>'sukses','data'=>$resp);
        $this->show_response($respon);   
    }

    /*KONSUMEN*/
    public function registUMKM(){
        if($this->get_request_method() != "POST") $this->response('', 406);
        $iduser = $this->_request['iduser'];
        $noktp = $this->_request['noktp'];
        $usaha = $this->_request['namausaha'];
        $deskripsiusaha = $this->_request['deskripsi'];
        $fotoktp = $this->_request['fotoktp'];  
        
        $target_dir = "../uploads/umkm/";
        $target_file_name = $target_dir .basename($_FILES["file"]["name"]);
        //$target_file_name1 = $target_dir .basename($_FILES["file"]["name"]);
        if (isset($_FILES["file"])) 
        {
           if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file_name)) 
           {
                $success = true;
                $message = "Successfully Uploaded";
                $registuser = $this->user->processRegisterUMKM($iduser,$noktp,$usaha,$deskripsiusaha,$fotoktp);
           }
           else 
           {
              $success = false;
              $message = "Error while uploading";
              $registuser = array('status'=>'failed','msg'=>'Error Uploaded Image');
           }
        }
        else 
        {
              $success = false;
              $message = "Required Field Missing";
              $registuser = array('status'=>'failed','msg'=>'Error Image');
        }
        $this->show_response($registuser);
    }
    /* Cek status version and get some config data */
    public function info(){
        if($this->get_request_method() != "GET") $this->response('',406);
        if(!isset($this->_request['version'])) $this->responseInvalidParam();
        $version = (int)$this->_request['version'];
        $query = "SELECT COUNT(DISTINCT a.id) FROM app_version a WHERE version_code = $version AND active = 1";
        $resp_ver = $this->db->get_count($query);
        $config_arr = $this->config->findAllArr();
        $info = array(
            "active" => ($resp_ver > 0),
            "tax" => $this->getValue($config_arr, 'TAX'),
            "currency" => $this->getValue($config_arr, 'CURRENCY'),
            "shipping" => json_decode($this->getValue($config_arr, 'SHIPPING'), true)
        );
        $response = array( "status" => "success", "info" => $info );
        $this->show_response($response);
    }

    /* Response featured News Info */
    public function findAllFeaturedNewsInfo(){
        if($this->get_request_method() != "GET") $this->response('',406);
        $featured_news = $this->news_info->findAllFeatured();
        $object_res = array();
        foreach ($featured_news as $r){
            unset($r['full_content']);
            array_push($object_res, $r);
        }
        $response = array(
            'status' => 'success', 'news_infos' => $object_res
        );
        $this->show_response($response);
    }

    
    /* Response All News Info */
    public function findAllNewsInfo(){
        if($this->get_request_method() != "GET") $this->response('',406);
        $limit = isset($this->_request['count']) ? ((int)$this->_request['count']) : 10;
        $page = isset($this->_request['page']) ? ((int)$this->_request['page']) : 1;
        $q = isset($this->_request['q']) && $this->_request['q'] != null ? ($this->_request['q']) : "";

        $offset = ($page * $limit) - $limit;
        $count_total = $this->news_info->allCountPlain($q, 1);
        $news_infos = $this->news_info->findAllByPagePlain($limit, $offset, $q, 1);

        $object_res = array();
        foreach ($news_infos as $r){
            unset($r['full_content']);
            array_push($object_res, $r);
        }
        $count = count($news_infos);
        $response = array(
            'status' => 'success', 'count' => $count, 'count_total' => $count_total, 'pages' => $page, 'news_infos' => $object_res
        );
        $this->show_response($response);
    }

    /* Response All Product */
    public function findAllProduct(){
        if($this->get_request_method() != "GET") $this->response('',406);
        $limit = isset($this->_request['count']) ? ((int)$this->_request['count']) : 10;
        $page = isset($this->_request['page']) ? ((int)$this->_request['page']) : 1;
        $q = isset($this->_request['q']) && $this->_request['q'] != null ? ($this->_request['q']) : "";
        $category_id = isset($this->_request['category_id']) && $this->_request['category_id'] != null ? ((int)$this->_request['category_id']) : -1;

        $offset = ($page * $limit) - $limit;
        $count_total = $this->product->allCountPlainForClient($q, $category_id);
        $products = $this->product->findAllByPagePlainForClient($limit, $offset, $q, $category_id);

        $object_res = array();
        foreach ($products as $r){
            unset($r['description']);
            array_push($object_res, $r);
        }
        $count = count($products);
        $response = array(
            'status' => 'success', 'count' => $count, 'count_total' => $count_total, 'pages' => $page, 'products' => $object_res
        );
        $this->show_response($response);
    }

    /* Response Details Product */
    public function findProductDetails(){
        if($this->get_request_method() != "GET") $this->response('',406);
        if(!isset($this->_request['id'])) $this->responseInvalidParam();
        $id = (int)$this->_request['id'];
        $product = $this->product->findOnePlain($id);
        if(count($product) > 0){
            $categories = $this->category->getAllByProductIdPlain($id);
            $product_images = $this->product_image->findAllByProductIdPlain($id);
            $product['categories'] = $categories;
            $product['product_images'] = $product_images;   
            $response = array( 'status' => 'success', 'product' => $product );
        } else {
            $response = array( 'status' => 'failed', 'product' => null );
        }
        $this->show_response($response);
    }
    
    /* Response Details News Info */
    public function findNewsDetails(){
        if($this->get_request_method() != "GET") $this->response('',406);
        if(!isset($this->_request['id'])) $this->responseInvalidParam();
        $id = (int)$this->_request['id'];
        $news_info = $this->news_info->findOnePlain($id);
        $response['status'] = 'success';
        $response['news_info'] = $news_info;
        $this->show_response($response);
    }   

    /* Response All Category */
    public function findAllCategory(){
        if($this->get_request_method() != "GET") $this->response('',406);
        $categories = $this->category->findAllForClient();
        $response = array(
            'status' => 'success', 'categories' => $categories
        );
        $this->show_response($response);
    }

    /* Sensor */
    public function sendAllWarn(){
         if($this->get_request_method() != "GET") $this->response('',406);
         $warn = $this->fcm->sendWarn();
         $response= array(
            'status' => 'success', 'warning' => $warn
         );
          $this->show_response($response);
    }

    /* Submit Product Order */
    public function submitProductOrder(){
        if($this->get_request_method() != "POST") $this->response('', 406);
        $data = json_decode(file_get_contents("php://input"), true);
        if(!isset($data) || !isset($data['product_order']) || !isset($data['product_order_detail'])) $this->responseInvalidParam();

        // checking security code
        if(!isset($this->_header['Security']) || $this->_header['Security'] != $this->conf->SECURITY_CODE){
            $m = array('status' => 'failed', 'msg' => 'Invalid security code', 'data' => null);
            $this->show_response($m);
            return;
        }

        // submit order
        $resp_po = $this->product_order->insertOnePlain($data['product_order']);
        if($resp_po['status'] == "success"){
            $order_id = (int)$resp_po['data']['id'];
            $resp_pod = $this->product_order_detail->insertAllPlain($order_id, $data['product_order_detail']);
            if($resp_pod['status'] == 'success'){
                $status = 'success';
                $msg = 'Success submit product order';
                // send email
                $this->mail_handler->curlEmailOrder($order_id);
                
            } else {
                $this->product_order->deleteOnePlain($order_id);
                $status = 'failed';
                $msg = 'Failed when submit order.';
            }
        } else {
            $status = 'failed';
            $msg = 'Failed when submit order';
        }
        $m = array('status' => $status, 'msg' => $msg, 'data' => $resp_po['data']);
        $this->show_response($m);
        return;
    }

    private function getValue($data, $code){
        foreach($data as $d){
            if($d['code'] == $code){
                return $d['value'];
            }
        }
    }
    public function ReLog(){
        if($this->get_request_method() != "POST") $this->response('', 406);
        $iduser = $this->_request['iduser'];
        $q = "SELECT * FROM user WHERE id='".$iduser."' LIMIT 1";
        $qselect = $this->mysqli->query($q);
        if($qselect->num_rows >0){
            $auto = array('resp'=>'resp');
        }
    }
    public function ManualPay(){
        if($this->get_request_method() != "POST") $this->response('',406);
        $idorder = $this->_request['order_id'];
        $pay = $this->_request['pay'];
        $rekening = $this->_request['rekening'];
        $namarekening = $this->_request['namarekening'];
        $queryorder = $this->mysqli->query("SELECT * FROM product_order WHERE code='".$idorder."'");
        if($queryorder->num_rows > 0){  
            $tomorrow = date("d-M-Y", strtotime("+1 day"));
            $upordermanual = $this->mysqli->query("UPDATE product_order SET manual_pay='".$pay."',nama_akun='".$namarekening."',rekening='".$rekening."',limit_transfer='".$tomorrow."' WHERE code='".$idorder."'");
            if($upordermanual){
                $responorder = array('status'=>'sukses','msg'=>'Sukses Update Harga','tomorrow'=>$tomorrow,'rekening'=>$rekening);
            }else{
                $responorder = array('status'=>'failed','msg'=>'Gagal Update Harga','tomorrow'=>'no data');
            }
        }else{
            $responorder = array('status'=>'failed','msg'=>'Gagal Update Harga','tomorrow'=>'no data');
        }
        $this->show_response($responorder);
    }

    public function selectManualPay(){
        if($this->get_request_method() != "POST") $this->response('',406);
        //$qManualPay = $this->mysqli->query("");
    }
}
?>