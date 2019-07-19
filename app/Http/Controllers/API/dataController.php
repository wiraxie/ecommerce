<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;

class dataController extends Controller
{
   public function registration(Request $request)
   {
	   date_default_timezone_set("Asia/Jakarta");
	   
	   $name = $request->input('name');
	   $email = $request->input('email');
	   $addr = $request->input('addr');
	   
	   $password = $request->input('password');
	   $confirm = $request->input('confirm');
	   
	   $getemail = DB::table('ms_customers')->select('cst_email')->where('cst_email', $email)->get();
	   
	   if($name == '' || $email == '' || $addr == '')
	   {
			echo '[{"message":"semua input harus diisi!"}]';
			http_response_code(400);
			die();
	   }
	   elseif($getemail != '[]')
	   {
			echo '[{"message":"email sudah terdaftar!"}]';
			http_response_code(400);
			die();
	   }
	   elseif($password != $confirm)
	   {
			echo '[{"message":"password tidak cocok dengan konfirmasi password!"}]';
			http_response_code(400);
			die();
	   }
	   else
	   {
			DB::table('ms_customers')->insert([
				'cst_name' => $name,
				'cst_email' => $email,
				'cst_password' => base64_encode(md5($password)),
				'cst_addr' => $addr,
				'created_at'=> now()
			]);
			
			echo '[{"message":"berhasil melakukan pendaftaran!"}]';
			http_response_code(200);
			die();
	   }
   }
	
   public function login(Request $request)
   { 
	   $email = $request->input('email');
	   $password = $request->input('password');
	   
	   $getpassword = DB::table('ms_customers')->select('cst_password')->where('cst_email', $email)->get();
	   $unnecessary = array('[', ']', '{', '}', '"', 'cst_password:');
	   $getpassword = str_replace($unnecessary, '', $getpassword);
	   
	   $getemail = DB::table('ms_customers')->select('cst_email')->where('cst_email', $email)->get();
	   
		if($getamil == '[]')
		{
			echo '[{"message":"email tidak terdaftar!"}]';
			http_response_code(400);
			die();
		}
		elseif(base64_encode(md5($password)) != $getpassword)
		{
			echo '[{"message":"email atau password salah!"}]';
			http_response_code(400);
			die();
		}
		else
		{
			$token = base64_encode(strtotime(now()));
			
			DB::table('ms_customers')->where('cst_email', $email)->update([
				'cst_token'=> $token
			]);
			
			$data = DB::table('ms_customers')->select('id', 'cst_email', 'cst_name')->where('cst_email', $email)->get();
			
			return $data;

		}
   }
   
   public function getproducts(Request $request)
   {
	   
		$data = DB::table('ms_products')->select('id', 'prd_code', 'prd_name', 'prd_image', 'prd_price')
				->where('prd_status', 'Aktif')->get();
			
		return $data;
	   
   }
   
   
   public function postpurchase(Request $request)
   {
		date_default_timezone_set("Asia/Jakarta");
	   
		$cstid = $request->input('cstid');
		$prdid = $request->input('prdid');
		
		$month = date("m");
		$year = date("y");
		
		$query = "SELECT trans_code 
		FROM trans_history
		WHERE left(trans_code, 4) = '".$month.$year."' order by id desc limit 1;";
		//echo $query;die();
		$res=DB::select($query);
		$res = array_values($res);
		$res = json_encode($res);
		//echo $res;die();
		
		if($res == '[]')
		{	
			DB::table('trans_history')->insert([
				'cst_id' => $cstid,
				'prd_id' => $prdid,
				'trans_code'=> $month.$year.'001',
				'created_at'=> now()
			]);
		}
		else
		{
			$unnecessary = array('[', ']', '{', '}', '"', 'trans_code:');
			$res2 =  str_replace($unnecessary, '', $res);
			$transcode = str_pad($res2+1, 7, '0', STR_PAD_LEFT);
			
			DB::table('trans_history')->insert([
				'cst_id' => $cstid,
				'prd_id' => $prdid,
				'trans_code'=> $transcode,
				'created_at'=> now()
			]);
		}
		
		echo '[{"message":"berhasil!"}]';
		http_response_code(200);
		die();
		
	}
   
   public function cancelpurchase(Request $request)
   {
		$code = $request->input('trans_code');

		DB::table('trans_history')->where('trans_code', $code)->delete();

		echo '[{"message":"berhasil!"}]';
		http_response_code(200);
		die();
   }
   
   public function gethistory(Request $request)
   {
		$id = $_GET['id'];

		$data = DB::table('trans_history')
			->join('ms_products', 'trans_history.prd_id', '=', 'ms_products.id')
			->select('trans_history.id as trans_id', 'trans_history.trans_code', 'ms_products.prd_name')
			->where('trans_history.cst_id', $id)
			->get();
		
		return $data;
   }
}