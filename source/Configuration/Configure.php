<?php
/**
 * 2007-2016 [PagSeguro Internet Ltda.]
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    PagSeguro Internet Ltda.
 * @copyright 2007-2016 PagSeguro Internet Ltda.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 *
 */

namespace PagSeguro\Configuration;

use PagSeguro\Domains\AccountCredentials;
use PagSeguro\Domains\ApplicationCredentials;
use PagSeguro\Domains\Charset;
use PagSeguro\Domains\Environment;
use PagSeguro\Domains\Log;
use PagSeguro\Parsers\ConfigureParser;
use PagSeguro\Resources\Responsibility;

/**
 * Class Configure
 * @package PagSeguro\Configuration
 */
class Configure
{
    use ConfigureParser;

    private static $configGenerated = false;

    private static $accountCredentials;
    private static $applicationCredentials;
    private static $charset;
    private static $environment;
    private static $log;

    private static $defaultConf = [
        'environment' => null,
        'credentials' => [
            'email' => null,
            'token' => [
                'production' => null,
                'sandbox' => null
            ],
            'appId' => [
                'production' => null,
                'sandbox' => null
            ],
            'appKey' => [
                'production' => null,
                'sandbox' => null
            ]
        ],
        'charset' => 'UTF-8',
        'log' => [
            'active' => false,
            'location' => null
        ]
    ];

    public static function fromLib()
    {
        if(self::$configGenerated === true){
            return;
        }

        $confLib = Responsibility::configuration();

        $confLib['credentials']['token'] = $confLib['credentials']['token']['environment'];
        $confLib['credentials']['appId'] = $confLib['credentials']['appId']['environment'];
        $confLib['credentials']['appKey'] = $confLib['credentials']['appKey']['environment'];

        self::combineConfig($confLib);
    }

    public static function fromArray(array $params = [])
    {
        if(self::$configGenerated === false){
            self::combineConfig($params);
        }
    }

    public static function fromXmlFile(string $file = null)
    {
        if(self::$configGenerated === true){
            return;
        }
        $fileExt = pathinfo($file, PATHINFO_EXTENSION);
        if($fileExt !== 'xml' and !file_exists($file)){
            throw new \InvalidArgumentException('File configuration not found');
        }

        $confXml = new \SimpleXMLElement($file, null, true);
        $confArr = self::xmlToArray($confXml);

        self::combineConfig($confArr);
    }

    /**
     * @return AccountCredentials
     */
    public static function getAccountCredentials()
    {
        if (self::$accountCredentials instanceof AccountCredentials) {
            return self::$accountCredentials;
        }

        self::setAccountCredentials(
            self::$defaultConf['credentials']['email'],
            self::$defaultConf['credentials']['token'][self::$defaultConf['environment']]
        );

        return self::$accountCredentials;
    }
    
    /**
     * @param string $email
     * @param string $token
     */
    public static function setAccountCredentials($email, $token)
    {
        self::$accountCredentials = new AccountCredentials;
        self::$accountCredentials->setEmail($email)
            ->setToken($token);
    }

    /**
     * @return ApplicationCredentials
     */
    public static function getApplicationCredentials()
    {
        if(self::$applicationCredentials instanceof ApplicationCredentials){
            return self::$applicationCredentials;
        }

        self::setApplicationCredentials(
            self::$defaultConf['credentials']['appId'][self::$defaultConf['environment']],
            self::$defaultConf['credentials']['appKey'][self::$defaultConf['environment']]
        );

        return self::$applicationCredentials;
    }
    
    /**
     * @param string $appId
     * @param string $appKey
     */
    public static function setApplicationCredentials($appId, $appKey)
    {
        self::$applicationCredentials = new ApplicationCredentials;
        self::$applicationCredentials->setAppId($appId)
            ->setAppKey($appKey);
    }

    /**
     * @return Environment
     */
    public static function getEnvironment()
    {
        if (!empty(self::$environment)) {
            return self::$environment;
        }
        self::setEnvironment(self::$defaultConf['environment']);
        return self::$environment;
    }
    
    /**
     * @param string $environment
     */
    public static function setEnvironment($environment)
    {
        self::$environment = new Environment;
        self::$environment->setEnvironment($environment);
    }

    /**
     * @return Charset
     */
    public static function getCharset()
    {
        if (!empty(self::$charset)) {
            return self::$charset;
        }
        self::setCharset(self::$defaultConf['charset']);
        return self::$charset;
    }
    
    /**
     * @param string $charset
     */
    public static function setCharset($charset)
    {
        self::$charset = new Charset;
        self::$charset->setEncoding($charset);
    }

    /**
     * @return Log
     */
    public static function getLog()
    {
        if (self::$log instanceof Log) {
            return self::$log;
        }
        self::setLog(
            self::$defaultConf['log']['active'] === "false" ? false : true,
            self::$defaultConf['log']['location']
        );
        return self::$log;
    }
    
    /**
     * @param boolean $active
     * @param string $location
     */
    public static function setLog($active, $location)
    {
        self::$log = new Log;
        self::$log->setActive($active)
            ->setLocation($location);
    }
}
