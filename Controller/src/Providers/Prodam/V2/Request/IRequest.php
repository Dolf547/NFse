<?php

namespace Nfsews\Providers\Prodam\V2\Request;


interface IRequest
{
    public function getAction();

    public function getAllAttributes();

    public function toXml();
}