<?php
ini_set("default_socket_timeout", 60);

define('DS', DIRECTORY_SEPARATOR);

include '../vendor/autoload.php';

use Nfsews\Config;
use Nfsews\Certificate\Certificate;
use Nfsews\Connection;
use Nfsews\Providers\Prodam\V2\Request\PedidoEnvioRps;


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


$rps = new PedidoEnvioRps();
$rps->setCpfCnpjRemetente('11578856000157');
//$rps->setCpfCnpjTomador('11.578.856/0001-58');
$rps->setDataEmissaoRps(date('Y-m-d'));
$rps->setInscricaoMunicipalPrestador('31000000');
$rps->setSerieRps('R1');
$rps->setNumeroRps('1');
$rps->setTipoRps('RPS-M');
$rps->setStatusRps('N');
$rps->setTributacaoRps('T');
$rps->setCodigoServico(2658);
$rps->setValorServicos(1400.50);
$rps->setValorDeducoes(50.01);
//$rps->setValorCofins(0);
//$rps->setValorPis(0);
//$rps->setValorCsll(0);
//$rps->setValorIr(0);
//$rps->setValorInss(0);
$rps->setIssRetido(false);
$rps->setAliquotaServicos(0.05);
$rps->setDiscriminacao('adasdasdasdasdasd');


// Realizar a conexao
$connection = new Connection($config, $certificate);
// Enviar o RPS
$response = $connection->dispatch($rps);


var_dump( $response);


?>