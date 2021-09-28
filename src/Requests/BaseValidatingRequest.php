<?php 

namespace App\Requests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseValidatingRequest implements ValidateRequestInterface
{
    private $request;
    public function __construct(Request $request)
    {
      $this->request = $request;
    }

    /**
     * Get the value of request
     *
     * @return  Request
     */ 
    public function getRequest()
    {
      return $this->request;
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize():bool
    {
      return true;
    }

}