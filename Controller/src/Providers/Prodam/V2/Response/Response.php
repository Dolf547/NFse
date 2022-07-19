<?php

namespace Nfsews\Providers\Prodam\V2\Response;


class Response
{
    public $exception = null;
    public $trace = null;
    public $sucesso = false;
    public $xmlEnvio = null;
    public $xmlResposta = null;
    public $erros = [];
    public $alertas = [];

}