<?php

namespace App\Requests;
interface ValidateRequestInterface
{
    public function authorize():bool;
}