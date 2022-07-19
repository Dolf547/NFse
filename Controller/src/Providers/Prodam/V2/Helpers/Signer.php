<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 10/05/2019
 * Time: 15:57
 */

namespace Nfsews\Providers\Prodam\V2\Helpers;



use Nfsews\Providers\Prodam\V2\Request\CancelamentoNfeFragmento;
use Nfsews\Providers\Prodam\V2\Request\IRequest;

class Signer
{
    const SYS_DS = DIRECTORY_SEPARATOR;

    public static function getSignCancelamentoSP(CancelamentoNfeFragmento $nfe, $priKeyPem){
        $strDecoded = '';

        $strDecoded .=  str_pad( $nfe->getInscricaoMunicipalPrestador(), 8, '0', STR_PAD_LEFT);
        $strDecoded .=  str_pad( $nfe->getNumeroNfe() , 12, '0', STR_PAD_LEFT);

        try{

            $isSigned = openssl_sign($strDecoded, $strEncoded, $priKeyPem , OPENSSL_ALGO_SHA1);

            if ($isSigned == false){
                throw new Exception('Não é possível gerar o hash de assinatura de cancelamento.');
            }

        }catch (\Exception $e){
            throw new Exception('Não é possível gerar o hash de assinatura de cancelamento. '. $e->getMessage());
        }


        // codifica o hash gerado
        return base64_encode( $strEncoded);
    }

    public static function getSignSP(IRequest $rps, $priKeyPem){
        $strDecoded = '';
        $strEncoded = '';

        $strDecoded .=  str_pad( $rps->getInscricaoMunicipalPrestador(), 8, '0', STR_PAD_LEFT);
        $strDecoded .=  str_pad( $rps->getSerieRps(), 5, ' ', STR_PAD_RIGHT);

        $strDecoded .=  str_pad( $rps->getNumeroRps(), 12, '0', STR_PAD_LEFT);
        // formata a data de emissão para o formato AAAAMMDD

        try{
            $date = \DateTime::createFromFormat('Y-m-d' , $rps->getDataEmissaoRps() );
            if ($date == null){
                throw new \Exception('A data de emissão do RPS é nula ou não está no formato YYYY-MM-DD. Valor 
                informado: '. $rps->getDataEmissaoRps());
            }
            $strDecoded .=  str_pad( $date->format('Ymd') ,8, '0', STR_PAD_LEFT);
        }catch (\Exception $e){
            throw new \Exception('A data de emissão do RPS é nula ou não está no formato YYYY-MM-DD. Valor 
                informado: '. $rps->getDataEmissaoRps());
        }
        $strDecoded .=  str_pad( $rps->getTributacaoRps() , 1, ' ', STR_PAD_RIGHT);
        $strDecoded .=  str_pad( $rps->getStatusRps() , 1, ' ', STR_PAD_RIGHT);
        $strDecoded .=  ($rps->getIssRetido() || $rps->getIssRetido() == 'true') ? 'S' : 'N';

        $strDecoded .=  str_pad( preg_replace('/[\.,]/', '', number_format($rps->getValorServicos(), 2))  , 15,
            '0', STR_PAD_LEFT);
        $strDecoded .=  str_pad( preg_replace('/[\.,]/', '', number_format($rps->getValorDeducoes(), 2))  , 15,
            '0', STR_PAD_LEFT);
        $strDecoded .=  str_pad( $rps->getCodigoServico(), 5, '0', STR_PAD_LEFT);
        // Indicador CPF/CNPJ Tomador
        $indicadorCpfCnpj = 3;
        if (strlen($rps->getCpfCnpjTomador()) == 14){
            $indicadorCpfCnpj = 2;
        }else
            if (strlen($rps->getCpfCnpjTomador()) == 11)
                $indicadorCpfCnpj = 1;
        $strDecoded .=  str_pad( $indicadorCpfCnpj, 1, '0', STR_PAD_LEFT);
        $strDecoded .=  str_pad( $rps->getCpfCnpjTomador(), 14, '0', STR_PAD_LEFT);
        // Indicador Cpf/Cnpj Intermediario
        if (! empty($rps->getCpfCnpjIntermediario())){
            $indicadorCpfCnpj = 3;
            if (strlen($rps->getCpfCnpjIntermediario()) == 14){
                $indicadorCpfCnpj = 2;
            }else
                if (strlen($rps->getCpfCnpjIntermediario()) == 11)
                    $indicadorCpfCnpj = 1;
            $strDecoded .=  str_pad( $indicadorCpfCnpj, 1, '0', STR_PAD_LEFT);
            $strDecoded .=  str_pad( $rps->getCpfCnpjIntermediario(), 14, '0', STR_PAD_LEFT);

            $issRetidoIntermediario = ($rps->getIssRetidoIntermediario()) ? 'S' : 'N';
            $strDecoded .=  str_pad( $issRetidoIntermediario , 1, ' ', STR_PAD_RIGHT);
        }

        /*var_dump($strDecoded);
        $strDecoded = hash('sha1', $strDecoded, true);
        var_dump( base64_encode($strDecoded)); //exit;*/
        try{

            $isSigned = openssl_sign($strDecoded, $strEncoded, $priKeyPem , OPENSSL_ALGO_SHA1);

            if ($isSigned == false){
                throw new Exception('Não é possível gerar o hash de assinatura.');
            }

        }catch (\Exception $e){
            throw new Exception('Não é possível gerar o hash de assinatura. '. $e->getMessage());
        }


        // codifica o hash gerado

        //echo base64_encode($strEncoded); exit;
        return base64_encode( $strEncoded);
    }

    public static function sign($xml, $priKeyPem, $pubKeyClean,array $tagname = ['PedidoEnvioLoteRPS']){
        $algorithm = OPENSSL_ALGO_SHA1;
        $canonical = [false,false,null,null];

        $nsDSIG = 'http://www.w3.org/2000/09/xmldsig#';
        $nsCannonMethod = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
        $nsSignatureMethod = 'http://www.w3.org/2000/09/xmldsig#rsa-sha1';
        $nsDigestMethod = 'http://www.w3.org/2000/09/xmldsig#sha1';
        $digestAlgorithm = 'sha1';
        if ($algorithm == OPENSSL_ALGO_SHA256) {
            $digestAlgorithm = 'sha1';
            $nsSignatureMethod = 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256';
            $nsDigestMethod = 'http://www.w3.org/2001/04/xmlenc#sha256';
        }
        $nsTransformMethod1 ='http://www.w3.org/2000/09/xmldsig#enveloped-signature';
        $nsTransformMethod2 = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';

        if(empty($xml)){
            throw new Exception('O xml não pode ser vazio');
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        if (! is_array($tagname))
            throw  new \Exception('tagname precisa ser um array na assinatura');
        else
            foreach ($tagname as $tag){
                $existTag = $dom->getElementsByTagName($tag)->item(0);
                if (empty($existTag))
                    throw new \Exception('Tag '. $tag . ' não encontrada para asiinatura');
            }

        // Se estiver tudo ok assina

        foreach ($tagname as $tag){
            $nodes = $dom->getElementsByTagName($tag);
            for($i = 0; $i < $nodes->length; $i++){
                $node = $nodes->item($i);

                $idSigned = '';
                // Obtem o digest value
                $c14n = $node->C14N($canonical[0], $canonical[1], $canonical[2], $canonical[3]);
                $hashValue = hash($digestAlgorithm, $c14n, true);
                $digestValue = base64_encode($hashValue);

                $signatureNode = $dom->createElementNS($nsDSIG, 'Signature');
                $node->appendChild($signatureNode);
                $signedInfoNode = $dom->createElement('SignedInfo');
                $signatureNode->appendChild($signedInfoNode);
                $canonicalNode = $dom->createElement('CanonicalizationMethod');
                $signedInfoNode->appendChild($canonicalNode);
                $canonicalNode->setAttribute('Algorithm', $nsCannonMethod);
                $signatureMethodNode = $dom->createElement('SignatureMethod');
                $signedInfoNode->appendChild($signatureMethodNode);
                $signatureMethodNode->setAttribute('Algorithm', $nsSignatureMethod);
                $referenceNode = $dom->createElement('Reference');
                $signedInfoNode->appendChild($referenceNode);
                if (!empty($idSigned)) {
                    $idSigned = "#$idSigned";
                }
                $referenceNode->setAttribute('URI', $idSigned);
                $transformsNode = $dom->createElement('Transforms');
                $referenceNode->appendChild($transformsNode);
                $transfNode1 = $dom->createElement('Transform');
                $transformsNode->appendChild($transfNode1);
                $transfNode1->setAttribute('Algorithm', $nsTransformMethod1);
                $transfNode2 = $dom->createElement('Transform');
                $transformsNode->appendChild($transfNode2);
                $transfNode2->setAttribute('Algorithm', $nsTransformMethod2);
                $digestMethodNode = $dom->createElement('DigestMethod');
                $referenceNode->appendChild($digestMethodNode);
                $digestMethodNode->setAttribute('Algorithm', $nsDigestMethod);
                $digestValueNode = $dom->createElement('DigestValue', $digestValue);
                $referenceNode->appendChild($digestValueNode);
                // var_dump($signedInfoNode->C14N(true, false, null, null)); exit;
                $c14n  = $signedInfoNode->C14N($canonical[0], $canonical[1], $canonical[2], $canonical[3]);


                // Assinatura com Certificado

                if (!openssl_sign($c14n, $signature, $priKeyPem, $algorithm)) {
                    throw new \Exception('Erro ao gerar a assinatura pelo openssl');
                }

                $signatureValue = base64_encode($signature);
                $signatureValueNode = $dom->createElement('SignatureValue', $signatureValue);
                $signatureNode->appendChild($signatureValueNode);
                $keyInfoNode = $dom->createElement('KeyInfo');
                $signatureNode->appendChild($keyInfoNode);
                $x509DataNode = $dom->createElement('X509Data');
                $keyInfoNode->appendChild($x509DataNode);

                $x509CertificateNode = $dom->createElement('X509Certificate', $pubKeyClean);
                $x509DataNode->appendChild($x509CertificateNode);

            }
        }

        return $dom->saveXML();

    }



}