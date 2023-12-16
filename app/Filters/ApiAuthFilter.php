<?php 

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\I18n\Time;

class ApiAuthFilter implements FilterInterface
{
    /**
     * @var - This is the allowed user type aside the normal users on the endpoint
     */
    const USER_API_TYPE = 'agent';

    /**
     * [before description]
     * @param  RequestInterface $request   [description]
     * @param  [type]           $arguments [description]
     * @return [type]                      [description]
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Do something here
        helper(['security','string']);
        $response = service('response');
        if (!$this->validateHeader($request)) {
            $this->logRequest($request);
            return $response->setStatusCode(405)->setJSON(['status'=>false,'message'=>'Authorization denied']);
        }
        $proceed = $this->canProceed($request,$request->getUri()->getSegments());
        if ($proceed === false) {
            $this->logRequest($request);
            return $response->setStatusCode(405)->setJSON(['status'=>false,'message'=>'Authorization denied']);
        }
        else if((int)$proceed === 403){
            $this->logRequest($request);
            return $response->setStatusCode(403)->setJSON(['status'=>false,'message'=>'Oops, user banned']);
        }
        if(!$this->validateUserType($request->getUri()->getSegments())){
            $this->logRequest($request);
            return $response->setStatusCode(422)->setJSON(['status'=>false,'message'=>'Invalid api call']);
        }
        $this->logRequest($request,'1');
    }

    /**
     * [after description]
     * @param  RequestInterface  $request   [description]
     * @param  ResponseInterface $response  [description]
     * @param  [type]            $arguments [description]
     * @return [type]                       [description]
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
        if($request->getUri()->getSegments()[1] === 'fetch_clock'){
            $response->setHeader('Cache-Control','no-store');
            $response->setHeader('Content-Type','text/event-stream');
        }
        else{
            $response->setHeader('Content-Type','application/json');
        }
    }

    /**
     * This is to validate request header
     * @param  object $request [description]
     * @return [type]          [description]
     */
    private function validateHeader(object $request)
    {
        $apiKey = getenv('xAppKey');
        return (array_key_exists('HTTP_X_APP_KEY', $_SERVER) && $request->getServer('HTTP_X_APP_KEY') == $apiKey);
    }

    /**
     * This is to validate request
     * @param  object $request [description]
     * @param  array  $args    [description]
     * @return [type]          [description]
     */
    private function canProceed(object $request,array $args)
    {
        $isExempted = $this->isExempted($request, $args);
        if ($isExempted) {
            return true;
        }
        return $this->validateAPIRequest();
    }

    private function validateAPIRequest(array $args=null)
    {
        try{
            $token = getBearerToken();
            $token = decodeJwtToken($token);
            $decodedToken = $token->data;
            $id = $decodedToken->user_table_id; // the real users_id and any other users
            $userType = $decodedToken->user_type;
            $userType = loadClass($userType);
            $tempUser  = new $userType(array('ID'=>$id));

            if (!$tempUser->load() || !$tempUser->status) {
                return 403; // this would mean that the user is ban
            }

            $newUser = (object)$tempUser->toArray();
            if(isset($decodedToken->user_type)){
                $newUser->user_type = $decodedToken->user_type;
                $newUser->user_id = $decodedToken->ID;
                $newUser->referral_code = $decodedToken->referral_code;
            }
            $_SERVER['current_user'] = $newUser;
            return true;

        }
        catch(\Exception $e){
            return false;
        }
    }

    /**
     * This is to validate the URI,ensuring proper usage of endpoint
     * on a authenticated user
     * @param  array  $arguments [description]
     * @return [type]            [description]
     */
    private function validateUserType(array $arguments){
        if(count($arguments) >= 3){
            $argument = $arguments[1];
            $customer = $_SERVER['current_user'];
            $userType = $customer->user_type;
            if($argument != $userType){
                return false;
            }
        }
        return true;
    }

    /**
     * This is to exempt certain request from the jwt auth
     * @param  object  $request   
     * @param  array   $arguments 
     * @return boolean  
     */
    private function isExempted(object $request,array $arguments)
    {
        $exemptionList = [
            'POST/signup', 'POST/forget_password',
            'POST/change_password', 'POST/auth',
            'POST/logout', 'POST/validate_otp',
            'POST/test', 'POST/auth_remember',
            'POST/validate_sms', 'POST/resend_sms_otp',
            'GET/fetch_clock','GET/lastest_cashback_time',
            'GET/list_daily_winners', 'GET/archives_winners',
            'POST/contact_us', 'GET/boom_number_history',
            'GET/auto_generated_numbers', 'GET/boom_code',
            'GET/app_settings', 'GET/share_ads_image',
        ];
        $argument = $arguments[1];
        if($argument == self::USER_API_TYPE){
            $argument = $arguments[2];
        }
        $argPath = strtoupper($request->getMethod()).'/'.$argument;
        return in_array($argPath, $exemptionList);
    }

    /**
     * This is to track users activity on the platform
     * @deprecated - This method not in use at the moment
     * @param  [type] $request [description]
     * @param  string $status  [description]
     * @return [type]          [description]
     */
    private function logRequest($request, $status = '0')
    {
        $uri =  $request->getUri();
        $uri = '/'.$uri->getPath();
        $db = db_connect();
        $builder = $db->table('audit_log');
        $customer = getCustomer();
        $customer = $customer ? $customer->user_id : null;
        $time = Time::createFromTimestamp($request->getServer('REQUEST_TIME'));
        $time = $time->format('Y-m-d H:i:s');
        $param = [
            'user_id' => $customer,
            'host' => $request->getServer('HTTP_HOST'),
            'url' => $uri,
            'user_agent' => toUserAgent($request->getUserAgent()),
            'ip_address' => $request->getIPAddress(),
            'date_created' => $time,
            'status' => $status,
        ];
        $builder->insert($param);
    }
}