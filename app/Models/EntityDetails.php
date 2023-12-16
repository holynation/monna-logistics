<?php 

/**
 * this will get different entity details that can be use inside api
 */
namespace App\Models;

class EntityDetails
{
	public function __construct()
    {
        helper('string');
    }

    public function getCustomerDetails(int $id)
    {
        # Get something
        $entity = loadClass('customer');
        $entity->ID = $id;
        $data = $entity->load();
        if(!$data) return false;
        $data= $entity->toArray();
        $customer = getCustomer();
        $result = array();

        $result[] = $temp;
        return $result;   
    }


}




?>