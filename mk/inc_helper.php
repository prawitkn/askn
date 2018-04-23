<?php
if ( ! function_exists('render_json'))
{
    function render_json($json)
    {
        ini_set('display_errors', 0);
        header('Content-Type: application/json');
        echo $json;
    }

}

if ( ! function_exists('get_current_thai_date'))
{
    function get_current_thai_date()
    {
        $d = explode('/', date('d/m/Y'));
        $day = $d[0];
        $month = $d[1];
        $year = (int) $d[2] + 543;

        $current_thai_date = $day . '/' . $month . '/' . $year;
        return $current_thai_date;

    }

}


/**
 * Get user agent
 *
 * @return string User agent
 **/
if ( ! function_exists( 'get_user_agent' ) )
{
    function get_user_agent()
    {
        $ci =& get_instance();
        if ( $ci->agent->is_browser() )
        {
            $agent = $ci->agent->browser() . ' ' . $ci->agent->version() . ' ' . $ci->agent->platform();
        }
        elseif( $ci->agent->is_robot() )
        {
            $agent = $ci->agent->robot();
        }
        elseif( $ci->agent->is_mobile() )
        {
            $agent = $this->agent->mobile();
        }
        else
        {
            $agent = 'Unknow agent';
        }

        return $agent;
    }
}

/**
 * Create logging
 *
 * @param string $log_level Log level 'info', 'warning', 'error'
 * @param string $log_message
 * @param string $log_ip User ip address
 * @param string $log_agent User agent
 **/
if ( ! function_exists( 'logging' ) )
{
    function logging( $logs )
    {
        $ci =& get_instance();

        date_default_timezone_set('Asia/Bangkok');

        $logs['log_date'] = date("Y-m-d");
        $logs['log_time'] = date("H:i:s");
        $logs['log_user'] = $ci->session->userdata('user_name');

        $ci->db->insert( 'logs', $logs );
    }
}
/**
 * Generate serial
 *
 * @param   string  $sr_type Type of serial
 * @param   boolean $gen_date Add 2 digits of year to serial
 * @return  string
 */
if ( ! function_exists('generate_serial'))
{
    function generate_serial($sr_type)
    {
        $ci =& get_instance();
        $ci->load->model('Serial_model', 'serial');
        //Generate serial with year and month digit.

        $prefix = $ci->serial->get_prefix($sr_type);
        $gen_date = $ci->serial->get_gen_date($sr_type);

        //generate with year and month
        if($gen_date){
            //formatted serial
            $sr_m = $ci->serial->get_month_prefix($sr_type);
            $sr_y = $ci->serial->get_year_prefix($sr_type);

            //for month prefix
            if($sr_m != date('m')){
                //update month
                $ci->serial->update_month($sr_type, date('m'));
                //set to current month
                $sr_m = date('m');
                $ci->serial->reset_serial($sr_type);
            }

            //for year prefix
            $current_year = date('Y') + 543;
            $short_year = substr($current_year, -2) ;

            if($sr_y != $short_year){
                //update year
                $ci->serial->update_year($sr_type, $short_year);
				$ci->serial->reset_serial($sr_type);
            }

            $new_sr = $prefix.'-'.$short_year.$sr_m;

        }else{//generate without year and month
            $new_sr = $prefix;
        }

        $sn = $ci->serial->get_serial($sr_type);
        $sn = get_string_length($sn);

        $a = $new_sr. '-' .$sn;

        //Update serials
        $ci->serial->update_serial($sr_type);

        return $a;
    }
}
//private function for serial
function get_string_length($sn){

    switch(strlen($sn)){
        case 1:
            $new_sn = '00000'.$sn;
            break;
        case 2:
            $new_sn = '0000'.$sn;
            break;
        case 3:
            $new_sn = '000'.$sn;
            break;
        case 4:
            $new_sn = '00'.$sn;
            break;
        case 5:
            $new_sn = '0'.$sn;
            break;
        case 6:
            $new_sn = $sn;
            break;
        default:
            $new_sn = '000001';
    }
    return $new_sn;
}

if( !function_exists('gen_unique')){
    function gen_unique(){
        $id = uniqid(hash("sha512",rand()), TRUE);
        $code = hash("sha512", $id);
        return substr($code, 0, 32);
    }
}

if(!function_exists('to_thai_date')){
    function to_thai_date($eng_date){
        if(strlen($eng_date) != 10){
            return null;
        }else{
            $new_date = explode('-', $eng_date);

            $new_y = (int) $new_date[0] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[2];

            $thai_date = $new_d . '/' . $new_m . '/' . $new_y;

            return $thai_date;
        }
    }
}

if(!function_exists('to_thai_date_fdt')){
    function to_thai_date_fdt($eng_date){
        //if(strlen($eng_date) != 10){
        //    return null;
        //}else{
			$new_datetime = explode(' ', $eng_date);
            $new_date = explode('-', $new_datetime[0]);

            $new_y = (int) $new_date[0] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[2];

            $thai_date = $new_d . '/' . $new_m . '/' . $new_y;

            return $thai_date;
        //}
    }
}

if(!function_exists('to_thai_datetime_fdt')){
    function to_thai_datetime_fdt($eng_date){
        //if(strlen($eng_date) != 10){
        //    return null;
        //}else{
			$new_datetime = explode(' ', $eng_date);
            $new_date = explode('-', $new_datetime[0]);

            $new_y = (int) $new_date[0] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[2];

            $thai_date = $new_d . '/' . $new_m . '/' . $new_y . ' ' . substr($new_datetime[1],0,5);

            return $thai_date;
        //}
    }
}

if(!function_exists('toThaiDatetimeFromDatetime')){
    function toThaiDatetimeFromDatetime($eng_date){
        //if(strlen($eng_date) != 10){
        //    return null;
        //}else{
			$new_datetime = explode(' ', $eng_date);
            $new_date = explode('-', $new_datetime[0]);

            $new_y = (int) $new_date[0] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[2];

            $thai_date = $new_d . '/' . $new_m . '/' . $new_y . ' ' . substr($new_datetime[1],0,5);

            return $thai_date;
        //}
    }
}


if(!function_exists('to_thai_datetime')){
    function to_thai_datetime($eng_date){
        if(strlen($eng_date) != 10){
            return null;
        }else{
			$new_datetime = explode(' ', $eng_date);
            $new_date = explode('-', $new_datetime[0]);

            $new_y = (int) $new_date[0] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[2];

            $thai_date = $new_d . '/' . $new_m . '/' . $new_y . ' ' . $new_datetime[1].substring(0,5);

            return $thai_date;
        }
    }
}

if(!function_exists('js_to_thai_date')){
    function js_to_thai_date($eng_date){
        if(strlen($eng_date) != 10){
            return null;
        }else{
            $new_date = explode('/', $eng_date);

            $new_y = (int) $new_date[2] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[0];

            $thai_date = $new_d . '/' . $new_m . '/' . $new_y;

            return $thai_date;
        }
    }
}

if(!function_exists('to_mysql_date')){
    function to_mysql_date($thai_date){
        if(strlen($thai_date) != 10){
            return null;
        }else{
            $new_date = explode('/', $thai_date);

            $new_y = (int)$new_date[2] - 543;
            $new_m = $new_date[1];
            $new_d = $new_date[0];

            $mysql_date = $new_y . '-' . $new_m . '-' . $new_d;

            return $mysql_date;
        }
    }
}

if(! function_exists('string_to_js_date'))
{
    function string_to_js_date($date)
    {
        $d = substr($date, 0, 2);
        $m = substr($date, 2, 2);
        $y = substr($date, 4, 4);

        return $d . '/' . $m . '/' . $y;
    }
}

if(! function_exists('count_age'))
{
    function count_age($date)
    {
        $c_y = (int) date('Y');
        $o_y = (int) explode('-', $date);
        $n_y = (int) $o_y[0];

        return $c_y - $n_y;
    }
}



//prawit.kn
//for print number 0 or leading zero in thaidate to >> "-", "๐", ""  
if ( ! function_exists('replaceFirstZero'))
{
    function replaceFirstZero($str, $insteadStr)
    {
		$str = trim($str);
		if(strlen($str)>=1){
			if(leftStr($str,1)=="0" or leftStr($str,1)=="๐"){
				return $insteadStr . rightStr($str, strlen($str) -1);
			}else{
				return $str;
			}
		}else{
			return "";
		}
    }
}
if ( ! function_exists('to_thai_number'))
{
    function to_thai_number($str)
    {
		$str = str_replace("1","๑",$str);
		$str = str_replace("2","๒",$str);
		$str = str_replace("3","๓",$str);
		$str = str_replace("4","๔",$str);
		$str = str_replace("5","๕",$str);
		$str = str_replace("6","๖",$str);
		$str = str_replace("7","๗",$str);
		$str = str_replace("8","๘",$str);
		$str = str_replace("9","๙",$str);
		$str = str_replace("0","๐",$str);
        return $str;
    }
}

if ( ! function_exists('to_thai_number_zero_dash'))
{
    function to_thai_number_zero_dash($str)
    {
		$str = str_replace("1","๑",$str);
		$str = str_replace("2","๒",$str);
		$str = str_replace("3","๓",$str);
		$str = str_replace("4","๔",$str);
		$str = str_replace("5","๕",$str);
		$str = str_replace("6","๖",$str);
		$str = str_replace("7","๗",$str);
		$str = str_replace("8","๘",$str);
		$str = str_replace("9","๙",$str);
		$str = str_replace("0","๐",$str);
		if($str == "๐"){
			$str = "-";
		}
        return $str;
    }
}

if(!function_exists('to_thai_short_date')){
    function to_thai_short_date($eng_date){
        if(strlen($eng_date) != 10){
            return null;
        }else{
            $new_date = explode('-', $eng_date);

            $new_y = (int) $new_date[0] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[2];

			$new_d = to_thai_number((int)$new_d); // 01 -> ๑
		
            $thai_date = $new_d . '/' . $new_m . '/' . substr($new_y, 2, 2);
			//replace thai month
			$thai_short_date = '';
			switch($new_m){
				case '01' : $thai_short_date = str_replace("/01/"," ม.ค.",$thai_date); break;
				case '02' : $thai_short_date = str_replace("/02/"," ก.พ.",$thai_date); break;
				case '03' : $thai_short_date = str_replace("/03/"," มี.ค.",$thai_date); break;
				case '04' : $thai_short_date = str_replace("/04/"," เม.ย.",$thai_date); break;
				case '05' : $thai_short_date = str_replace("/05/"," พ.ค.",$thai_date); break;
				case '06' : $thai_short_date = str_replace("/06/"," มิ.ย.",$thai_date); break;
				case '07' : $thai_short_date = str_replace("/07/"," ก.ค.",$thai_date); break;
				case '08' : $thai_short_date = str_replace("/08/"," ส.ค.",$thai_date); break;
				case '09' : $thai_short_date = str_replace("/09/"," ก.ย.",$thai_date); break;
				case '10' : $thai_short_date = str_replace("/10/"," ต.ค.",$thai_date); break;
				case '11' : $thai_short_date = str_replace("/11/"," พ.ย.",$thai_date); break;
				case '12' : $thai_short_date = str_replace("/12/"," ธ.ค.",$thai_date); break;
			}
			
			$thai_short_date = to_thai_number($thai_short_date);
			
            return $thai_short_date;
        }
    }
}

if(!function_exists('to_thai_short_date_fdt')){
    function to_thai_short_date_fdt($eng_date){
        if(strlen($eng_date) != 19){
            return null;
        }else{
			$new_datetime = explode(' ', $eng_date);
            $new_date = explode('-', $new_datetime[0]);

            $new_y = (int) $new_date[0] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[2];

            $thai_date = $new_d . '/' . $new_m . '/' . substr($new_y, 2, 2);
			//replace thai month
			$thai_short_date = '';
			switch($new_m){
				case '01' : $thai_short_date = str_replace("/01/"," ม.ค.",$thai_date); break;
				case '02' : $thai_short_date = str_replace("/02/"," ก.พ.",$thai_date); break;
				case '03' : $thai_short_date = str_replace("/03/"," มี.ค.",$thai_date); break;
				case '04' : $thai_short_date = str_replace("/04/"," เม.ย.",$thai_date); break;
				case '05' : $thai_short_date = str_replace("/05/"," พ.ค.",$thai_date); break;
				case '06' : $thai_short_date = str_replace("/06/"," มิ.ย.",$thai_date); break;
				case '07' : $thai_short_date = str_replace("/07/"," ก.ค.",$thai_date); break;
				case '08' : $thai_short_date = str_replace("/08/"," ส.ค.",$thai_date); break;
				case '09' : $thai_short_date = str_replace("/09/"," ก.ย.",$thai_date); break;
				case '10' : $thai_short_date = str_replace("/10/"," ต.ค.",$thai_date); break;
				case '11' : $thai_short_date = str_replace("/11/"," พ.ย.",$thai_date); break;
				case '12' : $thai_short_date = str_replace("/12/"," ธ.ค.",$thai_date); break;
			}
			
			$thai_short_date = to_thai_number($thai_short_date);
			
            return $thai_short_date;
        }
    }
}

if(!function_exists('mysqldatetime_to_thaishortdatetime')){
    function to_thai_short_datetime($eng_date){
        if(strlen($eng_date) != 19){
            return null;
        }else{
			$new_datetime = explode(' ', $eng_date);
            $new_date = explode('-', $new_datetime[0]);

            $new_y = (int) $new_date[0] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[2];
			
			$new_hhmm = substr($new_datetime[1], 0,5);

            $thai_date = $new_d . '/' . $new_m . '/' . substr($new_y, 2, 2);
			//replace thai month
			$thai_short_date = '';
			switch($new_m){
				case '01' : $thai_short_date = str_replace("/01/"," ม.ค.",$thai_date); break;
				case '02' : $thai_short_date = str_replace("/02/"," ก.พ.",$thai_date); break;
				case '03' : $thai_short_date = str_replace("/03/"," มี.ค.",$thai_date); break;
				case '04' : $thai_short_date = str_replace("/04/"," เม.ย.",$thai_date); break;
				case '05' : $thai_short_date = str_replace("/05/"," พ.ค.",$thai_date); break;
				case '06' : $thai_short_date = str_replace("/06/"," มิ.ย.",$thai_date); break;
				case '07' : $thai_short_date = str_replace("/07/"," ก.ค.",$thai_date); break;
				case '08' : $thai_short_date = str_replace("/08/"," ส.ค.",$thai_date); break;
				case '09' : $thai_short_date = str_replace("/09/"," ก.ย.",$thai_date); break;
				case '10' : $thai_short_date = str_replace("/10/"," ต.ค.",$thai_date); break;
				case '11' : $thai_short_date = str_replace("/11/"," พ.ย.",$thai_date); break;
				case '12' : $thai_short_date = str_replace("/12/"," ธ.ค.",$thai_date); break;
			}
			
			$thai_short_date = $thai_short_date.' '.$new_hhmm;
			
            return $thai_short_date;
        }
    }
}

if(!function_exists('to_thaiyear_short_date')){
    function to_thaiyear_short_date($eng_date){
        if(strlen($eng_date) != 10){
            return null;
        }else{
            $new_date = explode('-', $eng_date);

            $new_y = (int) $new_date[0] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[2];

			$new_d = (int)$new_d; // 01 -> ๑			
		
            $thai_date = $new_d . '/' . $new_m . '/' . substr($new_y, 2, 2);
			//replace thai month
			$thai_short_date = '';
			switch($new_m){
				case '01' : $thai_short_date = str_replace("/01/"," ม.ค.",$thai_date); break;
				case '02' : $thai_short_date = str_replace("/02/"," ก.พ.",$thai_date); break;
				case '03' : $thai_short_date = str_replace("/03/"," มี.ค.",$thai_date); break;
				case '04' : $thai_short_date = str_replace("/04/"," เม.ย.",$thai_date); break;
				case '05' : $thai_short_date = str_replace("/05/"," พ.ค.",$thai_date); break;
				case '06' : $thai_short_date = str_replace("/06/"," มิ.ย.",$thai_date); break;
				case '07' : $thai_short_date = str_replace("/07/"," ก.ค.",$thai_date); break;
				case '08' : $thai_short_date = str_replace("/08/"," ส.ค.",$thai_date); break;
				case '09' : $thai_short_date = str_replace("/09/"," ก.ย.",$thai_date); break;
				case '10' : $thai_short_date = str_replace("/10/"," ต.ค.",$thai_date); break;
				case '11' : $thai_short_date = str_replace("/11/"," พ.ย.",$thai_date); break;
				case '12' : $thai_short_date = str_replace("/12/"," ธ.ค.",$thai_date); break;
			}			
            return $thai_short_date;
        }
    }
}



if(!function_exists('get_date_from_date')){
    function get_date_from_date($eng_date){
        if(strlen($eng_date) != 10){
            return null;
        }else{
            $new_date = explode('-', $eng_date);

            $new_y = (int) $new_date[0] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[2];
			
            return $new_d;
        }
    }
}

if(!function_exists('get_month_from_date')){
    function get_month_from_date($eng_date){
        if(strlen($eng_date) != 10){
            return null;
        }else{
            $new_date = explode('-', $eng_date);

            $new_y = (int) $new_date[0] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[2];
			
            return $new_m;
        }
    }
}

if(!function_exists('get_full_month_from_date')){
    function get_full_month_from_date($eng_date){
        if(strlen($eng_date) != 10){
            return null;
        }else{
            $new_date = explode('-', $eng_date);

            $new_y = (int) $new_date[0] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[2];
			
			$full_month = '';
			switch($new_m){
				case '01' : $full_month = 'มกราคม'; break;
				case '02' : $full_month = 'กุมภาพันธ์'; break;
				case '03' : $full_month = 'มีนาคม'; break;
				case '04' : $full_month = 'เมษายน'; break;
				case '05' : $full_month = 'พฤษภาคม'; break;
				case '06' : $full_month = 'มิถุนายน'; break;
				case '07' : $full_month = 'กรกฎาคม'; break;
				case '08' : $full_month = 'สิงหาคม'; break;
				case '09' : $full_month = 'กันยายน'; break;
				case '10' : $full_month = 'ตุลาคม'; break;
				case '11' : $full_month = 'พฤศจิกายน'; break;
				case '12' : $full_month = 'ธันวาคม'; break;
			}
			
            return $full_month;
        }
    }
}

if(!function_exists('get_year_from_date')){
    function get_year_from_date($eng_date){
        if(strlen($eng_date) != 10){
            return null;
        }else{
            $new_date = explode('-', $eng_date);

            $new_y = (int) $new_date[0] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[2];
			
            return $new_y;
        }
    }
}

if (!function_exists('to_ucfirst')) {
    function to_ucfirst($string) {
        $string = strtoupper(substr($string, 0, 1)) . strtolower(substr($string, 1, strlen($string)));
        return $string;
    }
}


















if(!function_exists('to_thai_dd_sl_mm_sl_yyyy_fdt')){
    function to_thai_dd_sl_mm_sl_yyyy_fdt($mysql_datetime){
		$mysql_datetime = trim($mysql_datetime);		
        if(strlen($mysql_datetime) != 19){
            return null;
        }else{
			$new_datetime = explode(' ', $mysql_datetime);
            $new_date = explode('-', $new_datetime[0]);

            $new_y = (int) $new_date[0] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[2];
			
            $thai_date = $new_d . '/' . $new_m . '/' . $new_y;
            return $thai_date;
        }
    }
}

if(!function_exists('to_hh_smcl_mm_fdt')){
    function to_hh_smcl_mm_fdt($mysql_datetime){
		$mysql_datetime = trim($mysql_datetime);		
        if(strlen($mysql_datetime) != 19){
            return null;
        }else{
			$new_datetime = explode(' ', $mysql_datetime);
            $new_date = explode('-', $new_datetime[0]);
			
			$new_hhmm = substr($new_datetime[1], 0,2).':'.substr($new_datetime[1], 3,2);
			            					
            return $new_hhmm;
        }
    }
}


if(!function_exists('to_thai_dd_mm_yy_comma_hhss')){
    function to_thai_dd_mm_yy_comma_hhss($mysql_date){
		$mysql_date = trim($mysql_date);		
        if(strlen($mysql_date) != 19){
            return null;
        }else{
			$new_datetime = explode(' ', $mysql_date);
            $new_date = explode('-', $new_datetime[0]);

            $new_y = (int) $new_date[0] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[2];
			if((int)$new_d < 10){
				$new_d = (string)(int)$new_d;
			}
            $thai_date = $new_d . '/' . $new_m . '/' . substr($new_y, 2, 2);
			$new_hhmm = substr($new_datetime[1], 0,2).substr($new_datetime[1], 3,2);
			//replace thai month
			$thai_short_date = '';
			switch($new_m){
				case '01' : $thai_short_date = str_replace("/01/"," ม.ค.",$thai_date); break;
				case '02' : $thai_short_date = str_replace("/02/"," ก.พ.",$thai_date); break;
				case '03' : $thai_short_date = str_replace("/03/"," มี.ค.",$thai_date); break;
				case '04' : $thai_short_date = str_replace("/04/"," เม.ย.",$thai_date); break;
				case '05' : $thai_short_date = str_replace("/05/"," พ.ค.",$thai_date); break;
				case '06' : $thai_short_date = str_replace("/06/"," มิ.ย.",$thai_date); break;
				case '07' : $thai_short_date = str_replace("/07/"," ก.ค.",$thai_date); break;
				case '08' : $thai_short_date = str_replace("/08/"," ส.ค.",$thai_date); break;
				case '09' : $thai_short_date = str_replace("/09/"," ก.ย.",$thai_date); break;
				case '10' : $thai_short_date = str_replace("/10/"," ต.ค.",$thai_date); break;
				case '11' : $thai_short_date = str_replace("/11/"," พ.ย.",$thai_date); break;
				case '12' : $thai_short_date = str_replace("/12/"," ธ.ค.",$thai_date); break;
			}					
            return $thai_short_date.', '.$new_hhmm;
        }
    }
}

if(!function_exists('to_thai_dd_mm_yy_fdt')){
    function to_thai_dd_mm_yy_fdt($mysql_datetime){
		$mysql_datetime = trim($mysql_datetime);		
        if(strlen($mysql_datetime) != 19){
            return null;
        }else{
			$new_datetime = explode(' ', $mysql_datetime);
            $new_date = explode('-', $new_datetime[0]);

            $new_y = (int) $new_date[0] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[2];
			if((int)$new_d < 10){
				$new_d = (string)(int)$new_d;
			}
            $thai_date = $new_d . '/' . $new_m . '/' . substr($new_y, 2, 2);
			//replace thai month
			$thai_short_date = '';
			switch($new_m){
				case '01' : $thai_short_date = str_replace("/01/"," ม.ค.",$thai_date); break;
				case '02' : $thai_short_date = str_replace("/02/"," ก.พ.",$thai_date); break;
				case '03' : $thai_short_date = str_replace("/03/"," มี.ค.",$thai_date); break;
				case '04' : $thai_short_date = str_replace("/04/"," เม.ย.",$thai_date); break;
				case '05' : $thai_short_date = str_replace("/05/"," พ.ค.",$thai_date); break;
				case '06' : $thai_short_date = str_replace("/06/"," มิ.ย.",$thai_date); break;
				case '07' : $thai_short_date = str_replace("/07/"," ก.ค.",$thai_date); break;
				case '08' : $thai_short_date = str_replace("/08/"," ส.ค.",$thai_date); break;
				case '09' : $thai_short_date = str_replace("/09/"," ก.ย.",$thai_date); break;
				case '10' : $thai_short_date = str_replace("/10/"," ต.ค.",$thai_date); break;
				case '11' : $thai_short_date = str_replace("/11/"," พ.ย.",$thai_date); break;
				case '12' : $thai_short_date = str_replace("/12/"," ธ.ค.",$thai_date); break;
			}					
            return $thai_short_date;
        }
    }
}

if(!function_exists('to_thai_dd_mm_yy')){
    function to_thai_dd_mm_yy($mysql_date){
		$mysql_date = trim($mysql_date);
        if(strlen($mysql_date) != 10){
            return null;
        }else{
			//$new_datetime = explode(' ', $mysql_date);
            $new_date = explode('-', $mysql_date);

            $new_y = (int) $new_date[0] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[2];
			if((int)$new_d < 10){
				$new_d = (string)(int)$new_d;
			}
            $thai_date = $new_d . '/' . $new_m . '/' . substr($new_y, 2, 2);
			//$new_hhmm = substr($new_datetime[1], 0,2).substr($new_datetime[1], 3,2);
			//replace thai month
			$thai_short_date = '';
			switch($new_m){
				case '01' : $thai_short_date = str_replace("/01/"," ม.ค.",$thai_date); break;
				case '02' : $thai_short_date = str_replace("/02/"," ก.พ.",$thai_date); break;
				case '03' : $thai_short_date = str_replace("/03/"," มี.ค.",$thai_date); break;
				case '04' : $thai_short_date = str_replace("/04/"," เม.ย.",$thai_date); break;
				case '05' : $thai_short_date = str_replace("/05/"," พ.ค.",$thai_date); break;
				case '06' : $thai_short_date = str_replace("/06/"," มิ.ย.",$thai_date); break;
				case '07' : $thai_short_date = str_replace("/07/"," ก.ค.",$thai_date); break;
				case '08' : $thai_short_date = str_replace("/08/"," ส.ค.",$thai_date); break;
				case '09' : $thai_short_date = str_replace("/09/"," ก.ย.",$thai_date); break;
				case '10' : $thai_short_date = str_replace("/10/"," ต.ค.",$thai_date); break;
				case '11' : $thai_short_date = str_replace("/11/"," พ.ย.",$thai_date); break;
				case '12' : $thai_short_date = str_replace("/12/"," ธ.ค.",$thai_date); break;
			}						
            return $thai_short_date;
        }
    }
}

if(!function_exists('to_thai_thai_dd_mm_yy_comma_hhss')){
    function to_thai_thai_dd_mm_yy_comma_hhss($mysql_date){
		$mysql_date = trim($mysql_date);		
        if(strlen($mysql_date) != 19){
            return null;
        }else{
			$new_datetime = explode(' ', $mysql_date);
            $new_date = explode('-', $new_datetime[0]);

            $new_y = (int) $new_date[0] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[2];
			if((int)$new_d < 10){
				$new_d = (string)(int)$new_d;
			}
            $thai_date = $new_d . '/' . $new_m . '/' . substr($new_y, 2, 2);
			$new_hhmm = substr($new_datetime[1], 0,2).substr($new_datetime[1], 3,2);
			//replace thai month
			$thai_short_date = '';
			switch($new_m){
				case '01' : $thai_short_date = str_replace("/01/"," ม.ค.",$thai_date); break;
				case '02' : $thai_short_date = str_replace("/02/"," ก.พ.",$thai_date); break;
				case '03' : $thai_short_date = str_replace("/03/"," มี.ค.",$thai_date); break;
				case '04' : $thai_short_date = str_replace("/04/"," เม.ย.",$thai_date); break;
				case '05' : $thai_short_date = str_replace("/05/"," พ.ค.",$thai_date); break;
				case '06' : $thai_short_date = str_replace("/06/"," มิ.ย.",$thai_date); break;
				case '07' : $thai_short_date = str_replace("/07/"," ก.ค.",$thai_date); break;
				case '08' : $thai_short_date = str_replace("/08/"," ส.ค.",$thai_date); break;
				case '09' : $thai_short_date = str_replace("/09/"," ก.ย.",$thai_date); break;
				case '10' : $thai_short_date = str_replace("/10/"," ต.ค.",$thai_date); break;
				case '11' : $thai_short_date = str_replace("/11/"," พ.ย.",$thai_date); break;
				case '12' : $thai_short_date = str_replace("/12/"," ธ.ค.",$thai_date); break;
			}			
			$thai_short_date = to_thai_number($thai_short_date.', '.$new_hhmm);			
            return $thai_short_date;
        }
    }
}

if(!function_exists('to_thai_thai_dd_mm_yy_fdt')){
    function to_thai_thai_dd_mm_yy_fdt($mysql_datetime){
		$mysql_datetime = trim($mysql_datetime);		
        if(strlen($mysql_datetime) != 19){
            return null;
        }else{
			$new_datetime = explode(' ', $mysql_datetime);
            $new_date = explode('-', $new_datetime[0]);

            $new_y = (int) $new_date[0] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[2];
			if((int)$new_d < 10){
				$new_d = (string)(int)$new_d;
			}
            $thai_date = $new_d . '/' . $new_m . '/' . substr($new_y, 2, 2);
			//replace thai month
			$thai_short_date = '';
			switch($new_m){
				case '01' : $thai_short_date = str_replace("/01/"," ม.ค.",$thai_date); break;
				case '02' : $thai_short_date = str_replace("/02/"," ก.พ.",$thai_date); break;
				case '03' : $thai_short_date = str_replace("/03/"," มี.ค.",$thai_date); break;
				case '04' : $thai_short_date = str_replace("/04/"," เม.ย.",$thai_date); break;
				case '05' : $thai_short_date = str_replace("/05/"," พ.ค.",$thai_date); break;
				case '06' : $thai_short_date = str_replace("/06/"," มิ.ย.",$thai_date); break;
				case '07' : $thai_short_date = str_replace("/07/"," ก.ค.",$thai_date); break;
				case '08' : $thai_short_date = str_replace("/08/"," ส.ค.",$thai_date); break;
				case '09' : $thai_short_date = str_replace("/09/"," ก.ย.",$thai_date); break;
				case '10' : $thai_short_date = str_replace("/10/"," ต.ค.",$thai_date); break;
				case '11' : $thai_short_date = str_replace("/11/"," พ.ย.",$thai_date); break;
				case '12' : $thai_short_date = str_replace("/12/"," ธ.ค.",$thai_date); break;
			}			
			$thai_short_date = to_thai_number($thai_short_date);			
            return $thai_short_date;
        }
    }
}

if(!function_exists('to_thai_thai_dd_mm_yy')){
    function to_thai_thai_dd_mm_yy($mysql_date){
		$mysql_date = trim($mysql_date);
        if(strlen($mysql_date) != 10){
            return null;
        }else{
			//$new_datetime = explode(' ', $mysql_date);
            $new_date = explode('-', $mysql_date);

            $new_y = (int) $new_date[0] + 543;
            $new_m = $new_date[1];
            $new_d = $new_date[2];
			if((int)$new_d < 10){
				$new_d = (string)(int)$new_d;
			}
            $thai_date = $new_d . '/' . $new_m . '/' . substr($new_y, 2, 2);
			//$new_hhmm = substr($new_datetime[1], 0,2).substr($new_datetime[1], 3,2);
			//replace thai month
			$thai_short_date = '';
			switch($new_m){
				case '01' : $thai_short_date = str_replace("/01/"," ม.ค.",$thai_date); break;
				case '02' : $thai_short_date = str_replace("/02/"," ก.พ.",$thai_date); break;
				case '03' : $thai_short_date = str_replace("/03/"," มี.ค.",$thai_date); break;
				case '04' : $thai_short_date = str_replace("/04/"," เม.ย.",$thai_date); break;
				case '05' : $thai_short_date = str_replace("/05/"," พ.ค.",$thai_date); break;
				case '06' : $thai_short_date = str_replace("/06/"," มิ.ย.",$thai_date); break;
				case '07' : $thai_short_date = str_replace("/07/"," ก.ค.",$thai_date); break;
				case '08' : $thai_short_date = str_replace("/08/"," ส.ค.",$thai_date); break;
				case '09' : $thai_short_date = str_replace("/09/"," ก.ย.",$thai_date); break;
				case '10' : $thai_short_date = str_replace("/10/"," ต.ค.",$thai_date); break;
				case '11' : $thai_short_date = str_replace("/11/"," พ.ย.",$thai_date); break;
				case '12' : $thai_short_date = str_replace("/12/"," ธ.ค.",$thai_date); break;
			}			
			$thai_short_date = to_thai_number($thai_short_date);			
            return $thai_short_date;
        }
    }
}


if (!function_exists('leftStr')) {
    function leftStr($str, $length) {
        return substr($str, 0, $length);
    }
}

if (!function_exists('rightStr')) {
    function rightStr($str, $length) {
        return substr($str, 0, -$length);
    }
}
?>