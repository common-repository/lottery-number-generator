<?php
/*
Plugin Name: Lottery Number Generator
Plugin URI: http://plugins.cbnewsplus.com
Description: Lottery Number Generator
Version: 1.3
Author: Cilene Bonfim 
Author URI: http://cbnewsplus.com
*/


define("EUL_DIR",plugins_url().'/lottery-number-generator');

add_action('init', 'lottery_add_scripts');

function lottery_add_scripts() {
		wp_register_style('eul-style', EUL_DIR.'/js/style-t2.css');
		wp_enqueue_style( 'eul-style');
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'eul-script1', EUL_DIR.'/js/jquery.easing.1.3.js', array('jquery'));
		wp_enqueue_script( 'eul-script2', EUL_DIR.'/js/lottery.min.js', array('jquery'));
		
}	

add_action('wp_ajax_eu_action', 'eu_action_callback');
add_action('wp_ajax_nopriv_eu_action', 'eu_action_callback');

function eu_action_callback() {
	$number = get_option('eul_lottery_number');
	$highest = (int) get_option('eul_lottery_highest');
	$main_array = range(1,$highest);
	$json_data = Array();
	$json_key = array_rand($main_array, $number);
	foreach ( $json_key as $value ){
		if ($main_array[$value] > 5){
			$json_data[]= range($main_array[$value]-4,$main_array[$value]);
		}elseif($main_array[$value] == 5){$json_data[]=array(1,2,3,4,5);
		}elseif($main_array[$value] == 4){$json_data[]=array($highest,1,2,3,4);
		}elseif($main_array[$value] == 3){$json_data[]=array($highest-1,$highest,1,2,3);
		}elseif($main_array[$value] == 2){$json_data[]=array($highest-2,$highest-1,$highest,1,2);
		}elseif($main_array[$value] == 1){$json_data[]=array($highest-3,$highest-2,$highest-1,$highest,1);
		}
	}
	echo json_encode($json_data);
	die();
}
class EULottery extends WP_Widget{
	public function EULottery() {
		$widget_ops = array( 
		'classname' => 'eulottery', 
		'description' => __('Lottery Number Generator') );
		$control_ops = array( 
		'width' => 200, 
		'height' => 250, 
		'id_base' => 'eulottery' );
		parent::__construct( 'eulottery', __('Lottery Number Generator'), $widget_ops, $control_ops );
	}
 
	public function form($instance) {

if ( isset( $instance['eul_lottery_number_w'] ) ) {
			$number = $instance['eul_lottery_number_w'];
		}
		else {
			$number = 5;
		}
if ( isset( $instance['eul_lottery_highest_w'] ) ) {
			$highest = $instance['eul_lottery_highest_w'];
		}
		else {
			$highest = 60;
		}
		

	_e("Number"); 
	echo ": <input type='text' size='3' id='".$this->get_field_id('eul_lottery_number_w')."' name='".$this->get_field_name('eul_lottery_number_w')."' value='". $number."' >";
	echo "<br />";
	_e("Highest"); 
	echo ": <input type='text' size='3' id='".$this->get_field_id('eul_lottery_highest_w')."' name='".$this->get_field_name('eul_lottery_highest_w')."' value='".$highest."' >";
	}
 
	public function update($new_instance, $old_instance) {
		return $new_instance;
	}
	public function widget($args, $instance) {
		echo $args['before_widget'],$args['before_title'] .	'' . $args['after_title'];
		$number = $instance['eul_lottery_number_w'];
		$highest = $instance['eul_lottery_highest_w'];
		$t="[eu-lottery  number=".$number."  highest=".$highest."]";
		do_shortcode($t);
		echo $args['after_widget'];
	}
}
add_action('widgets_init', create_function('', 'return register_widget("EULottery");'));


function eu_lottery_f($atts){
	extract( shortcode_atts( array(
			'number' => '5',
			'highest' => '60',
			'position' => 'none'
	), $atts ) );
	



	if (get_option('eul_lottery_number') === FALSE) {
		add_option('eul_lottery_number', $number);
	}else{
		update_option('eul_lottery_number', $number);
	}
	if (get_option('eul_lottery_highest') === FALSE) {
		add_option('eul_lottery_highest', $highest);
	}else{
		update_option('eul_lottery_highest', $highest);
	}
	
	echo "<script type=\"text/javascript\"> var euadminAjaxUrl = '".esc_js(admin_url('admin-ajax.php'))."';</script>";

//position float_left, float_right, middle

switch ($position){
case 'float_left':
	$style1="<div style='float:left;margin:0 16px 0 0;'>";
	$style2="</div>";
	break;
case 'float_right':
	$style1="<div style='float:right;margin:0 0 0 16px'>";
	$style2="</div>";
	break;
case 'middle':
	$style1="<div style='text-align:center;'>";
	$style2="</div>";
	break;

case 'left':
	$style1="<div style='text-align:left;'>";
	$style2="</div>";
	break;

case 'right':
	$style1="<div style='text-align:right;'>";
	$style2="</div>";
	break;


default:
	$style="";
	break;
}


	$code = $style1."<div id='eu-lottery'>"; 

	for($i=0; $i<$number; $i++){
		$code .= "	<div class='slots' id='slots_".$i."'>
				<div class='wrapper'></div>
				</div>
			";
}
$code .= "	<input type='button' value='".__('Pick ticket')."' id='lottery-button'>
		</div>".$style2;
return $code;

}
add_shortcode('eu-lottery', 'eu_lottery_f');

?>