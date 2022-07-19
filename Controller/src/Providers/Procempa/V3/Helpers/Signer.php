<?php
/**
 * Created by PhpStorm.
 * User: Moisés
 * Date: 25/05/2019
 * Time: 17:11
 */

namespace Nfsews\Providers\Procempa\V3\Helpers;


class Signer
{

    public static function sign($xml, $priKeyPem, $pubKeyClean,array $tagname = ['CancelarNfseEnvio'])
    {
        $algorithm = OPENSSL_ALGO_SHA1;
        $canonical = [false, false, null, null];

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
        $nsTransformMethod1 = 'http://www.w3.org/2000/09/xmldsig#enveloped-signature';
        $nsTransformMethod2 = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';

        if (empty($xml)) {
            throw new Exception('O xml não pode ser vazio');
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        if (!is_array($tagname))
            throw  new \Exception('tagname precisa ser um array na assinatura');
        else
            foreach ($tagname as $tag) {
                $existTag = $dom->getElementsByTagName($tag)->item(0);
                if (empty($existTag))
                    throw new \Exception('Tag ' . $tag . ' não encontrada para asiinatura');
            }

        // Se estiver tudo ok assina

        foreach ($tagname as $tag) {
            $nodes = $dom->getElementsByTagName($tag);
            for ($i = 0; $i < $nodes->length; $i++) {
                $node = $nodes->item($i);
                // Obtem o digest value
                $idSigned = trim($node->getAttribute('Id'));
                $c14n = $node->C14N($canonical[0], $canonical[1], $canonical[2], $canonical[3]);
                $hashValue = hash($digestAlgorithm, $c14n, true);
                $digestValue = base64_encode($hashValue);

                $signatureNode = $dom->createElementNS($nsDSIG, 'Signature');
                $node->parentNode->appendChild($signatureNode);
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

                $c14n = $signedInfoNode->C14N($canonical[0], $canonical[1], $canonical[2], $canonical[3]);
                // Assinatura com Certificado
                $signature = '';
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