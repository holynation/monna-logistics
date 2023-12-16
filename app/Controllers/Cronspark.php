<?php
/**
 * This is a cron job schedule to run at 11:59 UTC+1 as a backup plan incase no client is available
 * thus, we can still be sure that a timestamp would be picked up just at 11:59
 * The server timezone is GMT-4 meaning we are 5hrs ahead that timezone. i.e whatever the current time
 * we are, count 5hrs backward to get equivalent time. 11:59 UTC+1 => 18:59 GMT -4
 * 
 */
namespace App\Controllers;

use CodeIgniter\CLI\CLI;

class Cronspark 
{   
    public static $db;

    public function __construct(){
        helper('string');
        self::$db = db_connect();
    }

    public static function performActions(string $task='track'){
        log_message("info","CRON_START_SPARK running a cron job on the server");
        $data = array(
            'title'=>"Test cron job: {$task}",
            'desciption'=>'This is a cron job test work on the server.'
        );
        self::$db->table('cron_jobs')->insert($data);
        log_message("info",'CRON_END running a cron job on the server');
    }

    // this function is important for the cashout page fetching periodically
    private static function performAction(string $task){
        if($task == 'timestamp'){
            self::processTimestamp();
        }
        else if($task == 'winners'){ // this should be run on the morrow day say 12:15am 
            self::processWinners();
        }
    }

    private static function processTimestamp(){
        $cashback = loadClass('cashback');
        $daily_timestamp = loadClass('daily_timestamp');
        $cronTrack = loadClass('cron_track');

        $data = $cashback->getDailyTimestamp();
        if(!$data){
            // this should not occur, and if it does, it means no data timestamp available.Hence, don't know yet what to do
            $data = $cashback->getDailyTimestamp();
        }
        // log the timestamp to database
        $cashback->logTimeStamp($data['tp_timer'],$data['percentage']);
        // initiate the timer and any coming new timer
        if(!$daily_timestamp->isTimeStampIn('cron_track')){
            $cronTrack->setArray(['timer_id'=>$data['time_order']]);
            $cronTrack->insert();
        }
        CLI::write('cron_finished', 'green');
        return true;
    }

    private static function processWinners(){
        $cashback = loadClass('cashback');
        if(!$cashback->processDailyWinners()){
            log_message('info',"NAIRABOOM_WINNERS_NOT_LOGGED:");
        }
    }
    
    /**
     * This function is called by cron job once in a day at midnight 00:00
     */
    public static function cronJob(string $task='timestamp')
    {
        CLI::write('cron_running', 'light_gray');

        $object = new Cron();
        return self::performAction($task);
    }
}

// here is the script to run the cron job
// php spark nairaboom:cron winners
// php spark nairaboom:cron timestamp

// **************
// /usr/local/bin/php /home/jacashback/public_html/update/index.php cron/cronJob
// curl -s "http://9jacashback.com/update/cron/cronJob"

?>