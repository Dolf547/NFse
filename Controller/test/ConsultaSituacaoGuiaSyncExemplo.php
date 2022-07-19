<?php
ini_set("default_socket_timeout", 60);

define('DS', DIRECTORY_SEPARATOR);

include '../vendor/autoload.php';

use Nfsews\Config;
use Nfsews\Certificate\Certificate;
use Nfsews\Connection;
use Nfsews\Providers\Prodam\V2\Request\PedidoConsultaSituacaoGuiaAsync;


// PARA CERTIFICADO VÁLIDO
$options = [
    'soapOptions' => [
        'ssl'	=>	[
            'cafile'	=>	__dir__ . DS . 'ca_mozilla_2019.pem',

        ]
    ]
];


$config = new Config($options);
$config->setPfxCert(__dir__ . DS . 'moises.pfx');
$config->setPasswordCert('demo1');
$config->setTmpDirectory(__dir__ . DS . 'tmp');
//$config->setXmlDirectory(__dir__ . DS . 'tmp' . DS . 'xml');

$config->setWsdl('https://nfews.prefeitura.sp.gov.br/lotenfeasync.asmx?WSDL');

$certificate = new Certificate($config);

$pedido = new PedidoConsultaSituacaoGuiaAsync();
$pedido->setCpfCnpjRemetente('14254587000148');
$pedido->setNumeroProtocolo('15424578541202545875523334215741');



// Realizar a conexao
$connection = new Connection($config, $certificate);
// Enviar o RPS
$response = $connection->dispatch($pedido);


var_dump( $response);

