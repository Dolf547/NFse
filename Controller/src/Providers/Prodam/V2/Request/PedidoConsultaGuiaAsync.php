<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 07/05/2019
 * Time: 20:29
 */

namespace Nfsews\Providers\Prodam\V2\Request;


use Nfsews\ParseTemplate;

class PedidoConsultaGuiaAsync implements IRequest
{
    const SYS_DS = DIRECTORY_SEPARATOR;
    private $abrasfVersion = '1.00';
    private $soapHelper = '\\Nfsews\\Providers\\Prodam\\V2\\Helpers\\Soap';
    private $responseNamespace = '\\Nfsews\\Providers\\Prodam\\V2\\Response\\PedidoConsultaGuiaAsyncResponse';
    private $templatePath = null;
    private $action = 'ConsultaGuia';
    private $cpfCnpjRemetente = null;
    private $inscricaoMunicipalPrestador = null;
    private $incidencia = null;
    private $situacao = null;
    private $tipoEmissao = null;

    /**
     * PedidoConsultaGuiaAsync constructor.
     */
    public function __construct()
    {
        $this->templatePath = __dir__ . self::SYS_DS . '..' . self::SYS_DS . 'template' . self::SYS_DS . 'PedidoConsultaGuiaAsync.xml'  ;
    }

    /**
     * @return string
     */
    public function getAbrasfVersion()
    {
        return $this->abrasfVersion;
    }


    /**
     * @return string
     */
    public function getSoapHelper()
    {
        return $this->soapHelper;
    }

    /**
     * @return string
     */
    public function getResponseNamespace()
    {
        return $this->responseNamespace;
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
    public function getCpfCnpjRemetente()
    {
        return $this->cpfCnpjRemetente;
    }

    /**
     * @param null $cpfCnpjRemetente
     */
    public function setCpfCnpjRemetente($cpfCnpjRemetente)
    {
        $this->cpfCnpjRemetente = preg_replace('/[\.\-\/]/', '', $cpfCnpjRemetente);
    }

    /**
     * @return null
     */
    public function getInscricaoMunicipalPrestador()
    {
        return $this->inscricaoMunicipalPrestador;
    }

    /**
     * @param null $inscricaoMunicipalPrestador
     */
    public function setInscricaoMunicipalPrestador($inscricaoMunicipalPrestador)
    {
        $this->inscricaoMunicipalPrestador = $inscricaoMunicipalPrestador;
    }

    /**
     * @return null
     */
    public function getIncidencia()
    {
        return $this->incidencia;
    }

    /**
     * @param null $incidencia Formato YYYY-MM
     * @throws
     */
    public function setIncidencia($incidencia)
    {
        if (strlen($incidencia) != 7)
            throw new \Exception('A incidência precisa estar no formato YYYY-MM');
        $this->incidencia = $incidencia;
    }

    /**
     * @return null
     */
    public function getSituacao()
    {
        return $this->situacao;
    }

    /**
     * @param null $situacao
     */
    public function setSituacao($situacao)
    {
        $this->situacao = $situacao;
    }

    /**
     * @return null
     */
    public function getTipoEmissao()
    {
        return $this->tipoEmissao;
    }

    /**
     * @param null $tipoEmissao
     */
    public function setTipoEmissao($tipoEmissao)
    {
        $this->tipoEmissao = $tipoEmissao;
    }

    public function getAction()
    {
        // TODO: Implement getAction() method.
        return $this->action;
    }

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

    public function toXml()
    {
        // TODO: Implement toXml() method.
        return ParseTemplate::parse($this, $this->getXmlReplaceMark());
    }

    /**
     * Utilizado para substituir TAGs que podem ter mais de um nome, como ocorre por exemplo com a CPFCNPJ
     * na qual pode assumir tanto o valor CNPJ quanto o valor CPF
     * @return array
     */
    private function getXmlReplaceMark(){
        return [
            [
                'mark' =>  '{CpxCpfCnpjRemetente}',
                'value' =>  (strlen($this->cpfCnpjRemetente) == 14) ? '<CNPJ>{cpfCnpjRemetente}</CNPJ>' : '<CPF>{cpfCnpjRemetente}</CPF>'
            ]
        ];
    }

    public function getEnvelopString(){

        return '<ConsultaGuiaRequest xmlns="http://www.prefeitura.sp.gov.br/nfe">
                  <VersaoSchema>1</VersaoSchema>
                  <MensagemXML>{body}</MensagemXML>
                </ConsultaGuiaRequest>';

    }
}