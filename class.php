<?php
// ========== ملف الكلاسات - التعامل مع API ==========

class MainClass
{
    private $user, $pass, $app, $key;
    private $link = 'http://api.durianrcs.com/out/ext_api/';

    const METHOD_MOBILE = 'getMobile';
    const METHOD_MSG = 'getMsg';
    const METHOD_BLACK = 'addBlack';

    public function __construct($user, $pass, $app, $key)
    {
        $this->user = $user;
        $this->pass = $pass;
        $this->app = $app;
        $this->key = $key;
    }

    public function returnLinkWithMethodAndMainData($method)
    {
        return $this->link . $method . '?name=' . $this->user . '&pwd=' . $this->pass . '&ApiKey=' . $this->key . '&pid=' . $this->app;
    }

    public function getNumber($countryCode, $blackList = 0)
    {
        $url = $this->returnLinkWithMethodAndMainData(self::METHOD_MOBILE) . "&cuy=$countryCode&num=1&noblack=$blackList&serial=2";
        $response = json_decode(file_get_contents($url));

        if ($response->code == 200) {
            $num = $response->data;

            if (empty($num)) {
                return ['Error' => 'Empty number'];
            } else {
                return ['Error' => null, 'num' => $num, 'id' => $this->app];
            }
        } else {
            return ['Error' => $response->msg];
        }
    }

    public function getCode($num, $id)
    {
        $url = $this->returnLinkWithMethodAndMainData(self::METHOD_MSG) . "&pn=$num&serial=2";
        $response = json_decode(file_get_contents($url));

        if ($response->code == 200) {
            $code = $response->data;

            if (empty($code)) {
                return ['Error' => 'Empty code'];
            } else {
                return ['Error' => null, 'code' => $code];
            }
        } else {
            return ['Error' => $response->msg];
        }
    }

    public function banNum($num, $id)
    {
        $url = $this->returnLinkWithMethodAndMainData(self::METHOD_BLACK) . "&pn=$num";
        $response = json_decode(file_get_contents($url));

        return $response->msg;
    }
}
?>