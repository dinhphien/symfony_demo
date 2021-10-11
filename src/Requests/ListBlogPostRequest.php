<?php

namespace App\Requests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Range;

class ListBlogPostRequest extends BaseValidatingRequest
{
    #[Range(["min" => 1])]
    private $page;
    #[Range(["min" => 1, "max" => 50])]
    private $limit;
    public function __construct(Request $request, ?UserInterface $user)
    {
        parent::__construct($request, $user);
        $this->page = $request->query->get('page', 1);
        $this->limit = $request->query->get('limit', 10);
    }

    /**
     * Get the value of page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Get the value of limit
     */
    public function getLimit()
    {
        return $this->limit;
    }

    public function authorize(): bool
    {
        return true;
    }
}
