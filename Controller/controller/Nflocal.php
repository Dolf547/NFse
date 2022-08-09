<?php

include('adapted_vendor/nfse/src/Config.php');
include('adapted_vendor/nfse/src/Connection.php');
include('adapted_vendor/nfse/src/Certificate/Certificate.php');
//adapted_vendor\nfse\src\Providers\Prodam\V2\Request
include('adapted_vendor/nfse/src/Providers/Prodam/V2/Request/PedidoEnvioRps.php');
//C:\xampp\htdocs\yoursystem\adapted_vendor\nfse\src\Providers\Procempa\V3\Request
include('adapted_vendor/nfse/src/Providers/Procempa/V3/Request/PedidoGerarNfse.php');
include('adapted_vendor/nfse/src/Providers/Prodim/saovandelino/Pedidoenviarlote.php');

include('adapted_vendor/nfse/src/Providers/Procempa/V3/Request/IRequest.php');
include('adapted_vendor/nfse/src/Providers/Prodam/V2/Request/PedidoCancelamentoNfe.php');
include('adapted_vendor/nfse/src/Providers/Prodam/V2/Request/CancelamentoNfeFragmento.php');

include('adapted_vendor/nfse/src/Providers/Procempa/V3/Request/RpsFragmento.php');

include('adapted_vendor/nfse/src/Providers/Procempa/V3/Request/RpsFragmentoSv.php');

include('adapted_vendor/nfse/src/Providers/Procempa/V3/Request/PedidoConsultarNfseRps.php');
include('adapted_vendor/nfse/src/Providers/Procempa/V3/Request/PedidoConsultarNfseRpsSv.php');
include('adapted_vendor/nfse/src/Providers/Procempa/V3/Request/PedidoCancelarNfse.php');
include('adapted_vendor/nfse/src/Providers/Procempa/V3/Request/PedidoCancelarNfseSv.php');
include(getCwd() . '/adapted_vendor/nfse/vendor/autoload.php');

use Nfsews\Config;
use Nfsews\Connection;
use Nfsews\Certificate\Certificate;
use Utils\System\Component\NF\NFe4;

//SAO VANDELINO
use  Nfsews\Providers\Prodim\saovandelino\Pedidoenviarlote;
use Nfsews\Providers\Procempa\V3\Request\RpsFragmentoSv;



//São Paulo
use Nfsews\Providers\Prodam\V2\Request\PedidoEnvioRps;
use Nfsews\Providers\Prodam\V2\Request\PedidoConsultaNfe;
use Nfsews\Providers\Prodam\V2\Request\PedidoConsultaCnpj;
use Nfsews\Providers\Prodam\V2\Request\PedidoCancelamentoNfe;
use Nfsews\Providers\Prodam\V2\Request\CancelamentoNfeFragmento;
use Nfsews\Providers\Prodam\V2\Request\PedidoConsultaNfeRecebida;
//Porto Alegre
use Nfsews\Providers\Procempa\V3\Request\RpsFragmento;
use Utils\System\Database\Enum\StatusNfAutorizadaEnum;
use Utils\System\Database\Enum\StatusNfTransmitidaEnum;
use Nfsews\Providers\Procempa\V3\Request\PedidoGerarNfse;
use Nfsews\Providers\Procempa\V3\Request\PedidoConsultarNfseRps;
use Nfsews\Providers\Procempa\V3\Request\PedidoConsultarNfseRpsSv;
use NFePHP\Common\Certificate as otherCertificate;
use Nfsews\Providers\Procempa\V3\Request\PedidoCancelarNfseSv;
use Nfsews\Providers\Procempa\V3\Request\PedidoCancelarNfse;
use Symfony\Component\CssSelector\Node\FunctionNode;
use Utils\System\Component\NF\NFe4Danfe;

ini_set("default_socket_timeout", 60);
define('DS', DIRECTORY_SEPARATOR);

class nflocal extends YS_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('number');
		$this->company = $this->Empresas_model->getById($_SESSION['id_empresa']);
		$this->caminhosXml = getCwd() . '/public/clientes/' . $_SESSION['id_empresa'] . '/nfe/';
		$this->sefaz_ambiente = (isset($this->company->sefaz_ambiente) && $this->company->sefaz_ambiente == 1) ? 1 : 2;
		$this->loadModel([
			'NFSaida_model',
			'Estados_model',
			'Produtos_model',
			'Clientes_model',
			'Empresas_model',
			'Recursos_model',
			'Negociacao_model',
			'Vendedores_model',
			'NFSaidaItens_model',
			'Numerosdeserie_model',
			'Contasareceber_model',
			'Ordensdeservico_model',
			'CatalogosProdutos_model',
			'Condicoesdepagamento_model',
			'ListaServicos_model',
			'Sistema_model',
			'LogExclusao_model',
			'MovimentoComissoes_model',
			'Movimentosbancarios_model',
			'Ordensdeservico_model'
		]);
	}
	public function index($alert = '')
	{
		if (!empty($alert)) {
			$this->session->keep_flashdata('alert');
		}
		$invoiceDate = null;
		if (!empty($_POST['invoice_date'])) {
			$invoiceDate = \DateTime::createFromFormat('d/m/Y', $_POST['invoice_date']);
		}
		$invoiceDateUntil = null;
		if (!empty($_POST['invoice_date_until'])) {
			$invoiceDateUntil = \DateTime::createFromFormat('d/m/Y', $_POST['invoice_date_until']);
		}
		$data_de_emi = !empty($this->input->post('data_de_emi')) ? dataBRparaUS($this->input->post('data_de_emi')) : date('Y-01-01');
		$data_ate_emi = !empty($this->input->post('data_ate_emi')) ? dataBRparaUS($this->input->post('data_ate_emi')) : date('Y-m-d');
		$nfs = $this->NFSaida_model->getNfse(
			$this->input->post('document_number'),
			$this->input->post('client_name'),
			$invoiceDate,
			$invoiceDateUntil,
			$this->input->post('serial_number'),
			$this->input->post('authorized_status'),
			$this->input->post('transmitted_status'),
			$this->input->post('type'),
			null,
			$data_de_emi,
			$data_ate_emi,
			$this->input->get('order')
		);
		$customSegments[1] = 'Monitor de NFSe';
		$data['nfs'] = $nfs;
		$data['customSegments'] = $customSegments;
		$data['numserie'] = $this->Ordensdeservico_model->getNumSeries();
		$this->view('nf_saida.monitor2_nfse', $data);
	}

	public function abrirResumoVenda()
	{
		$id_nf_saida = $_POST['id_nf_saida'];
		$cabecalho = $this->NFSaida_model->getResumoVenda($id_nf_saida);

		if ($cabecalho) {
			$data['cabecalho'] = $cabecalho;
		}
		$itens = $this->NFSaida_model->getResumoVendaItens($id_nf_saida);
		if ($itens) {
			$data['itens'] = $itens;
		}
		$a = $this->Ordensdeservico_model->getById($cabecalho->id_negociacao);
		$this->view('nf_saida/resumo_nf', $data);
	}


	public function verXML($id, $tipo)
	{
		$company = $this->company;
		$nf = $this->NFSaida_model->getByID($id);

		if (empty($nf->xml_recebido)) {
			exit('Problemas ao efetuar leitura do XML!');
		}

		if ($company->municipio_empresa == 'São Vendelino') {
			if(empty($nf->consultasv)){
				$this->session->set_flashdata('alert', [
					'danger',
					'Consulte a nota primeiro!'
				]);
				redirect('Nflocal');
			}
			$url = $nf->xml_recebido;
			$arquivo = file_get_contents($url);
			$oXML = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $arquivo);
			$oXML = new SimpleXMLElement($oXML);
			header('Content-type: text/xml');
			echo $oXML->asXML();
			exit;
		} elseif ($company->municipio_empresa == 'Porto Alegre') {
			$url = $nf->xml_recebido;
			$arquivo = file_get_contents($url);
			$oXML = new SimpleXMLElement($arquivo);
			header('Content-type: text/xml');
			echo $oXML->asXML();
			exit;
		}
	}





	public function transmitir($idNota)
	{
		$company = $this->company;
		if ($company->municipio_empresa == "Porto Alegre") {
			$this->envioRpsPOA($idNota);
		} elseif ($company->municipio_empresa == "São Vendelino") {
			$this->enviarsv($idNota);
		} else {
			$this->session->set_flashdata('alert', [
				'warning',
				'Múnicipio da sua empresa não disponível para envio da NFSE!'
			]);
			redirect('ordensdeservico');
		}
	}
	public function consultar($idNota)
	{
		$company = $this->company;
		if ($company->municipio_empresa == "Porto Alegre") {
			$this->consultaRpsPOA($idNota);
		} elseif ($company->municipio_empresa == "São Vendelino") {
			$this->consultarSv($idNota);
		} else {
			$this->session->set_flashdata('alert', [
				'warning',
				'Múnicipio da sus empresa não disponível para envio da NFSE!'
			]);
			redirect('ordensdeservico');
		}
	}
	public function cancelar($idNota)
	{
		$company = $this->company;

		if ($company->municipio_empresa == "Porto Alegre") {
			$this->excluir($idNota, 'Porto Alegre');
		} elseif ($company->municipio_empresa == "São Vendelino") {
			$this->excluir($idNota, 'São Vendelino');
		} else {
			$this->session->set_flashdata('alert', [
				'warning',
				'Múnicipio da sua empresa não disponível para envio da NFSE!'
			]);
			redirect('nflocal');
		}
	}


	public function spenvia($id)
	{

		$response = "";
		$company = $this->company;
		$certificadoDigital = $this->caminhosXml . 'certificado.pfx';
		$senhaCertificado = $company->senhacertificadonfe;
		$cnpj = str_replace(['.', '/', '-', ' '], '', $company->cnpj_empresa);
		$im = str_replace(['.', '/', '-', ' '], '', $company->im_empresa);
		$options = [
			'soapOptions' => [
				'ssl'   =>  [
					'cafile'    =>  __dir__ . DS . 'ca_mozilla_2019.pem',
				]
			]
		];
		$config = new Config($options);
		$config->setPfxCert($certificadoDigital);
		$config->setPasswordCert($senhaCertificado);
		$config->setTmpDirectory(__dir__ . DS . 'tmp');
		if ($this->sefaz_ambiente == 1) {
			//	$config->setWsdl('http://nfe.portoalegre.rs.gov.br/nfe-ws');
		} else {
			$config->setWsdl('https://nfe.prefeitura.sp.g
			ov.br/ws/lotenfe.asmx?WSDL');
		}

		$certificate = new Certificate($config);
		$dadosNF        = $this->NFSaida_model->getByID($id);
		$dadosCliente   = $this->Clientes_model->getById($dadosNF->id_cliente);
		$dadosItensNF   = $this->NFSaidaItens_model->getItemsByIdNegotiation($id);
		$aEmpresa = $this->Sistema_model->getEmpresa();
		$dadosUF        = $this->Estados_model->getStateById($dadosCliente->estado);
		$vowels = array(".", "/", "-");
		$tomadorcpf = str_replace($vowels, "", $dadosCliente->documento);
		$inscricao_mun = str_replace(['.', '/', '-', ' '], '', $company->inscricao_municipal);


		/* 	$pedido = new PedidoGerarNfse();
		$pedido->setNumeroLote(1);
		$pedido->setCpfCnpjPrestador($cnpj);
		$pedido->setInscricaoMunicipalPrestador($im); */
		$rps = new PedidoEnvioRps();
		//dados do tomador
		$rps->setCpfCnpjRemetente($cnpj);
		$rps->setCpfCnpjTomador($tomadorcpf);
		$rps->setDataEmissaoRps(date('Y-m-d'));
		$rps->setInscricaoMunicipalPrestador('31000000');
		$rps->setSerieRps($dadosNF->serie_documento);
		$rps->setNumeroRps('1');
		$rps->setTipoRps('RPS-M');
		$rps->setStatusRps('N');
		$rps->setTributacaoRps('T');

		//T igual a SP

		$rps->setValorDeducoes(50.01);
		$rps->setValorCofins(0);
		$rps->setValorPis(0);
		$rps->setValorCsll(0);
		$rps->setValorIr(0);
		$rps->setValorInss(0);
		$rps->setIssRetido(false);
		$rps->setAliquotaServicos(0.05);
		$i = '';
		if ($dadosItensNF != null) {
			foreach ($dadosItensNF as $product) {

				$productDatabase  = $this->Produtos_model->getByIdFull($product->id_produto, 0);
				$produtocodigo = $this->ListaServicos_model->getListaServicoByCodigo($productDatabase->codigo_issqn);

				if (empty($productDatabase->codigo_issqn)) {
					$this->session->set_flashdata('alert', ['danger', 'Serviço sem o código do ISS cadastrado. Entre no cadastro do produto informe o código e tente transmitir novamente!']);
					redirect('nflocal');
				}
				//	$productListaServico = $this->Produtos_model->getListaServicoByCodigo($productDatabase->codigo_issqn);
				/* if(empty($productListaServico)){
					$this->session->set_flashdata('alert', ['danger', 'Código de ISSQN não encontrado na lista de serviços!']);
					redirect('nflocal');
				}  */
				$total = '';
				$total .= "$product->nome; ";
				//var_dump($rps->setDiscriminacao($total));
				$rps->setValorDeducoes($dadosNF->desconto);
				$rps->setValorCofins(0); //
				$rps->setValorPis(0); //
				$rps->setValorCsll(0); //
				$rps->setValorIr(0);
				$rps->setIssRetido(0);
				$rps->setAliquotaServicos(0);
				$rps->setValorServicos($dadosNF->valor_nota);



				//	$liquido = $product->total - $dadosNF->desconto;

				$rps->setDiscriminacao($total);
				$rps->setCodigoServico($produtocodigo->subitem_lista_servico);
				//var_Dump($produtocodigo);

				$i++;
			}
		}


		$connection = new Connection($config, $certificate);
		// Enviar o RPS
		$response = $connection->dispatch($rps);
		var_dump($response);
	}


	public function cancelametoRps()
	{
		$company = $this->company;
		$certificadoDigital = $this->caminhosXml . 'certificado.pfx';
		$senhaCertificado = $company->senhacertificadonfe;
		$cnpj = str_replace(['.', '/', '-', ' '], '', $company->cnpj_empresa);
		$inscricao_mun = str_replace(['.', '/', '-', ' '], '', $company->inscricao_municipal);
		$options = [
			'soapOptions' => [
				'ssl'   =>  [
					'cafile'    =>  __dir__ . DS . 'ca_mozilla_2019.pem',
				]
			]
		];
		$config = new Config($options);
		$config->setPfxCert($certificadoDigital);
		$config->setPasswordCert($senhaCertificado);
		$config->setTmpDirectory(__dir__ . DS . 'tmp');
		$config->setWsdl('https://nfe.prefeitura.sp.gov.br/ws/lotenfe.asmx?WSDL');
		$certificate = new Certificate($config);
		$pedidoCancelamento = new PedidoCancelamentoNfe();
		$pedidoCancelamento->setCpfCnpjRemetente($cnpj);
		$pedidoCancelamento->setTransacao(false);
		$nfe = new CancelamentoNfeFragmento();
		$nfe->setInscricaoMunicipalPrestador($inscricao_mun);
		$nfe->setNumeroNfe('1');
		$pedidoCancelamento->addCancelamentoNfeFragmento($nfe);
		// Realizar a conexao
		$connection = new Connection($config, $certificate);
		// Enviar o RPS
		$response = $connection->dispatch($pedidoCancelamento);
	}


	public function envioRpsFeliz($id)
	{

		$response = "";
		$company = $this->company;
		$certificadoDigital = $this->caminhosXml . 'certificado.pfx';
		$senhaCertificado = $company->senhacertificadonfe;
		$cnpj = str_replace(['.', '/', '-', ' '], '', $company->cnpj_empresa);
		$im = str_replace(['.', '/', '-', ' '], '', $company->im_empresa);
		$options = [
			'soapOptions' => [
				'ssl'   =>  [
					'cafile'    =>  __dir__ . DS . 'ca_mozilla_2019.pem',
				]
			]
		];

		$config = new Config($options);
		$config->setPfxCert($certificadoDigital);
		$config->setPasswordCert($senhaCertificado);
		$config->setTmpDirectory(__dir__ . DS . 'tmp');
		if ($this->sefaz_ambiente == 1) {
			$config->setWsdl('http://bomprincipio.nfse-tecnos.com.br:9091');
		} else {

			//$config->setWsdl('http://picadacafe-portais.govcloud.com.br/NFSe.Portal.Integracao.Teste/Services.svc?singleWsdl');
			$config->setWsdl('http://feliz-portais.govcloud.com.br/NFSe.Portal.Integracao/Services.svc?singleWsdl');
		}

		$certificate = new Certificate($config);
		$dadosNF        = $this->NFSaida_model->getByID($id);
		$dadosCliente   = $this->Clientes_model->getById($dadosNF->id_cliente);
		$dadosItensNF   = $this->NFSaidaItens_model->getItemsByIdNegotiation($id);
		$aEmpresa = $this->Sistema_model->getEmpresa();
		$dadosUF        = $this->Estados_model->getStateById($dadosCliente->estado);
		$rps = new PedidoGerarNfseProdim();
		//dados do tomador
		$rps->setCpfCnpjRemetente($cnpj);
		$rps->setCpfCnpjTomador($tomadorcpf);
		$rps->setDataEmissaoRps(date('Y-m-d'));
		$rps->setInscricaoMunicipalPrestador('31000000');
		$rps->setSerieRps($dadosNF->serie_documento);
		$rps->setNumeroRps('1');
		$rps->setTipoRps('RPS-M');
		$rps->setStatusRps('N');
		$rps->setTributacaoRps('T');

		//T igual a SP

		$rps->setValorDeducoes(50.01);
		$rps->setValorCofins(0);
		$rps->setValorPis(0);
		$rps->setValorCsll(0);
		$rps->setValorIr(0);
		$rps->setValorInss(0);
		$rps->setIssRetido(false);
		$rps->setAliquotaServicos(0.05);
		$connection = new Connection($config, $certificate);
		// Enviar o RPS
		$response = $connection->dispatch($rps);
		var_dump($response);
	}






	public function envioRpsPOA($id)
	{
		$response = "";
		$company = $this->company;
		$certificadoDigital = $this->caminhosXml . 'certificado.pfx';
		$senhaCertificado = $company->senhacertificadonfe;
		$cnpj = str_replace(['.', '/', '-', ' '], '', $company->cnpj_empresa);
		$im = str_replace(['.', '/', '-', ' '], '', $company->im_empresa);
		$options = [
			'soapOptions' => [
				'ssl'   =>  [
					'cafile'    =>  __dir__ . DS . 'ca_mozilla_2019.pem',
				]
			]
		];
		$config = new Config($options);
		$config->setPfxCert($certificadoDigital);
		$config->setPasswordCert($senhaCertificado);
		$config->setTmpDirectory(__dir__ . DS . 'tmp');
		if ($this->sefaz_ambiente == 1) {
			$config->setWsdl('http://nfe.portoalegre.rs.gov.br/nfe-ws');
		} else {
			$config->setWsdl('https://nfse-hom.procempa.com.br/bhiss-ws/nfse?wsdl');
		}
		$certificate = new Certificate($config);
		$dadosNF        = $this->NFSaida_model->getByID($id);
		$dadosCliente   = $this->Clientes_model->getById($dadosNF->id_cliente);
		$dadosItensNF   = $this->NFSaidaItens_model->getItemsByIdNegotiation($id);
		$aEmpresa = $this->Sistema_model->getEmpresa();
		$dadosUF        = $this->Estados_model->getStateById($dadosCliente->estado);
		$pedido = new PedidoGerarNfse();
		$pedido->setNumeroLote(1);
		$pedido->setCpfCnpjPrestador($cnpj);
		$pedido->setInscricaoMunicipalPrestador($im);
		$rps = new RpsFragmento();
		//dados do tomador
		$rps->setStatus(1);
		$rps->setTipoRps(1);
		$rps->setUfTomador($dadosUF);
		$rps->setCpfCnpjPrestador($cnpj);
		$rps->setIncentivadorCultural(2);
		$rps->setOptanteSimplesNacional($aEmpresa->simplesnacional_empresa);
		$rps->setInscricaoMunicipalPrestador($im);
		$rps->setDataEmissao(date('Y-m-d\TH:i:s'));
		$rps->setSerieRps($dadosNF->serie_documento);
		$rps->setEnderecoTomador($dadosCliente->rua);
		$rps->setBairroTomador($dadosCliente->bairro);
		$rps->setNumeroRps($dadosNF->numero_documento);
		$vowels = array(".", "/", "-");
		$tomadorcpf = str_replace($vowels, "", $dadosCliente->documento);
		$rps->setCodigoMunicipioTomador($dadosCliente->ibge);
		$rps->setNumeroEnderecoTomador($dadosCliente->numero);
		$rps->setCodigoMunicipioPrestacao($dadosCliente->ibge);
		$razao = preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/", "/&/"), explode(" ", "a A e E i I o O u U n N"), $dadosCliente->razaosocial);
		$rps->setRazaoSocialTomador($razao);
		$rps->setCepTomador(str_replace(['.', '/', '-', ' '], '', $dadosCliente->cep));
		$rps->setCpfCnpjTomador(str_replace(['.', '/', '-', ' '], '', $tomadorcpf));
		$rps->setRegimeEspecialTributacao($aEmpresa->regimeespecial_empresa);
		$certVal = $certValObj = null;
		$cert = 'public/clientes/' . $this->Sistema_model->getEmpresa()->id_empresa . '/nfe/certificado.pfx';

		if (file_exists($cert)) {
			$certVal = true;
			try {
				$certO = otherCertificate::readPfx(file_get_contents($cert), $aEmpresa->senhacertificadonfe);
				$certValObj = $certO->getValidTo();
				$certVal = !empty($certValObj) ? $certValObj->format("U") : $certVal;
			} catch (Exception $e) {
				$certErr = $e->getMessage();
			}
		}

		if (empty($certVal)) {
			$a = "Você ainda não enviou seu certificado";
			$arr = array(
				'status_autorizada' => StatusNfAutorizadaEnum::NAO_AUTORIZADA,
				'status_transmitida' => StatusNfTransmitidaEnum::NAO_TRANSMITIDA,
				//'chave_nfe' => $response->listaNfse[0]["numero"]->textContent,
				//'numeroprefeitura' => $response->listaNfse[0]["numero"]->textContent,
				//'protocolo' =>  $response->listaNfse[0]["codigoVerificacao"]->textContent,
				'mensagem_nota' => $a,
			);
			$this->db->where('id', $id);
			$this->db->update('nf_saida', $arr);
			$this->session->set_flashdata('alert', 'danger', $a);
			/* redirect('nflocal'); */
		} else if (empty($certErr) && $certVal < time()) {
			$a = "Seu certificado venceu em " . date("d/m/Y \à\s H:i:s", $certVal);
			$arr = array(
				'status_autorizada' => StatusNfAutorizadaEnum::NAO_AUTORIZADA,
				'status_transmitida' => StatusNfTransmitidaEnum::NAO_TRANSMITIDA,
				//'chave_nfe' => $response->listaNfse[0]["numero"]->textContent,
				//'numeroprefeitura' => $response->listaNfse[0]["numero"]->textContent,
				//'protocolo' =>  $response->listaNfse[0]["codigoVerificacao"]->textContent,
				'mensagem_nota' => $a,
			);
			$this->db->where('id', $id);
			$this->db->update('nf_saida', $arr);

			$this->session->set_flashdata('alert', 'danger', $a);

			/* redirect('nflocal'); */
		}




		if ($dadosCliente->cidade != $company->municipio_empresa) {
			$rps->setNaturezaOperacao(2);
		} else {
			$rps->setNaturezaOperacao(1);
		}
		$i = 1;


		if ($dadosItensNF != null) {
			foreach ($dadosItensNF as $product) {
				if ($product->tipo != 'SE') {
					$this->session->set_flashdata('alert', ['danger', 'O produto ' . $product->nome . ' não é um serviço!']);
					redirect('nflocal');
				}
				$productDatabase  = $this->Produtos_model->getByIdFull($product->id_produto, 0);
				$produtocodigo = $this->ListaServicos_model->getListaServicoByCodigo($productDatabase->codigo_issqn);
				if (empty($productDatabase->codigo_issqn)) {
					$this->session->set_flashdata('alert', ['danger', 'Serviço sem o código do ISS cadastrado. Entre no cadastro do produto informe o código e tente transmitir novamente!']);
					redirect('nflocal');
				}

				//	$total = '';
				$total .= "$product->nome; ";
				//var_dump($rps->setDiscriminacao($total));

				$liquido = $product->total - $dadosNF->desconto;
				$rps->setValorServicos($dadosNF->valor_nota);
				$rps->setdescontoIncondicionado($dadosNF->desconto);
				$rps->setvalorLiquidoNfe($liquido);
				$rps->setBaseCalculo($product->id_produto);
				$rps->setAliquota($produtocodigo->aliquota);
				$rps->setDiscriminacao($total);
				$rps->setItemListaServico($produtocodigo->subitem_lista_servico);
				//var_Dump($produtocodigo);
				$rps->setValorIss($product->total * ($produtocodigo->aliquota / 100));
				$rps->setCodigoTributacaoMunicipio(trim($productDatabase->codigo_issqn));

				$i++;
			}
		}
		//die;

		//var_dump($product->id_produto); die;
		// Os campos preenchidos acima são apenas os obrigatórios do ponto de vista estrutural do XML no entanto você deve
		// continuar preenchendo até satisfazer todas as informações que a Prefeitura de Porto Alegre exige
		$pedido->addFragmento($rps);
		// Realizar a conexao
		$connection = new Connection($config, $certificate);
		// Enviar o RPS
		$response = $connection->dispatch($pedido);


		if ($response->listaNfse[0]["outrasInformacoes"]->textContent == null) {
			$response->listaNfse[0]["outrasInformacoes"]->textContent = 'RPS Autorizada com sucesso';
		}


		$caminhosXml = getCwd() . '/public/clientes/' . $_SESSION['id_empresa'] . '/nfe/';

		if (count($response->erros) > 0) {
			$mensagem = "";
			foreach ($response->erros as $_att) {
				$mensagem .= $_att["codigo"] . ": " . $_att["mensagem"];
			}

			$arr = array(
				'mensagem_nota' => $mensagem,
				'xml_enviado' => $caminhosXml . $response->xmlEnvio,
				'xml_recebido' => $caminhosXml . $response->xmlResposta,
				'status_autorizada' => StatusNfAutorizadaEnum::NAO_AUTORIZADA,
				'status_transmitida' => StatusNfTransmitidaEnum::NAO_TRANSMITIDA,
			);
			$this->db->where('id', $id);
			$this->db->update('nf_saida', $arr);
			$this->session->set_flashdata('alert', ['danger', $mensagem]);

			redirect('nflocal');
		} else {

			$arr = array(
				'xml_enviado' => $caminhosXml . $response->xmlEnvio,
				'xml_recebido' => $caminhosXml . $response->xmlResposta,
				'status_autorizada' => StatusNfAutorizadaEnum::AUTORIZADA,
				'status_transmitida' => StatusNfTransmitidaEnum::TRANSMITIDA,
				'numeroprefeitura' => $response->listaNfse[0]["numero"]->textContent,
				'chave_nfe' => $response->listaNfse[0]["numero"]->textContent,
				'protocolo' => $response->listaNfse[0]["codigoVerificacao"]->textContent,
				'mensagem_nota' => $response->listaNfse[0]["outrasInformacoes"]->textContent,
			);
			$this->db->where('id', $id);
			$this->db->update('nf_saida', $arr);

			if ($company->nfseauto == 's') {
				$this->imprimirRPS($id, 's');
			}

			$this->session->set_flashdata('alert', ['success', "NFSe transmitida com sucesso!"]);
			redirect('nflocal');
		}
		//	ECHO "OI";
		//echo $response->asXML();
		redirect('nflocal');
	}
	public function consultaRpsPOA($id)
	{
		$company = $this->company;
		$dadosNF = $this->NFSaida_model->getByID($id);
		$certificadoDigital = $this->caminhosXml . 'certificado.pfx';
		$senhaCertificado = $company->senhacertificadonfe;




		$cnpj = str_replace(['.', '/', '-', ' '], '', $company->cnpj_empresa);
		$im = str_replace(['.', '/', '-', ' '], '', $company->im_empresa);
		$options = [
			'soapOptions' => [
				'ssl'   =>  [
					'cafile'    =>  __dir__ . DS . 'ca_mozilla_2019.pem',
				]
			]
		];
		$numero_rps = str_pad($dadosNF->numero_documento, 11, '0', STR_PAD_LEFT);
		$config = new Config($options);
		$config->setPfxCert($certificadoDigital);
		$config->setPasswordCert($senhaCertificado);
		$config->setTmpDirectory(__dir__ . DS . 'tmp');
		if ($this->sefaz_ambiente == 1) {
			$config->setWsdl('http://nfe.portoalegre.rs.gov.br/nfe-ws');
		} else {
			$config->setWsdl('https://nfse-hom.procempa.com.br/bhiss-ws/nfse?wsdl');
		}
		$certificate = new Certificate($config);

		$pedido = new PedidoConsultarNfseRps();
		$pedido->setTipoRps(1);
		$pedido->setNumeroRps($numero_rps);
		$pedido->setCpfCnpjPrestador($cnpj);
		$pedido->setInscricaoMunicipalPrestador($im);
		$pedido->setSerieRps($dadosNF->serie_documento);
		// Realizar a conexao
		$connection = new Connection($config, $certificate);
		// Enviar o RPS
		$response = $connection->dispatch($pedido);
	

		$aEmpresa = $this->Sistema_model->getEmpresa();
		$certVal = $certValObj = null;
		$cert = 'public/clientes/' . $this->Sistema_model->getEmpresa()->id_empresa . '/nfe/certificado.pfx';


		/* if (file_exists($cert)) {
			$certVal = true;
			try {
				$certO = otherCertificate::readPfx(file_get_contents($cert), $aEmpresa->senhacertificadonfe);
				$certValObj = $certO->getValidTo();
				$certVal = !empty($certValObj) ? $certValObj->format("U") : $certVal;
			} catch (Exception $e) {
				$certErr = $e->getMessage();
			}
		}

		if (empty($certVal)) {
			$a = "Você ainda não enviou seu certificado";
			$arr = array(
				'status_autorizada' => StatusNfAutorizadaEnum::NAO_AUTORIZADA,
				'status_transmitida' => StatusNfTransmitidaEnum::NAO_TRANSMITIDA,
				//'chave_nfe' => $response->listaNfse[0]["numero"]->textContent,
				//'numeroprefeitura' => $response->listaNfse[0]["numero"]->textContent,
				//'protocolo' =>  $response->listaNfse[0]["codigoVerificacao"]->textContent,
				'mensagem_nota' => $a,
			);
			$this->db->where('id', $id);
			$this->db->update('nf_saida', $arr);

			$this->session->set_flashdata('alert', ['danger', $a]);

			redirect('nflocal');
		} else if (empty($certErr) && $certVal < time()) {
			$a = "Seu certificado venceu em " . date("d/m/Y \à\s H:i:s", $certVal);
			$arr = array(
				'status_autorizada' => StatusNfAutorizadaEnum::NAO_AUTORIZADA,
				'status_transmitida' => StatusNfTransmitidaEnum::NAO_TRANSMITIDA,
				//'chave_nfe' => $response->listaNfse[0]["numero"]->textContent,
				//'numeroprefeitura' => $response->listaNfse[0]["numero"]->textContent,
				//'protocolo' =>  $response->listaNfse[0]["codigoVerificacao"]->textContent,
				'mensagem_nota' => $a,
			);
			$this->db->where('id', $id);
			$this->db->update('nf_saida', $arr);

			$this->session->set_flashdata('alert', ['danger', $a]);

			redirect('nflocal');
		} else {
			$a = "Certificado enviado, mas não foi possível ler a data de vencimento";
			$arr = array(
				'status_autorizada' => StatusNfAutorizadaEnum::NAO_AUTORIZADA,
				'status_transmitida' => StatusNfTransmitidaEnum::NAO_TRANSMITIDA,
				//'chave_nfe' => $response->listaNfse[0]["numero"]->textContent,
				//'numeroprefeitura' => $response->listaNfse[0]["numero"]->textContent,
				//'protocolo' =>  $response->listaNfse[0]["codigoVerificacao"]->textContent,
				'mensagem_nota' => $a,
			); */
		/* $this->db->where('id', $id);
			$this->db->update('nf_saida', $arr);

			$this->session->set_flashdata('alert', ['danger', $a]);

			redirect('nflocal');
		}; */



		if ($response->listaNfse[0]["outrasInformacoes"]->textContent == null) {
			$response->listaNfse[0]["outrasInformacoes"]->textContent = 'RPS Autorizada com sucesso';
		}


		if (count($response->erros) > 0) {
			$mensagem = "";
			foreach ($response->erros as $_att) {
				$mensagem .= $_att["codigo"] . ": " . $_att["mensagem"];
			}
			$this->session->set_flashdata('alert', ['danger', $mensagem]);
			redirect('nflocal');
		} else {
			if ($dadosNF->cancelada == 'S') {
				$this->session->set_flashdata('alert', ['success', 'Solicitação de cancelamento processada']);
				$arr = array(
					'mensagem_nota' => 'Solicitação de cancelamento processada',
					'chave_nfe' => $response->listaNfse[0]["numero"]->textContent
				);
				$this->db->where('id', $id);
				$this->db->update('nf_saida', $arr);
				redirect('nflocal');
			}

			$arr = array(
				'status_autorizada' => StatusNfAutorizadaEnum::AUTORIZADA,
				'status_transmitida' => StatusNfTransmitidaEnum::TRANSMITIDA,
				'chave_nfe' => $response->listaNfse[0]["numero"]->textContent,
				'numeroprefeitura' => $response->listaNfse[0]["numero"]->textContent,
				'protocolo' =>  $response->listaNfse[0]["codigoVerificacao"]->textContent,
				'mensagem_nota' => $response->listaNfse[0]["outrasInformacoes"]->textContent,
			);
			$this->db->where('id', $id);
			$this->db->update('nf_saida', $arr);

			$this->session->set_flashdata('alert', ['success', $response->listaNfse[0]["outrasInformacoes"]->textContent]);
			redirect('nflocal');
		}
	}



	function imprimirRPSsv($id = null, $enviaAuto = null)
	{
		$nf = $this->NFSaida_model->getByID($id);
		$dadosOS        = $this->NFSaida_model->getByID($id);
		$dadosItensOs   = $this->NFSaidaItens_model->getItemsByIdNegotiation($id);
		$dadosCliente   = $this->Clientes_model->getById($dadosOS->id_cliente);
		$productDatabase  = $this->Produtos_model->getByIdFull($dadosItensOs[0]->id_produto, 0);
		$itenlist = $this->Ordensdeservico_model->getItens($dadosOS->id_negociacao);

		$this->view('nf_saida.imprimirsv', [
			'dadosOS'		=> $dadosOS,
			'dadosItensOs'   => $dadosItensOs,
			'dadosCliente' => $dadosCliente,
			'productDatabase' => $productDatabase,
			'itenslist' 		=> $itenlist,
		]);
	}







	function imprimirRPS($id = null, $enviaAuto = null)
	{
		$nf = $this->NFSaida_model->getByID($id);
		$url = $nf->xml_recebido;
		$arquivo = file_get_contents($url);
		$oXML = new SimpleXMLElement($arquivo);
		// header( 'Content-type: text/xml' );
		/* 	header( 'Content-type: text/xml' );
		echo $oXML->asXML(); */

		$dadosOS        = $this->NFSaida_model->getByID($id);
		$dadosItensOs   = $this->NFSaidaItens_model->getItemsByIdNegotiation($id);
		$productDatabase  = $this->Produtos_model->getByIdFull($dadosItensOs[0]->id_produto, 0);
		$produtocodigo = $this->ListaServicos_model->getListaServicoByCodigo($productDatabase->codigo_issqn);
		$aEmpresa = $this->Sistema_model->getEmpresa();

		if (isset($aEmpresa->regimeespecial_empresa)) {
			if ($aEmpresa->regimeespecial_empresa == 10) {
				$regime = 'Simples nacional com excesso do sublimite';
			} elseif ($aEmpresa->regimeespecial_empresa == 9) {
				$regime = 'Tributação Normal';
			} elseif ($aEmpresa->regimeespecial_empresa == 8) {
				$regime = 'Lucro Presumido';
			} elseif ($aEmpresa->regimeespecial_empresa == 7) {
				$regime = 'Lucro Real';
			} elseif ($aEmpresa->regimeespecial_empresa == 6) {
				$regime = 'Microempresário e Empresa de Pequeno Porte (ME EPP)';
				$especial = 'Documento emitido por ME ou EPP optante pelo Simples Nacional. Não gera direito a credito fiscal de IPI.';
			} elseif ($aEmpresa->regimeespecial_empresa == 5) {
				$regime = 'Microempresário Individual (MEI)';
			} elseif ($aEmpresa->regimeespecial_empresa == 4) {
				$regime = 'Cooperativa';
			} elseif ($aEmpresa->regimeespecial_empresa == 3) {
				$regime = 'Sociedade de profissionais';
			} elseif ($aEmpresa->regimeespecial_empresa == 2) {
				$regime = 'Estimativa';
			} elseif ($aEmpresa->regimeespecial_empresa == 1) {
				$regime = 'Microempresa municipal';
			}
		}


		//$negotiation = $this->Negociacao_model->getById($dadosOS->id_negociacao);

		$curl = curl_init();
		$ceptomador = $oXML->ListaNfse->CompNfse->Nfse->InfNfse->TomadorServico->Endereco->Cep;

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://viacep.com.br/ws/" . $ceptomador . "/json/unicode/",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Accept: text/xml',
				'Content-Type: text/xml;charset=UTF-8'
			),
		));

		$response = curl_exec($curl);
		$b = json_decode($response);


		$ordem = $this->Ordensdeservico_model->getById($dadosOS->id_negociacao);

		if ($ordem->condicaopagamento != 0 && $ordem->condicaopagamento != '') {
			$condpag = $this->Condicoesdepagamento_model->getValParcelas($ordem->condicaopagamento, $ordem->valorbruto, $ordem->vencimento);
		} else {
			$condpag = null;
		}

		$qtd = count($dadosItensOs);


		$itenlist = $this->Ordensdeservico_model->getItens($dadosOS->id_negociacao);

		$this->view('nf_saida.imprimirrps', [
			'itenslist'		=> $itenlist,
			'qtd'		   => $qtd,
			'condpag'	   => $condpag,
			'dadosOS'	   => $dadosOS,
			'nf'           => $nf,
			'compe'  	   => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->Competencia,
			'DataEmissao'  => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->DataEmissao,
			'dadosOS'      => $dadosOS,
			'nome'  	   => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->PrestadorServico->RazaoSocial,
			'Cnpj'		   => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->PrestadorServico->IdentificacaoPrestador->Cnpj,
			'ie'		   => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->PrestadorServico->IdentificacaoPrestador->InscricaoMunicipal,
			'rua'		   => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->PrestadorServico->Endereco->Endereco,
			'num'		   => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->PrestadorServico->Endereco->Numero,
			'ap'		   => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->PrestadorServico->Endereco->Complemento,
			'bairro'	   => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->PrestadorServico->Endereco->Bairro,
			'cep'		   =>  $oXML->ListaNfse->CompNfse->Nfse->InfNfse->PrestadorServico->Endereco->Cep,
			'nunfe'        => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->Numero,
			'cod'		   => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->CodigoVerificacao,
			'ietomador'	   =>  $oXML->ListaNfse->CompNfse->Nfse->InfNfse->TomadorServico->IdentificacaoTomador->InscricaoMunicipal,
			'cpftomador'   => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->TomadorServico->IdentificacaoTomador->CpfCnpj,
			'RazaoSocialtomador' => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->TomadorServico->RazaoSocial,
			'Enderecotomador'   =>  $oXML->ListaNfse->CompNfse->Nfse->InfNfse->TomadorServico->Endereco->Endereco,
			'numetomador'	 => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->TomadorServico->Endereco->Numero,
			'bairrotomaador'   => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->TomadorServico->Endereco->bairrotomaador,
			'Ceptomador'	=> $oXML->ListaNfse->CompNfse->Nfse->InfNfse->TomadorServico->Endereco->Cep,
			'discri'	 => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->Servico->Discriminacao,
			'codigotomador' => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->Servico->CodigoMunicipio,
			'CodigoTributacaoMunicipio' => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->Servico->CodigoTributacaoMunicipio,
			'subitem'		=> $oXML->ListaNfse->CompNfse->Nfse->InfNfse->Servico->ItemListaServico,
			'NaturezaOperacao' => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->NaturezaOperacao,
			'valorprinc'  =>  $oXML->ListaNfse->CompNfse->Nfse->InfNfse->Servico->Valores->ValorServicos,
			'IssRetido' => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->Servico->Valores->IssRetido,
			'ValorIss' => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->Servico->Valores->ValorIss,
			'BaseCalculo' => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->Servico->Valores->BaseCalculo,
			'Aliquota' =>  $oXML->ListaNfse->CompNfse->Nfse->InfNfse->Servico->Valores->Aliquota,
			'estadotomador' => $b->localidade,
			'dadosItensOs' => $dadosItensOs,
			'DescontoIncondicionado' => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->Servico->Valores->DescontoIncondicionado,
			'valorliquido'	 => $oXML->ListaNfse->CompNfse->Nfse->InfNfse->Servico->Valores->ValorLiquidoNfse,
			'tributacaotype' => $produtocodigo->descricao_servico,
			'regime' => $regime,
			'especial' => $especial,
			'responsemsg' => $dadosOS->mensagem_nota,
			'enviaAuto' => $enviaAuto,
			'id' => $id,
			'ordem' => $ordem
		]);
	}



	public function excluir($idNota, $city)
	{

		if (!$idNota) {
			$this->session->set_flashdata('alert', [
				'warning',
				'Nota não encontrada'
			]);
			redirect('NFSe');
		}
		// busca nf
		$nota = $this->NFSaida_model->getByID($idNota);
		if (!$nota) {
			$this->session->set_flashdata('alert', [
				'warning',
				'Nota não encontrada'
			]);
			redirect('NFSe');
		}
		$Itens = $this->NFSaidaItens_model->getItemsByIdNegotiation($idNota);
		$jsonitens = array();
		foreach ($Itens as $item) {
			$dados = [
				'nome' => $item->nome,
				'valor' => $item->valor,
				'total' => $item->total,
				'codigo' => $item->codigo,
				'desconto' => $item->desconto,
				'quantidade' => $item->quantidade,
			];
			array_push($jsonitens, $dados);
		}
		$historico = [
			'itens' => $jsonitens,
			'cliente' => $nota->nomecliente,
		];
		$data = [
			'numero_documento' => $nota->numero_documento,
			'usercri' => $this->session->userdata('nome'),
			'serie_documento' => $nota->serie_documento,
			'tabela' => $this->NFSaida_model->table,
			'historico' => json_encode($historico),
			'descricao' => 'Nota Fiscal de Saída',
			'data_exclusao' => date('Y-m-d'),
			'hora_exclusao' => date('H:i'),
			'motivo' => $motivo,
		];
		$this->LogExclusao_model->add($data);
		$titulos = $this->Contasareceber_model->getTitulosByIdNotaSaida($nota->id);

		// valida se teve baixa total ou parcial
		if ($titulos) {
			foreach ($titulos as $tit) {
				if ($tit->valorpago_cr > 0) {
					$this->session->set_flashdata('alert', [
						'error',
						"O título à receber de número {$tit->numero_cr}, vinculado a essa nota, teve algum valor pago",
						'Nota não pode ser excluída'
					]);
					redirect('NFSe');
				}
			}
		}
		// busca comissões
		$comissoes = $this->MovimentoComissoes_model->getByNota($nota->serie_documento, $nota->numero_documento);
		foreach ($comissoes as $comissao) {
			if ($comissao->saldo_comissao == 0) {
				$data = [
					'tipo' => 'S',
					'data_pagamento' => null,
					'datamod' => date('Y-m-d'),
					'datacri' => date('Y-m-d'),
					'data_movimento' => date('Y-m-d'),
					'vendedor' => $comissao->vendedor,
					'nf_origem' => $comissao->nf_origem,
					'valor_base' => $comissao->valor_base,
					'percentual' => $comissao->percentual,
					'usercri' => $this->session->userdata('nome'),
					'usermod' => $this->session->userdata('nome'),
					'valor_comissao' => $comissao->valor_comissao,
					'saldo_comissao' => $comissao->valor_comissao,
					'nf_origem_serie' => $comissao->nf_origem_serie,
				];
				$this->MovimentoComissoes_model->save($data);
			} else {
				$this->MovimentoComissoes_model->delete($comissao->id);
			}
		}
		$this->Negociacao_model->edit($nota->id_negociacao, ['numero_documento' => null, 'serie_documento' => null]);

		// exclui titulos
		$this->Contasareceber_model->excluiTitulosDaNota($idNota);
		// exclui movimentos bancários
		$this->Movimentosbancarios_model->excluiMovimentosDaNota($idNota);
		//retorna a ordem servico;
		$this->Ordensdeservico_model->getMod($nota->numero_documento);


		if ($city == 'Porto Alegre') {
			$this->cancelaRpsPOA($idNota);
		} elseif ($city == 'São Vendelino') {

			$this->cancelaRpssv($idNota);
		}

		// exclui nf_saida_itens
		redirect('Nflocal');
	}



	public function cancelaRpsPOA($id)
	{
		$dadosNF = $this->NFSaida_model->getByID($id);
		$company = $this->company;
		$certificadoDigital = $this->caminhosXml . 'certificado.pfx';
		$senhaCertificado = $company->senhacertificadonfe;
		$cnpj = str_replace(['.', '/', '-', ' '], '', $company->cnpj_empresa);
		$im = str_replace(['.', '/', '-', ' '], '', $company->im_empresa);
		$options = [
			'soapOptions' => [

				'ssl'   =>  [
					'cafile'    =>  __dir__ . DS . 'ca_mozilla_2019.pem',
				]
			]
		];
		$numero_rps = date('Y') . str_pad($dadosNF->numero_documento, 11, '0', STR_PAD_LEFT);
		$config = new Config($options);
		$config->setPfxCert($certificadoDigital);
		$config->setPasswordCert($senhaCertificado);
		$config->setTmpDirectory(__dir__ . DS . 'tmp');
		if ($this->sefaz_ambiente == 1) {
			$config->setWsdl('http://nfe.portoalegre.rs.gov.br/nfe-ws');
		} else {
			$config->setWsdl('https://nfse-hom.procempa.com.br/bhiss-ws/nfse?wsdl');
		}
		$certificate = new Certificate($config);
		$certificate = new Certificate($config);

		$pedidoCancelamento = new PedidoCancelarNfse();
		$pedidoCancelamento->cpfCnpjPrestador($cnpj);
		$pedidoCancelamento->setNumeroNfse($dadosNF->chave_nfe);
		$pedidoCancelamento->setCodigoMunicipio('4314902');
		$pedidoCancelamento->setCodigoCancelamento('2');
		$pedidoCancelamento->setIdPedidoCancelamento($dadosNF->chave_nfe);
		$pedidoCancelamento->setInscricaoMunicipalPrestador($im);
		$nfe = new CancelamentoNfeFragmento();
		$nfe->setInscricaoMunicipalPrestador($im);
		$nfe->setNumeroNfe($dadosNF->chave_nfe);

		$connection = new Connection($config, $certificate);

		// Enviar o RPS
		$response = $connection->dispatch($pedidoCancelamento);



		if ($dadosNF->cancelada == 'S') {
			$this->session->set_flashdata('alert', ['success', 'Solicitação de cancelamento processada']);
			redirect('nflocal');
		} else {
			$arr = array(
				'status_autorizada' => StatusNfAutorizadaEnum::AUTORIZADA,
				'status_transmitida' => StatusNfTransmitidaEnum::TRANSMITIDA,
				'mensagem_nota' => 'Solicitação de cancelamento processada',
				'cancelada' => 'S'
			);
			$this->db->where('id', $id);
			$this->db->update('nf_saida', $arr);
			$this->session->set_flashdata('alert', ['success', 'NFSE cancelada com sucesso!']);
			redirect('nflocal');
		}
	}

	public function save_pdf_nfse($id, $docnumber)
	{
		$nfse = $this->NFSaida_model->getById($id);
		$fileName = $nfse->numero_documento ? 'NFSE' . $nfse->numero_documento . '.pdf' : 'NFSE' . strval(rand(1000, 9999)) . '.pdf';

		$path = FCPATH . 'public\clientes\\' . $_SESSION['id'] . '\danfes\\';
		$file = $path . $fileName;

		// $this->BoletoConfigs_model->editBoletoConfig($id, ['pdf_boleto' => $file]);
		move_uploaded_file($_FILES['pdf']['tmp_name'], $file);

		$nf = new NFe4Danfe();
		$nf->send_mail_nfse($id, $file, $docnumber);
	}


	public function enviarsv($id)
	{

		$response = "";
		$company = $this->company;
		$certificadoDigital = $this->caminhosXml . 'certificado.pfx';
		$senhaCertificado = $company->senhacertificadonfe;
		$cnpj = str_replace(['.', '/', '-', ' '], '', $company->cnpj_empresa);
		$im = str_replace(['.', '/', '-', ' '], '', $company->im_empresa);
		$options = [
			'soapOptions' => [
				'ssl'   =>  [
					'cafile'    =>  __dir__ . DS . 'ca_mozilla_2019.pem',
				]
			]
		];
		//aa
		$config = new Config($options);
		$config->setPfxCert($certificadoDigital);
		$config->setPasswordCert($senhaCertificado);
		$config->setTmpDirectory(__dir__ . DS . 'tmp');
		if ($this->sefaz_ambiente == 1) {
			$config->setWsdl('http://saovendelino.nfse-tecnos.com.br:9091/EnvioLoteRPSSincrono.asmx?WSDL');
		} else {
			$config->setWsdl('');
		}
		$certificate = new Certificate($config);
		$dadosNF        = $this->NFSaida_model->getByID($id);
		$dadosCliente   = $this->Clientes_model->getById($dadosNF->id_cliente);
		$dadosItensNF   = $this->NFSaidaItens_model->getItemsByIdNegotiation($id);
		$aEmpresa = $this->Sistema_model->getEmpresa();

		$dadosUF        = $this->Estados_model->getStateById($dadosCliente->estado);
		//$pedido = new Pedidoenviarlote();
		$rps = new RpsFragmentoSv();

		$rps->setNumeroLote(1);

		$rps->setCpfCnpjPrestador($cnpj);
		$rps->setInscricaoMunicipalPrestador($im);
		$rps->setQuantidadeRps(1);
		$rps->setrazaoSocialPrestador($aEmpresa->razaosocial_empresa);
		//dados do tomador
		$rps->setStatus(1);
		$rps->setTipoRps(1);
		$rps->setUfTomador($dadosUF);
		$rps->setIncentivadorCultural(2);
		$rps->setOptanteSimplesNacional($aEmpresa->simplesnacional_empresa);
		$rps->setDataEmissao(date('Y-m-d\TH:i:s'));
		$rps->setSerieRps($dadosNF->serie_documento);
		$rps->setEnderecoTomador($dadosCliente->rua);
		$rps->setBairroTomador($dadosCliente->bairro);

		$rps->setNumeroRps($dadosNF->numero_documento);

		$vowels = array(".", "/", "-");
		$tomadorcpf = str_replace($vowels, "", $dadosCliente->documento);
		$rps->setCodigoMunicipioTomador($dadosCliente->ibge);
		$rps->setNumeroEnderecoTomador($dadosCliente->numero);


		$rps->setCodigoMunicipioPrestacao($dadosCliente->ibge);
		$razao = preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/", "/&/"), explode(" ", "a A e E i I o O u U n N"), $dadosCliente->razaosocial);
		$rps->setRazaoSocialTomador($razao);
		$rps->setCepTomador(str_replace(['.', '/', '-', ' '], '', $dadosCliente->cep));
		$rps->setCpfCnpjTomador(str_replace(['.', '/', '-', ' '], '', $tomadorcpf));
		$rps->setRegimeEspecialTributacao($aEmpresa->regimeespecial_empresa);

		if ($dadosCliente->cidade != $company->municipio_empresa) {
			$rps->setNaturezaOperacao(2);
		} else {
			$rps->setNaturezaOperacao(1);
		}
		$i = 1;
		/* 	if(count($dadosItensNF) > 1){
			$this->session->set_flashdata('alert', ['danger', 'Município não permite o envio de multiserviços']);
			
		 } */

		if ($dadosItensNF != null) {
			foreach ($dadosItensNF as $product) {
				if ($product->tipo != 'SE') {
					$this->session->set_flashdata('alert', ['danger', 'O produto ' . $product->nome . ' não é um serviço!']);
				}
				$productDatabase  = $this->Produtos_model->getByIdFull($product->id_produto, 0);
				//$produtocodigo = $this->ListaServicos_model->getListaServicoByCodigo($productDatabase->codigo_issqn);


				if (empty($productDatabase->codigo_issqn)) {
					$this->session->set_flashdata('alert', ['danger', 'Serviço sem o código do ISS cadastrado. Entre no cadastro do produto informe o código e tente transmitir novamente!']);
				}
				/* 	$productListaServico = $this->Produtos_model->getListaServicoByCodigo($productDatabase->codigo_issqn);
				if(empty($productListaServico)){
					$this->session->set_flashdata('alert', ['danger', 'Código de ISSQN não encontrado na lista de serviços!']);
					
				} */
				//	$total = '';
				$total .= "$product->nome; ";
				//var_dump($rps->setDiscriminacao($total));
				$baseinss = $productDatabase->baseinss/100;
				$inss =  $baseinss * intval($company->inss);
				$inss =  $inss/100;

				$baseiss = $productDatabase->baseiss/100;
				$iss = $baseiss * intval($productDatabase->iss);
				$iss = $iss/100;

				var_dump($iss);
				die;
 //teste
				$liquido = $product->total - $dadosNF->desconto;
				$rps->setValorServicos($dadosNF->valor_nota);
				$rps->setdescontoIncondicionado($dadosNF->desconto);
				$rps->setvalorLiquidoNfe($liquido);
				$rps->setBaseCalculo($product->id_produto);
				$rps->setAliquota($produtocodigo->aliquota);
				$rps->setDiscriminacao($total);
				$rps->setOutrasRetencoes(0.00);
				$rps->setValorIss($iss);
				$rps->setValorInss($inss);
				$rps->setItemListaServico($productDatabase->codigo_issqn);
				$rps->setCodigoTributacaoMunicipio(trim($productDatabase->codigo_issqn));

				$i++;
			}
		}




		$connection = new Connection($config, $certificate);
		// Enviar o RPS
		//$response = $connection->dispatch($rps);


		$your_xml_response = $response->exception;
		$clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $your_xml_response);
		$xml = new SimpleXMLELement($clean_xml);


		$xml = $xml->Body->mEnvioLoteRPSSincronoResponse->mEnvioLoteRPSSincronoResult;
		$xml = new SimpleXMLELement($xml);
		//salvar protocolo
		$abc = $xml->ListaMensagemRetorno->MensagemRetorno;

		$msg = '';

		foreach ($abc as $key) {

			$msg .= '';
			$msg .= $key->Mensagem;
		}

		$caminhosXml = getCwd() . '/public/clientes/' . $_SESSION['id_empresa'] . '/nfe/';



		if ($msg == 'Operação efetuada com sucesso') {
			$arr = array(
				'xml_enviado' => $caminhosXml . $response->xmlEnvio,
				'xml_recebido' => $caminhosXml . $response->xmlResposta,
				'status_autorizada' => StatusNfAutorizadaEnum::AUTORIZADA,
				'status_transmitida' => StatusNfTransmitidaEnum::TRANSMITIDA,
				'numeroprefeitura' => $response->listaNfse[0]["numero"]->textContent,
				'chave_nfe' => $response->listaNfse[0]["numero"]->textContent,
				'protocolo' => $xml->Protocolo,
				'mensagem_nota' => $msg,
			);
			$this->db->where('id', $id);
			$this->db->update('nf_saida', $arr);
			$this->session->set_flashdata('alert', ['success', $msg]);
		} else {
			$arr = array(
				'xml_enviado' => $caminhosXml . $response->xmlEnvio,
				'xml_recebido' => $caminhosXml . $response->xmlResposta,
				'status_autorizada' => StatusNfAutorizadaEnum::NAO_AUTORIZADA,
				'status_transmitida' => StatusNfTransmitidaEnum::NAO_TRANSMITIDA,
				'numeroprefeitura' => $response->listaNfse[0]["numero"]->textContent,
				'chave_nfe' => $response->listaNfse[0]["numero"]->textContent,
				'protocolo' => $xml->Protocolo,
				'mensagem_nota' => $msg,
			);
			$this->db->where('id', $id);
			$this->db->update('nf_saida', $arr);
			$this->session->set_flashdata('alert', ['danger', $msg]);
		}

		redirect('nflocal');
	}



	public function cancelaRpssv($id)
	{
		$dadosNF = $this->NFSaida_model->getByID($id);
		$company = $this->company;
		$dadosCliente   = $this->Clientes_model->getById($dadosNF->id_cliente);
		$certificadoDigital = $this->caminhosXml . 'certificado.pfx';
		$senhaCertificado = $company->senhacertificadonfe;
		$cnpj = str_replace(['.', '/', '-', ' '], '', $company->cnpj_empresa);
		$im = str_replace(['.', '/', '-', ' '], '', $company->im_empresa);
		$aEmpresa = $this->Sistema_model->getEmpresa();

		$options = [
			'soapOptions' => [

				'ssl'   =>  [
					'cafile'    =>  __dir__ . DS . 'ca_mozilla_2019.pem',
				]
			]
		];
		$numero_rps = date('Y') . str_pad($dadosNF->numero_documento, 11, '0', STR_PAD_LEFT);
		$config = new Config($options);
		$config->setPfxCert($certificadoDigital);
		$config->setPasswordCert($senhaCertificado);
		$config->setTmpDirectory(__dir__ . DS . 'tmp');
		if ($this->sefaz_ambiente == 1) {
			$config->setWsdl('http://saovendelino.nfse-tecnos.com.br:9098/CancelamentoNFSe.asmx?WSDL');
		} else {
			$config->setWsdl('');
		}
		$certificate = new Certificate($config);

		$pedidoCancelamento = new PedidoCancelarNfseSv();
		$pedidoCancelamento->cpfCnpjPrestador($cnpj);
		$pedidoCancelamento->setNumeroNfse($dadosNF->numero_documento);
		$pedidoCancelamento->setCodigoMunicipio($dadosCliente->ibge);
		$pedidoCancelamento->setIdPedidoCancelamento($dadosNF->chave_nfe);
		$pedidoCancelamento->setInscricaoMunicipalPrestador($im);


		$connection = new Connection($config, $certificate);


		// Enviar o RPS
		$response = $connection->dispatch($pedidoCancelamento);
		 $your_xml_response = $response->exception;
		 $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $your_xml_response);
		 $xml = new SimpleXMLELement($clean_xml);
 
 
		 $xml = $xml->Body->mCancelamentoNFSeResponse->mCancelamentoNFSeResult;
		 $xml = new SimpleXMLELement($xml);
		 //salvar protocolo
		
		//header('Content-Type: text/plain; charset=utf-8');
			if($xml->MensagemRetorno->MensagemRetorno->Mensagem == 'Object reference not set to an instance of an object.'){
				$this->session->set_flashdata('alert', ['danger', 'Erro no servidor do Web Service/Sefaz! Cancele pelo portal.']);
				redirect('nflocal');
			}
		


		if ($dadosNF->cancelada == 'S') {
			$this->session->set_flashdata('alert', ['success', 'Solicitação de cancelamento processada']);
			redirect('nflocal');
		} else {
			$arr = array(
				'status_autorizada' => StatusNfAutorizadaEnum::AUTORIZADA,
				'status_transmitida' => StatusNfTransmitidaEnum::TRANSMITIDA,
				'mensagem_nota' => 'Solicitação de cancelamento processada',
				'cancelada' => 'S'
			);
			$this->db->where('id', $id);
			$this->db->update('nf_saida', $arr);
			$this->session->set_flashdata('alert', ['success', 'NFSE cancelada com sucesso']);
			redirect('nflocal');
		}
	}


	public function consultarSv($id)
	{
		$company = $this->company;
		$dadosNF = $this->NFSaida_model->getByID($id);
		$certificadoDigital = $this->caminhosXml . 'certificado.pfx';
		$senhaCertificado = $company->senhacertificadonfe;




		$cnpj = str_replace(['.', '/', '-', ' '], '', $company->cnpj_empresa);
		$im = str_replace(['.', '/', '-', ' '], '', $company->im_empresa);
		$options = [
			'soapOptions' => [
				'ssl'   =>  [
					'cafile'    =>  __dir__ . DS . 'ca_mozilla_2019.pem',
				]
			]
		];
		$numero_rps = str_pad($dadosNF->numero_documento, 11, '0', STR_PAD_LEFT);
		$config = new Config($options);
		$config->setPfxCert($certificadoDigital);
		$config->setPasswordCert($senhaCertificado);
		$config->setTmpDirectory(__dir__ . DS . 'tmp');
		if ($this->sefaz_ambiente == 1) {
			$config->setWsdl('http://saovendelino.nfse-tecnos.com.br:9095/ConsultaNFSePorRPS.asmx?WSDL');
		} else {
			$config->setWsdl('');
		}
		$certificate = new Certificate($config);

		$pedido = new PedidoConsultarNfseRpsSv();
		$aEmpresa = $this->Sistema_model->getEmpresa();

		$pedido->setTipoRps(1);
		$pedido->setNumeroRps($dadosNF->numero_documento);
		//	$pedido->setNumeroRps(263);

		$pedido->setCpfCnpjPrestador($cnpj);
		$pedido->setInscricaoMunicipalPrestador($im);
		$pedido->setSerieRps($dadosNF->serie_documento);
		$pedido->setrazaoSocialPrestador($aEmpresa->razaosocial_empresa);

		// Realizar a conexao
		$connection = new Connection($config, $certificate);
		// Enviar o RPS
		$response = $connection->dispatch($pedido);

		$caminhosXml = getCwd() . '/public/clientes/' . $_SESSION['id_empresa'] . '/nfe/';
		if ($dadosNF->cancelada == 'S') {
			$this->session->set_flashdata('alert', ['success', 'Solicitação de cancelamento processada']);
			$msg = 'Solicitação de cancelamento processada';
			$arr = array(
				'xml_enviado' => $caminhosXml . $response->xmlEnvio,
				'xml_recebido' => $caminhosXml . $response->xmlResposta,
				'status_autorizada' => StatusNfAutorizadaEnum::AUTORIZADA,
				'status_transmitida' => StatusNfTransmitidaEnum::TRANSMITIDA,
				'mensagem_nota' => $msg,
				'consultasv' => 'S',
			);
			$this->db->where('id', $id);
			$this->db->update('nf_saida', $arr);

			redirect('nflocal');
		} else {
			$arr = array(
				'xml_enviado' => $caminhosXml . $response->xmlEnvio,
				'xml_recebido' => $caminhosXml . $response->xmlResposta,
				'status_autorizada' => StatusNfAutorizadaEnum::AUTORIZADA,
				'status_transmitida' => StatusNfTransmitidaEnum::TRANSMITIDA,
				'consultasv' => 'S',

			);
			$this->db->where('id', $id);
			$this->db->update('nf_saida', $arr);
			$this->session->set_flashdata('alert', [
				'success',
				'Operação efetuada com sucesso!	'
			]);
			redirect('nflocal');
		}
	}
}
