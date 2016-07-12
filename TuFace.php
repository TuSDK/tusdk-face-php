<?php

/**
 * TuSDK 人脸服务API调用示例类
 *
 * @author TuSDK
 */
class TuFace {

    /**
     * api 服务地址 
     */
    const API_URL = 'http://srv.tusdk.com/srv/face/';

    /**
     *  私有key
     */
    public $key = '';

    /**
     * 公有key
     */
    public $pid = '';

    /**
     * 请求参数
     * 
     * @var array
     */
    protected $params;

    public function __construct($file = null) {
        // 初始化参数列表, 设置公有key
        $this->params = array(
            'pid' => $this->pid,
        );

        // 图片文件参数
        if (!is_null($file)) {
            $this->setFile($file);
        }
    }

    public function setFile($file) {

        if (filter_var($file, FILTER_VALIDATE_URL)) {
            $fileField = $this->params['url'] = $file;
        } elseif (is_file($file)) {
            $fileField = $this->params['pic'] = $this->curl_file_create(realpath($file));
        } else {
            throw new Exception('file does not exist');
        }
        return $fileField;
    }

    /**
     * 请求接口
     * 
     * @param string $method 接口方法
     * @param array $params 请求参数
     * @return array
     */
    public function request($method, $params = null) {
        if (empty($this->key) || empty($this->pid)) {
            throw new Exception('empty key or pid');
        }

        if (!isset($this->params['url']) && !isset($this->params['pic'])) {
            throw new Exception('param url or pic requried');
        }

        $apiUrl = self::API_URL . $method;

        $postFields = is_array($params) ? array_merge($this->params, $params) : $this->params;

        //设置时间戳参数
        $postFields['t'] = time();

        //设置签名参数
        $postFields['sign'] = $this->sign($postFields);

        $response = $this->curlPost($apiUrl, $postFields);
        return json_decode($response, true);
    }

    /**
     * 参数签名
     * 
     * @param array $params
     * @return string
     */
    protected function sign($params) {
        //图片参数 pic 不参与签名
        if (isset($params['pic'])) {
            unset($params['pic']);
        }
        //参数名排序
        ksort($params);

        $signStr = '';

        //连接 参数名.参数值
        foreach ($params as $para => $value) {
            //参数名转为小写
            $signStr .= strtolower($para) . $value;
        }

        //排序后的字符串接上 私有key
        $signStr .= $this->key;

        //返回md5字符串
        return md5($signStr);
    }

    /**
     * curl post
     * 
     * @param string $url
     * @param array $postFields
     * @return string
     * @throws Exception
     */
    protected function curlPost($url, $postFields) {
        if (!function_exists('curl_init')) {
            throw new Exception('Do not support CURL function.');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 18600);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            throw new Exception($error);
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode >= 200 && $httpCode < 300) {
            return $response;
        }
        throw new Exception('curl http code: ' . $httpCode);
    }

    /**
     * 生成 CURLFile
     * 
     * @param type $filename
     * @param type $mimetype
     * @param type $postname
     * @return type
     */
    protected function curl_file_create($filename, $mimetype = '', $postname = '') {
        if (function_exists('curl_file_create')) {
            return curl_file_create($filename, $mimetype, $postname);
        }
        //兼容处理
        return "@$filename;filename="
                . ($postname ? : basename($filename))
                . ($mimetype ? ";type=$mimetype" : '');
    }

}
