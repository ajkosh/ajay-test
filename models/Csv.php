<?php

namespace app\models;

use Yii;
use yii\base\Model;
use DateTime;
use yii\helpers\Url;
/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class Csv extends Model
{
  
  public static function generate_csv(){
  	$first=date('Y-m-d');
  	$last='2020-05-01';                                         //this can be change to any date
    $start    = new \DateTime($first);
	$start->modify('first day of this month');
	$end      = new DateTime($last);
	$end->modify('first day of next month');
	$interval = \DateInterval::createFromDateString('1 month');
	$period   = new \DatePeriod($start, $interval, $end);
    $dates=[];
    
	foreach ($period as $key=>$dt) {

         $dates[$key]['bonus']=$dt->format("Y-m-15");
         $dates[$key]['salary']=$dt->format("Y-m-t");
	   
	}
    $csv_column=[];
	$count=count($dates);
	$csv_column=[];
	$date_of_create=date('Ymd');
	$dir= getcwd();
	if(file_exists($dir.'/month.csv')){
	unlink($dir.'/month.csv');
    }
	$cmd='printf "month\tBonus Payment Day\tSalary Payment Day\n">>month.csv';
	shell_exec($cmd); 
	
	for ($i=0; $i < $count; $i++) { 
		$bonus_date=$dates[$i]['bonus'];
		$salary_date=$dates[$i]['salary'];
		$day15th=date('D',strtotime($bonus_date)); 
		$day_last=date('D',strtotime($salary_date)); 
		//bonus day
		if($day15th=='Sun'){
			$new_bonus_date= date('Y-m-d', strtotime($bonus_date. ' + 3 days'));
		}elseif($day15th=='Sat'){
			$new_bonus_date= date('Y-m-d', strtotime($bonus_date. ' + 4 days'));
	    }else{
			$new_bonus_date=$bonus_date;
			
		}

		//salary day
		if($day_last=='Sun'){
			$new_salary_date= date('Y-m-d', strtotime($salary_date. ' + 1 days'));
		}elseif($day_last=='Sat'){
			$new_salary_date= date('Y-m-d', strtotime($salary_date. ' + 2 days'));
		}else{
			$new_salary_date=$salary_date;
		}

        
		$month=escapeshellarg(date('M',strtotime($salary_date))); 
	     putenv("month=$month");
         putenv("bonus_payment_date=$new_bonus_date");
         putenv("bonus_payment_date=$new_salary_date");
         $cmd="echo $month, $new_bonus_date, $new_salary_date >> month.csv";
         exec($cmd); 
	}

  }
}	