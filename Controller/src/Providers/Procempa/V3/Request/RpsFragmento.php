<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 26/05/2019
 * Time: 15:59
 */

namespace Nfsews\Providers\Procempa\V3\Request;


use Nfsews\ParseTemplate;


/**
 * Class RpsFragmento
 *
 * Classe do tipo fragmento. É utilizada em conjunto com as classes PedidoEnviarLoteRps e PedidoGerarNfse.
 * A mesma contém as informações do RPS que será convertido em Nota Fiscal
 *
 * @package Nfsews\Providers\Procempa\V3\Request
 */
class RpsFragmento
{

    const SYS_DS = DIRECTORY_SEPARATOR;
    private $abrasfVersion = '1.00';
    private $templatePath = null;
    private $idInfRps = null;
    private $numeroRps = null;
    private $serieRps = null;
    private $tipoRps = null;
    private $dataEmissao = null;
    private $naturezaOperacao = null;
    private $regimeEspecialTributacao = null;
    private $optanteSimplesNacional = null;
    private $incentivadorCultural = null;
    private $status = null;
    private $numeroRpsSubstituido = null;
    private $serieRpsSubstituido = null;
    private $tipoRpsSubstituido = null;
    private $valorServicos = 0.00;
    private $valorDeducoes = 0.00;
    private $valorPis = 0.00;
    private $valorCofins = 0.00;
    private $valorInss = 0.00;
    private $valorIr = 0.00;
    private $valorCsll = 0.00;
    private $issRetido = 2;
    private $valorIss = 0.00;
    private $valorIssRetido = 0.00;
    private $outrasRetencoes = 0.00;
    private $baseCalculo = 0.00;
    private $aliquota = 0.00;
    private $valorLiquidoNfe = 0.00;
    private $descontoIncondicionado = 0.00;
    private $descontoCondicionado = 0.00;
    private $itemListaServico = null;
    private $codigoCnae = null;
    private $codigoTributacaoMunicipio = null;
    private $discriminacao = null;
    private $codigoMunicipioPrestacao = null;
    private $inscricaoMunicipalPrestador = null;
    private $cpfCnpjPrestador = null;
    private $cpfCnpjTomador = null;
    private $inscricaoMunicipalTomador = null;
    private $razaoSocialTomador = null;
    private $enderecoTomador = null;
    private $numeroEnderecoTomador = null;
    private $complementoEnderecoTomador = null;
    private $bairroTomador = null;
    private $codigoMunicipioTomador = null;
    private $ufTomador = null;
    private $cepTomador = null;
    private $telefoneTomador = null;
    private $emailTomador = null;
    private $razaoSocialIntermediario = null;
    private $cpfCnpjIntermediario = null;
    private $inscricaoMunicipalIntermediario = null;
    private $codigoObra = null;
    private $art = null;

    /**
     * RpsFragmento constructor.
     */
    public function __construct()
    {
        $this->templatePath = __dir__ . self::SYS_DS . '..' . self::SYS_DS . 'template' . self::SYS_DS . 'RpsFragmento.xml'  ;
    }

    /**
     * @return string
     */
    public function getAbrasfVersion()
    {
        return $this->abrasfVersion;
    }


    /**
     * @return string|null
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }


    /**
     * @return null
     */
    public function getIdInfRps()
    {
        return $this->idInfRps;
    }

    /**
     * @param null $idInfRps
     */
    public function setIdInfRps($idInfRps)
    {
        $this->idInfRps = $idInfRps;
    }

    /**
     * @return null
     */
    public function getNumeroRps()
    {
        return $this->numeroRps;
    }

    /**
     * @param null $numeroRps
     */
    public function setNumeroRps($numeroRps)
    {
        $this->numeroRps = $numeroRps;
    }

    /**
     * @return null
     */
    public function getSerieRps()
    {
        return $this->serieRps;
    }

    /**
     * @param null $serieRps
     */
    public function setSerieRps($serieRps)
    {
        $this->serieRps = $serieRps;
    }

    /**
     * @return null
     */
    public function getTipoRps()
    {
        return $this->tipoRps;
    }

    /**
     * @param null $tipoRps
     */
    public function setTipoRps($tipoRps)
    {
        $this->tipoRps = $tipoRps;
    }

    /**
     * @return null
     */
    public function getDataEmissao()
    {
        return $this->dataEmissao;
    }

    /**
     * @param null $dataEmissao
     */
    public function setDataEmissao($dataEmissao)
    {
        $this->dataEmissao = $dataEmissao;
    }

    /**
     * @return null
     */
    public function getNaturezaOperacao()
    {
        return $this->naturezaOperacao;
    }

    /**
     * @param null $naturezaOperacao
     */
    public function setNaturezaOperacao($naturezaOperacao)
    {
        $this->naturezaOperacao = $naturezaOperacao;
    }

    /**
     * @return null
     */
    public function getRegimeEspecialTributacao()
    {
        return $this->regimeEspecialTributacao;
    }

    /**
     * @param null $regimeEspecialTributacao
     */
    public function setRegimeEspecialTributacao($regimeEspecialTributacao)
    {
        $this->regimeEspecialTributacao = $regimeEspecialTributacao;
    }

    /**
     * @return null
     */
    public function getOptanteSimplesNacional()
    {
        return $this->optanteSimplesNacional;
    }

    /**
     * Indica se a empresa é optante pelo simples nacional
     * Informar conforme solicitado pelo manual da prefeitura
     *
     * @param null $optanteSimplesNacional Int aceita os valores 1 para Sim e 2 Para Não
     */
    public function setOptanteSimplesNacional($optanteSimplesNacional)
    {
        $this->optanteSimplesNacional = $optanteSimplesNacional;
    }

    /**
     * @return null
     */
    public function getIncentivadorCultural()
    {
        return $this->incentivadorCultural;
    }

    /**
     * Indica se a empresa participa de algum programa de incentivo cultural
     * @param null $incentivadorCultural Int Aceita os valores 1 para Sim e 2 para Não
     */
    public function setIncentivadorCultural($incentivadorCultural)
    {
        $this->incentivadorCultural = $incentivadorCultural;
    }

    /**
     * @return null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Informa se deverá ser emitida uma nota fiscal com o status normal ou cancelada
     * @param null $status Int Status do RPS: 1 para Sim e 2 para Não
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return null
     */
    public function getNumeroRpsSubstituido()
    {
        return $this->numeroRpsSubstituido;
    }

    /**
     * (OPCIONAL)
     * Indica a nota fiscal a ser gerada deverá substitui a nota com o número do RPS passado
     *
     * @param null $numeroRpsSubstituido  Int Número do RPS que será substituído
     */
    public function setNumeroRpsSubstituido($numeroRpsSubstituido)
    {
        $this->numeroRpsSubstituido = $numeroRpsSubstituido;
    }

    /**
     * @return null
     */
    public function getSerieRpsSubstituido()
    {
        return $this->serieRpsSubstituido;
    }

    /**
     * (OPCIONAL) quando numeroRpsSubstituido for null
     * Informa a série do RPS a ser substituído
     *
     * @param null $serieRpsSubstituido String Série do RPS a ser substituído
     */
    public function setSerieRpsSubstituido($serieRpsSubstituido)
    {
        $this->serieRpsSubstituido = $serieRpsSubstituido;
    }

    /**
     * @return null
     */
    public function getTipoRpsSubstituido()
    {
        return $this->tipoRpsSubstituido;
    }

    /**
     * (OPCIONAL) quando numeroRpsSubstituído for null
     * Informa o tipo do RPS a ser substituído
     *
     * @param null $tipoRpsSubstituido String Tipo do RPS a ser substituído
     */
    public function setTipoRpsSubstituido($tipoRpsSubstituido)
    {
        $this->tipoRpsSubstituido = $tipoRpsSubstituido;
    }

    /**
     * @return float
     */
    public function getValorServicos()
    {
        return $this->valorServicos;
    }

    /**
     * @param float $valorServicos
     */
    public function setValorServicos($valorServicos)
    {
        $this->valorServicos = number_format($valorServicos, 2, '.', '');
    }

    /**
     * @return float
     */
    public function getValorDeducoes()
    {
        return $this->valorDeducoes;
    }

    /**
     * (OPCIONAL)
     * Valor das Deduções
     * @param float $valorDeducoes
     */
    public function setValorDeducoes($valorDeducoes)
    {
        $this->valorDeducoes = number_format($valorDeducoes, 2, '.', '');
    }

    /**
     * @return float
     */
    public function getValorPis()
    {
        return $this->valorPis;
    }

    /**
     * (OPCIONAL)
     * Valor do PIS
     * @param float $valorPis
     */
    public function setValorPis($valorPis)
    {
        $this->valorPis = $valorPis;
    }

    /**
     * @return float
     */
    public function getValorCofins()
    {
        return $this->valorCofins;
    }

    /**
     * (OPCIONAL)
     * Valor do Cofins
     * @param float $valorCofins
     */
    public function setValorCofins($valorCofins)
    {
        $this->valorCofins = number_format($valorCofins, 2, '.', '');;
    }

    /**
     * @return float
     */
    public function getValorInss()
    {
        return $this->valorInss;
    }

    /**
     * (Opcional)
     * Valor do INSS
     * @param float $valorInss
     */
    public function setValorInss($valorInss)
    {
        $this->valorInss = number_format($valorInss, 2, '.', '');;
    }

    /**
     * @return float
     */
    public function getValorIr()
    {
        return $this->valorIr;
    }

    /**
     * (OPCIONAL)
     * Valor do IR
     * @param float $valorIr
     */
    public function setValorIr($valorIr)
    {
        $this->valorIr = number_format($valorIr, 2, '.', '');;
    }

    /**
     * @return float
     */
    public function getValorCsll()
    {
        return $this->valorCsll;
    }

    /**
     * (OPCIONAL)
     * Valor do CSLL
     * @param float $valorCsll
     */
    public function setValorCsll($valorCsll)
    {
        $this->valorCsll = number_format($valorCsll, 2, '.', '');;
    }

    /**
     * @return int
     */
    public function getIssRetido()
    {
        return $this->issRetido;
    }

    /**
     * Indica se o imposto será retido
     *
     * @param int $issRetido 1 - Sim; 2 - Não
     */
    public function setIssRetido($issRetido)
    {
        $this->issRetido = $issRetido;
    }

    /**
     * @return float
     */
    public function getValorIss()
    {
        return $this->valorIss;
    }

    /**
     * (OPCIONAL) Somente quando o imposto for retido
     * Informa o valor do imposto
     *
     * @param float $valorIss
     */
    public function setValorIss($valorIss)
    {
        $this->valorIss = number_format($valorIss, 2, '.', '');;
    }

    /**
     * @return float
     */
    public function getValorIssRetido()
    {
        return $this->valorIssRetido;
    }

    /**
     * (OPCIONAL) Somente quando o imposto NÃO for retido pelo tomador
     * Valor do imposto quando o mesmo sofrer retenção na fonte
     *
     * @param float $valorIssRetido
     */
    public function setValorIssRetido($valorIssRetido)
    {
        $this->valorIssRetido = number_format($valorIssRetido, 2, '.', '');;
    }

    /**
     * @return float
     */
    public function getOutrasRetencoes()
    {
        return $this->outrasRetencoes;
    }

    /**
     * (OPCIONAL)
     * @param float $outrasRetencoes
     */
    public function setOutrasRetencoes($outrasRetencoes)
    {
        $this->outrasRetencoes = number_format($outrasRetencoes, 2, '.', '');;
    }

    /**
     * @return float
     */
    public function getBaseCalculo()
    {
        return $this->baseCalculo;
    }

    /**
     * @param float $baseCalculo
     */
    public function setBaseCalculo($baseCalculo)
    {
        $this->baseCalculo = number_format($baseCalculo, 2, '.', '');;
    }

    /**
     * @return float
     */
    public function getAliquota()
    {
        return $this->aliquota;
    }

    /**
     * Aliquota em percentual. 0.05 indica 5% e 5.00 indica 500%
     * @param float $aliquota
     */
    public function setAliquota($aliquota)
    {
        $this->aliquota = number_format($aliquota, 2, '.', '');;
    }

    /**
     * @return float
     */
    public function getValorLiquidoNfe()
    {
        return $this->valorLiquidoNfe;
    }

    /**
     * O valor liquido conforme definido pelo manual do município deve ser o seguinte cálculo
     * ValorServicos - ValorPis - ValorCofins - ValorInss - ValorIr - ValorCsll - OutrasRetencoes - ValorIssRetido -
     * DescontoCondicionado - DescontoIncondicionado
     *
     * @param float $valorLiquidoNfe
     */
    public function setValorLiquidoNfe($valorLiquidoNfe)
    {
        $this->valorLiquidoNfe = number_format($valorLiquidoNfe, 2, '.', '');;
    }

    /**
     * @return float
     */
    public function getDescontoIncondicionado()
    {
        return $this->descontoIncondicionado;
    }

    /**
     * (OPCIONAL)
     * @param float $descontoIncondicionado
     */
    public function setDescontoIncondicionado($descontoIncondicionado)
    {
        $this->descontoIncondicionado = number_format($descontoIncondicionado, 2, '.', '');;
    }

    /**
     * @return float
     */
    public function getDescontoCondicionado()
    {
        return $this->descontoCondicionado;
    }

    /**
     * (OPCIONAL)
     * @param float $descontoCondicionado
     */
    public function setDescontoCondicionado($descontoCondicionado)
    {
        $this->descontoCondicionado = number_format($descontoCondicionado, 2, '.', '');;
    }

    /**
     * @return null
     */
    public function getItemListaServico()
    {
        return $this->itemListaServico;
    }

    /**
     * @param null $itemListaServico
     */
    public function setItemListaServico($itemListaServico)
    {
        $this->itemListaServico = $itemListaServico;
    }

    /**
     * @return null
     */
    public function getCodigoCnae()
    {
        return $this->codigoCnae;
    }

    /**
     * @param null $codigoCnae
     */
    public function setCodigoCnae($codigoCnae)
    {
        $this->codigoCnae = $codigoCnae;
    }

    /**
     * @return null
     */
    public function getCodigoTributacaoMunicipio()
    {
        return $this->codigoTributacaoMunicipio;
    }

    /**
     * @param null $codigoTributacaoMunicipio
     */
    public function setCodigoTributacaoMunicipio($codigoTributacaoMunicipio)
    {
        $this->codigoTributacaoMunicipio = $codigoTributacaoMunicipio;
    }

    /**
     * @return null
     */
    public function getDiscriminacao()
    {
        return $this->discriminacao;
    }

    /**
     * @param null $discriminacao
     */
    public function setDiscriminacao($discriminacao)
    {
        $this->discriminacao = $discriminacao;
    }

    /**
     * @return null
     */
    public function getCodigoMunicipioPrestacao()
    {
        return $this->codigoMunicipioPrestacao;
    }

    /**
     * Código IBGE de 7 digitos do município onde o serviço foi prestado
     *
     * @param string $codigoMunicipioPrestacao
     */
    public function setCodigoMunicipioPrestacao($codigoMunicipioPrestacao)
    {
        $this->codigoMunicipioPrestacao = $codigoMunicipioPrestacao;
    }

    /**
     * @return null
     */
    public function getInscricaoMunicipalPrestador()
    {
        return $this->inscricaoMunicipalPrestador;
    }

    /**
     * @param string $inscricaoMunicipalPrestador
     */
    public function setInscricaoMunicipalPrestador($inscricaoMunicipalPrestador)
    {
        $this->inscricaoMunicipalPrestador = $inscricaoMunicipalPrestador;
    }

    /**
     * @return null
     */
    public function getCpfCnpjPrestador()
    {
        return $this->cpfCnpjPrestador;
    }

    /**
     * @param string $cpfCnpjPrestador
     */
    public function setCpfCnpjPrestador($cpfCnpjPrestador)
    {
        $this->cpfCnpjPrestador = $cpfCnpjPrestador;
    }

    /**
     * @return null
     */
    public function getCpfCnpjTomador()
    {
        return $this->cpfCnpjTomador;
    }

    /**
     * @param null $cpfCnpjTomador
     */
    public function setCpfCnpjTomador($cpfCnpjTomador)
    {
        $this->cpfCnpjTomador = $cpfCnpjTomador;
    }

    /**
     * @return null
     */
    public function getInscricaoMunicipalTomador()
    {
        return $this->inscricaoMunicipalTomador;
    }

    /**
     * @param null $inscricaoMunicipalTomador
     */
    public function setInscricaoMunicipalTomador($inscricaoMunicipalTomador)
    {
        $this->inscricaoMunicipalTomador = $inscricaoMunicipalTomador;
    }

    /**
     * @return null
     */
    public function getRazaoSocialTomador()
    {
        return $this->razaoSocialTomador;
    }

    /**
     * @param null $razaoSocialTomador
     */
    public function setRazaoSocialTomador($razaoSocialTomador)
    {
        $this->razaoSocialTomador = $razaoSocialTomador;
    }

    /**
     * @return null
     */
    public function getEnderecoTomador()
    {
        return $this->enderecoTomador;
    }

    /**
     * @param null $enderecoTomador
     */
    public function setEnderecoTomador($enderecoTomador)
    {
        $this->enderecoTomador = $enderecoTomador;
    }

    /**
     * @return null
     */
    public function getNumeroEnderecoTomador()
    {
        return $this->numeroEnderecoTomador;
    }

    /**
     * @param null $numeroEnderecoTomador
     */
    public function setNumeroEnderecoTomador($numeroEnderecoTomador)
    {
        $this->numeroEnderecoTomador = $numeroEnderecoTomador;
    }

    /**
     * @return null
     */
    public function getComplementoEnderecoTomador()
    {
        return $this->complementoEnderecoTomador;
    }

    /**
     * @param null $complementoEnderecoTomador
     */
    public function setComplementoEnderecoTomador($complementoEnderecoTomador)
    {
        $this->complementoEnderecoTomador = $complementoEnderecoTomador;
    }

    /**
     * @return null
     */
    public function getBairroTomador()
    {
        return $this->bairroTomador;
    }

    /**
     * @param null $bairroTomador
     */
    public function setBairroTomador($bairroTomador)
    {
        $this->bairroTomador = $bairroTomador;
    }

    /**
     * @return null
     */
    public function getCodigoMunicipioTomador()
    {
        return $this->codigoMunicipioTomador;
    }

    /**
     * @param null $codigoMunicipioTomador
     */
    public function setCodigoMunicipioTomador($codigoMunicipioTomador)
    {
        $this->codigoMunicipioTomador = $codigoMunicipioTomador;
    }

    /**
     * @return null
     */
    public function getUfTomador()
    {
        return $this->ufTomador;
    }

    /**
     * @param null $ufTomador
     */
    public function setUfTomador($ufTomador)
    {
        $this->ufTomador = $ufTomador;
    }

    /**
     * @return null
     */
    public function getCepTomador()
    {
        return $this->cepTomador;
    }

    /**
     * @param null $cepTomador
     */
    public function setCepTomador($cepTomador)
    {
        $this->cepTomador = $cepTomador;
    }

    /**
     * @return null
     */
    public function getTelefoneTomador()
    {
        return $this->telefoneTomador;
    }

    /**
     * @param null $telefoneTomador
     */
    public function setTelefoneTomador($telefoneTomador)
    {
        $this->telefoneTomador = $telefoneTomador;
    }

    /**
     * @return null
     */
    public function getEmailTomador()
    {
        return $this->emailTomador;
    }

    /**
     * @param null $emailTomador
     */
    public function setEmailTomador($emailTomador)
    {
        $this->emailTomador = $emailTomador;
    }

    /**
     * @return null
     */
    public function getRazaoSocialIntermediario()
    {
        return $this->razaoSocialIntermediario;
    }

    /**
     * (OPCIONAL)
     * @param null $razaoSocialIntermediario
     */
    public function setRazaoSocialIntermediario($razaoSocialIntermediario)
    {
        $this->razaoSocialIntermediario = $razaoSocialIntermediario;
    }

    /**
     * @return null
     */
    public function getCpfCnpjIntermediario()
    {
        return $this->cpfCnpjIntermediario;
    }

    /**
     * (OPCIONAL)
     * @param null $cpfCnpjIntermediario
     */
    public function setCpfCnpjIntermediario($cpfCnpjIntermediario)
    {
        $this->cpfCnpjIntermediario = $cpfCnpjIntermediario;
    }

    /**
     * (OPCIONAL)
     * @return null
     */
    public function getInscricaoMunicipalIntermediario()
    {
        return $this->inscricaoMunicipalIntermediario;
    }

    /**
     * (OPCIONAL)
     * @param null $inscricaoMunicipalIntermediario
     */
    public function setInscricaoMunicipalIntermediario($inscricaoMunicipalIntermediario)
    {
        $this->inscricaoMunicipalIntermediario = $inscricaoMunicipalIntermediario;
    }

    /**
     * @return null
     */
    public function getCodigoObra()
    {
        return $this->codigoObra;
    }

    /**
     * (OPCIONAL)
     * @param null $codigoObra
     */
    public function setCodigoObra($codigoObra)
    {
        $this->codigoObra = $codigoObra;
    }

    /**
     * @return null
     */
    public function getArt()
    {
        return $this->art;
    }

    /**
     * (OPCIONAL)
     * @param null $art
     */
    public function setArt($art)
    {
        $this->art = $art;
    }



    /**
     * @return mixed
     */
    public function getAllAttributes()
    {
        // TODO: Implement getAllAttributes() method.
        $array = [];

        foreach ($this as $key => $value) {
            if (property_exists($this, $key)) {
                array_push($array, array($key => $value));
            }
        }
        return $array;
    }

    /**
     * Utilizado para substituir TAGs que podem ter mais de um nome, como ocorre por exemplo com a CPFCNPJ
     * na qual pode assumir tanto o valor CNPJ quanto o valor CPF
     * @return array
     */
    private function getXmlReplaceMark(){
        return [
            [
                'mark' =>  '{cpxCpfCnpjPrestador}',
                'value' =>  (strlen($this->cpfCnpjPrestador) == 14) ? '<Cnpj>{cpfCnpjPrestador}</Cnpj>' : '<Cpd>{cpfCnpjPrestador}</Cpd>'
            ],
            [
                'mark' =>  '{cpxCpfCnpjTomador}',
                'value' =>  (strlen($this->cpfCnpjTomador) == 14) ? '<Cnpj>{cpfCnpjTomador}</Cnpj>' : '<Cpf>{cpfCnpjTomador}</Cpf>'
            ],
            [
                'mark' =>  '{cpxCpfCnpjIntermediario}',
                'value' =>  (strlen($this->cpfCnpjIntermediario) == 14) ? '<Cnpj>{cpfCnpjIntermediario}</Cnpj>' : '<Cpf>{cpfCnpjIntermediario}</Cpf>'
            ],
        ];
    }

    /**
     * @return mixed
     * @throws
     */
    public function toXml()
    {
        // TODO: Implement toXml() method.
        if(empty($this->idInfRps))
            $this->idInfRps = 'RPS_'. preg_replace('/[\. ]/','',microtime(true));

        return ParseTemplate::parse($this, $this->getXmlReplaceMark());
    }


}