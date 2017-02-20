<?php

/* клaсс для определения географии пользователей */
class GeoIp {

    public function __construct() {
        $this->log = Logger::getLogger(__CLASS__);
    }

    private function getIP()
    {
        $serverVars = array(
            "HTTP_X_FORWARDED_FOR",
            "HTTP_X_FORWARDED",
            "HTTP_FORWARDED_FOR",
            "HTTP_FORWARDED",
            "HTTP_VIA",
            "HTTP_X_COMING_FROM",
            "HTTP_COMING_FROM",
            "HTTP_CLIENT_IP",
            "HTTP_XROXY_CONNECTION",
            "HTTP_PROXY_CONNECTION",
            "HTTP_USERAGENT_VIA"
        );
        foreach ($serverVars as $serverVar) {
            if (!empty($_SERVER) && !empty($_SERVER[$serverVar])) {
                $proxyIP = $_SERVER[$serverVar];
            } elseif (!empty($_ENV) && isset($_ENV[$serverVar])) {
                $proxyIP = $_ENV[$serverVar];
            } elseif (@getenv($serverVar)) {
                $proxyIP = getenv($serverVar);
            }
        }
        if (!empty($proxyIP)) {
            $isIP = preg_match('|^([0-9]{1,3}\.){3,3}[0-9]{1,3}|', $proxyIP, $regs);
            $long = ip2long($regs[0]);
            if ($isIP && (sizeof($regs) > 0) && $long != -1 && $long !== false) {
                return $regs[0];
            }
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    private function getLocationWithIP($ipAddress){
        $geoInfo = json_decode(file_get_contents('http://ru.sxgeo.city/json/'.$ipAddress), true);
        $locationInfo = [
            'country' => $geoInfo['country']['name_en'],
            'region' => $geoInfo['region']['name_en'],
            'city' => $geoInfo['city']['name_en'],
            'phone' => $geoInfo['country']['phone'],
            'ip' => $ipAddress
        ];
        return $locationInfo;
    }

    public function getUserLocation(){
        $ipAddress = $this->getIP();
        return $this->getLocationWithIP($ipAddress);
    }
}
