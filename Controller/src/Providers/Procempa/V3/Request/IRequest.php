<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 25/05/2019
 * Time: 16:53
 */

namespace Nfsews\Providers\Procempa\V3\Request;


/**
 * Interface IRequest
 * @package Nfsews\Providers\Procempa\V3\Request
 */
interface IRequest
{
    /**
     * @return mixed
     */
    public function getResponseNamespace();

    /**
     * @return mixed
     */
    public function getAction();

    /**
     * @return mixed
     */
    public function getAllAttributes();

    /**
     * @return mixed
     */
    public function toXml();

}