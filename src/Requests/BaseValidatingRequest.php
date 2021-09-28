<?php

namespace App\Requests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class BaseValidatingRequest implements ValidateRequestInterface
{
  private $request;
  private $user;
  public function __construct(Request $request, UserInterface $user)
  {
    $this->request = $request;
    $this->user = $user;
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
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the value of user
   */
  public function getUser()
  {
    return $this->user;
  }
}
