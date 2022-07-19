<?php
ini_set("default_socket_timeout", 60);

define('DS', DIRECTORY_SEPARATOR);

include '../vendor/autoload.php';

use Nfsews\Config;
use Nfsews\Certificate\Certificate;
use Nfsews\Connection;
use Nfsews\Providers\Prodam\V2\Request\RpsFragmento;
use Nfsews\Providers\Prodam\V2\Request\PedidoEnvioLoteRps;

$options = [
    'soapOptions' => [
        'ssl'	=>	[
            'cafile'	=>	__dir__ . DS . 'ca_mozilla_2019.pem'
        ]
    ]
];


$config = new Config($options);
$config->setPfxCert(__dir__ . DS . 'certificado.pfx');
$config->setPasswordCert('password');
$config->setTmpDirectory(__dir__ . DS . 'tmp');

$config->setWsdl('https://nfe.prefeitura.sp.gov.br/ws/lotenfe.asmx?WSDL');


$certificate = new Certificate($config);

$loteRps = new PedidoEnvioLoteRps();

$rps = new RpsFragmento();
//$rps->setAssinatura('assinaturaalzçlkss152zs');
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


$loteRps->addRpsFragmento($rps);

$loteRps->setIndicaTeste(true);
$loteRps->setCpfCnpjRemetente('14256324000145');
$loteRps->setQuantidadeRps(1);
$loteRps->setDataInicio(date('Y-m-d'));
$loteRps->setDataFim(date('Y-m-d'));
$loteRps->setTransacao(false);
$loteRps->setValorTotalServicos(1400.50);
$loteRps->setValorTotalDeducoes(50.01);


// Realizar a conexao
$connection = new Connection($config, $certificate);
$response = $connection->dispatch($loteRps);

// $connection->dispatch($loteRps) Retorna um objeto do tipo Response. Caso queira que seja retornado o xml
// informe $connection->dispatch($loteRps, true);
var_dump( $response);


?>