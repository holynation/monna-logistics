<?php
/**
 * This is a cron job schedule to run at 11:59 UTC+1 as a backup plan incase no client is available
 * thus, we can still be sure that a timestamp would be picked up just at 11:59
 * The server timezone is GMT-4 meaning we are 5hrs ahead that timezone. i.e whatever the current time
 * we are, count 5hrs backward to get equivalent time. 11:59 UTC+1 => 18:59 GMT -4
 *
 * AS OF TODAY, IT SEEMS THE CRON JOB IS USING OUR OWN CURRENT TIMEZONE TO RUN
 * 
 */
namespace App\Controllers;

use CodeIgniter\CLI\CLI;

class Cron extends BaseController 
{   
    protected $db;

    public function __construct(){
        helper('string');
        $this->db = db_connect();
    }

    public function performActions(string $task){
        log_message("info","CRON_START running a cron job on the server");
        $data = array(
            'title'=>"Test cron job: {$task}",
            'desciption'=>'This is a cron job test work on the server.'
        );
        $this->db->table('cron_jobs')->insert($data);
        log_message("info",'CRON_END running a cron job on the server');
    }

    // this function is important for the cashout page fetching periodically
    private function performAction(string $task){
        if($task == 'timestamp'){
            $this->processTimestamp();
        }
        else if($task == 'winners'){ // this should be run on the morrow day say 12:15am 
            $this->processWinners();
        }
        else if($task == 'expired'){
            $this->processBonusExpire();
        }
        else if($task == 'giveaway_threshold'){
            // $this->processGiveawayThreshold();
        }
        else if($task == 'boom_points'){
            $this->processBoomPoints();
        }
    }

    private function processTimestamp(){
        $cashback = loadClass('cashback');
        $daily_timestamp = loadClass('daily_timestamp');

        $data = $cashback->getDailyTimestamp();
        if(!$data){
            // this should not occur, and if it does, it means no data timestamp available.
            // Hence, start again
            $data = $cashback->getDailyTimestamp();
        }
        // log the timestamp to database
        $cashback->logTimeStamp($data['tp_timer'],$data['percentage']);
        // initiate the timer and any coming new timer
        if(!$daily_timestamp->isTimeStampIn('cron_track')){
            $builder = $this->db->table('cron_track');
            $builder->insert(['timer_id'=>$data['time_order']]);    
        }
        return true;
    }

    private function processWinners(){
        $cashback = loadClass('cashback');
        if(!$cashback->processDailyWinners()){
            log_message('info',"NAIRABOOM_WINNERS_NOT_LOGGED:");
        }
    }

    private function processBonusExpire(){
        $bonus = loadClass('bonus_wallet');
        $bonus->processBonusExpire();
    }

    private function processGiveawayThreshold(){
        $bonus = loadClass('bonus_wallet');
        $bonus->processGiveawayThreshold();
    }

    private function processBoomPoints(){
        $bonus = loadClass('boom_points');
        $bonus->processHighestBoomPoint();
    }
    
    /**
     * This function is called by cron job once in a day at midnight 00:00
     */
    public function cronJob(string $task='timestamp')
    {
        CLI::write('cron_running', 'green');

        return $this->performAction($task);
    }
}

// here is the script to run the cron job
// php spark nairaboom:cron winners
// php spark nairaboom:cron timestamp

// **************
// /usr/local/bin/php /home/jacashback/public_html/update/index.php cron/cronJob
// curl -s "http://9jacashback.com/update/cron/cronJob"
// /usr/local/bin/php /home/nirabomng/public_html/nairaboom.com.ng/index.php cron cronJob giveaway_threshold

?>