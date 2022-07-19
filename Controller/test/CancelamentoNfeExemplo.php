<?php
ini_set("default_socket_timeout", 60);

define('DS', DIRECTORY_SEPARATOR);

include '../vendor/autoload.php';

use Nfsews\Config;
use Nfsews\Certificate\Certificate;
use Nfsews\Connection;
use Nfsews\Providers\Prodam\V2\Request\PedidoCancelamentoNfe;
use Nfsews\Providers\Prodam\V2\Request\CancelamentoNfeFragmento;


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

$config->setWsdl('https://nfe.prefeitura.sp.gov.br/ws/lotenfe.asmx?WSDL');

$certificate = new Certificate($config);

$pedidoCancelamento = new PedidoCancelamentoNfe();
$pedidoCancelamento->setCpfCnpjRemetente('54654257000195');
$pedidoCancelamento->setTransacao(false);


$nfe = new CancelamentoNfeFragmento();
$nfe->setInscricaoMunicipalPrestador('31000000');
$nfe->setNumeroNfe('21');

$pedidoCancelamento->addCancelamentoNfeFragmento($nfe);
// Realizar a conexao
$connection = new Connection($config, $certificate);
// Enviar o RPS
$response = $connection->dispatch($pedidoCancelamento);


var_dump( $response);