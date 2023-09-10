<?php
namespace Zodream\Module\OAuth\Domain\Model;

/**
 * Class OAuthPublicKeyModel
 * @package Zodream\Module\OAuth\Domain
 * @property string $client_id
 * @property string $public_key
 * @property string $private_key
 * @property string $encryption_algorithm
 */
class OAuthPublicKeyModel extends BaseModel {

    const RSA2 = 'RSA256';
    const MD5 = 'MD5';
    const RSA = 'RSA';

    public static function tableName(): string {
        return 'oauth_public_key';
    }

    public function getSignType(): string {
        return strtoupper($this->get('encryption_algorithm', self::RSA2));
    }

    /**
     * 签名
     * @param array|string $content
     * @return string
     * @throws \Exception
     */
    public function sign($content) {
        if (is_array($content)) {
            $content = $this->getSignContent($content);
        }
        if ($this->getSignType() == self::MD5) {
            return md5($content.$this->public_key);
        }
        $priKey = $this->private_key;
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($priKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
        if (self::RSA2 == $this->getSignType()) {
            openssl_sign($content, $sign, $res, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($content, $sign, $res);
        }
        if (is_resource($res)) {
            openssl_free_key($res);
        }
        return base64_encode($sign);
    }

    protected function getSignContent(array $params) {
        ksort($params);
        $args = [];
        foreach ($params as $key => $item) {
            if ($this->isEmpty($item)
                || in_array($key, ['sign', 'sign_type'])
                || strpos($item, '@') === 0
            ) {
                continue;
            }
            $args[] = $key.'='.$item;
        }
        return implode('&', $args);
    }

    /**
     * 验签
     * @param array $params
     * @param string $sign
     * @return bool
     */
    public function verify(array $params, $sign = null) {
        if (is_null($sign)) {
            $sign = $params[$this->signKey];
        }
        $content = $this->getSignContent($params);
        $result = $this->verifyContent($content, $sign);
        if (!$result && strpos($content, '\\/') > 0) {
            $content = str_replace('\\/', '/', $content);
            return $this->verifyContent($content, $sign);
        }
        return $result;
    }

    public function verifyContent($content, $sign) {
        if ($this->getSignType() == self::MD5) {
            return md5($content. $this->public_key) == $sign;
        }
        $pubKey= $this->public_key;
        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($pubKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";

        //调用openssl内置方法验签，返回bool值

        if (self::RSA2 == $this->getSignType()) {
            $result = (bool)openssl_verify($content,
                base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool)openssl_verify($content, base64_decode($sign), $res);
        }

        //释放资源
        if (is_resource($res)) {
            openssl_free_key($res);
        }
        return $result;
    }

}