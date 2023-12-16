<?php 

namespace App\Controllers;

use App\Models\WebSessionManager;
use App\Models\EntityModel;
use App\Models\WebApiModel;

class Api extends BaseController
{
    /**
     * @var - This is the allowed user type aside the normal users on the endpoint
     */
    const USER_API_TYPE = 'agent';

    /**
     * @deprecated  - This is not in use at the moment
     * @param  string $entity [description]
     * @return [type]         [description]
     */
    public function webApi(string $entity)
    {
        $apiType = null;
        $dictionary = getEntityTranslation();
        $args = array_slice(func_get_args(),1);
        // incase of extra uri args before the real endpoint name
        if($entity == self::USER_API_TYPE){
            $apiType = $this->formatURIType($entity,$args);
        }
        $method = array_key_exists($entity, $dictionary) ? $dictionary[$entity] : $entity;
        $entities = listEntities($this->db); // caching is used here for performance

        // this check if the method is equivalent to any entity model to get it equiv result
        if (in_array($method, $entities)) {
            $entityModel = new EntityModel($this->request,$this->response);
            $entityModel->process($method,$args,$apiType);
            return;
        }

        // define the set of methods in another model called WebApiModel|ApiModel
        $webApiModel = new WebApiModel($this->request,$this->response);
        if (method_exists($webApiModel, $method)) {
            $webApiModel->$method($args, $apiType);
            return;
        }
        else{
            // method no dey exist for this place ooo
            return $this->response->setStatusCode(405)
            ->setJSON(['status'=>false,'message'=>'operation denied']);
        }
        
    }

    /**
     * This is to ensure the URI is format based on the user_type requesting
     * the endpoint without breaking the code
     * 
     * @param string &$entity   This is to pass it by reference just like a pointer
     * @param  array  &$args    This is to pass it by reference just like a pointer
     * @return string
     */
    private function formatURIType(string &$entity, array &$args){
        $entity = $args[0];
        unset($args[0]);

        // reset the array index
        $args = array_values($args);
        return self::USER_API_TYPE;
    }
    
    public function accessFiles(string $directory, string $filename){
        if (!is_dir(WRITEPATH.'uploads/'.$directory)) {
            displayJson(false,"Oops, {$directory} does not exists");return;
        }
        $filename = trim(urldecode($filename));
        $target = WRITEPATH.'uploads/'.urldecode($directory).'/'.$filename;
        $link = "uploads/equipments/{$filename}";
        if(!is_link($link)){
            symlink($target, $link);
        }
    }

}
