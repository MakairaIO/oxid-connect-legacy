<?php

use Makaira\Connect\Result\Error;
use Makaira\Connect\Result\ForbiddenException;
use Makaira\Connect\Utils\BoostFields;

class makaira_connect_endpoint extends oxUBase
{
    protected $statusCodes = array(
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
    );

    /**
     * Main render method
     *
     * Called by oxid – is supposed to render a Smarty template. In our case it
     * just returns a JSON response and dies afterwards.
     *
     * @return void
     */
    public function render()
    {
        ini_set('html_errors', false);
        header("Content-Type: application/json");

        if (!isset($_SERVER['HTTP_X_MAKAIRA_NONCE']) || !isset($_SERVER['HTTP_X_MAKAIRA_HASH'])) {
            $this->setStatusHeader(401);
            echo json_encode(new Error('Unauthorized'));
            exit();
        }

        if (!$this->verifySharedSecret()) {
            $this->setStatusHeader(403);
            echo json_encode(new Error('Forbidden'));
            exit();
        }

        try {
            $body = json_decode(file_get_contents('php://input'));
            if ($body === null) {
                throw new \RuntimeException("Failed to decode request body");
            }

            switch ($body->action) {
                case 'listLanguages':
                    $updates = $this->getLanguagesAction();
                    break;
                case 'getBoostFieldStatistics':
                    $updates = $this->getBoostFieldStatistics();
                    break;
                case 'loadUserByUsername':
                    $updates = new User([
                        'ok' => false
                    ]);
                    break;
                case 'loadUserByToken':
                    $updates = new User([
                        'ok' => false
                    ]);
                    break;
                case 'getReplicationStatus':
                    $updates = $this->getReplicationStatusAction($body);
                    break;
                case 'getVersionNumber':
                    $dic            = oxRegistry::get('yamm_dic');
                    $versionHandler = $dic['makaira.connect.version.handler'];
                    $updates        = $versionHandler->getVersionNumber();
                    break;
                case 'getUpdates':
                default:
                    $updates = $this->getUpdatesAction($body);
            }

            echo json_encode($updates);
        } catch (\Exception $e) {
            $this->setStatusHeader(500);
            $error = new Error($e->getMessage());

            if (!oxRegistry::getConfig()->isProductiveMode()) {
                $error->file = $e->getFile();
                $error->line = $e->getLine();
                $error->stack = explode(PHP_EOL, $e->getTraceAsString());
            }

            echo json_encode($error);
        }

        exit();
    }

    protected function verifySharedSecret()
    {
        $nonce  = isset($_SERVER['HTTP_X_MAKAIRA_NONCE']) ? $_SERVER['HTTP_X_MAKAIRA_NONCE'] : null;
        $hash   = isset($_SERVER['HTTP_X_MAKAIRA_HASH']) ? $_SERVER['HTTP_X_MAKAIRA_HASH'] : null;
        $secret = oxRegistry::getConfig()->getShopConfVar('makaira_connect_secret');
        $body   = file_get_contents('php://input');

        return ($hash === hash_hmac('sha256', $nonce . ':' . $body, $secret));
    }

    protected function setStatusHeader($statusCode)
    {
        if (isset($this->statusCodes[$statusCode])) {
            $string = $statusCode . ' ' . $this->statusCodes[$statusCode];
            header('HTTP/1.1 ' . $string, true, $statusCode);
        } else {
            $this->setStatusHeader(500);
        }
    }

    public function getUpdatesAction($body)
    {
        if (!isset($body->since)) {
            throw new \RuntimeException("since parameter not set");
        }

        /** @var \Marm\Yamm\DIC $dic */
        $dic = oxRegistry::get('yamm_dic');

        if (property_exists($body, 'language')) {
            $oxLang = $dic['oxid.language'];
            $language = $body->language;
            $langIds = $oxLang->getLanguageIds();
            $langIds = array_flip($langIds);
            if (isset($langIds[$language])) {
                /** @var oxLang $oxLang */
                $oxLang = $dic['oxid.language'];
                $oxLang->setBaseLanguage($langIds[$language]);
            }
        } else {
            $language = oxRegistry::getLang()->getLanguageAbbr();
        }

        /** @var \Makaira\Connect\Utils\TableTranslator $translator */
        $translator = $dic['oxid.table_translator'];
        $translator->setLanguage($language);

        /** @var \Makaira\Connect\Repository $repository */
        $repository = $dic['makaira.connect.repository'];

        $result = $repository->getChangesSince($body->since, isset($body->count) ? $body->count : 50);
        $result->language = $language;
        $result->highLoad = $this->checkSystemLoad();

        return $result;
    }

    protected function checkSystemLoad()
    {
        $loadLimit = oxRegistry::getConfig()->getShopConfVar(
            'makaira_connect_load_limit',
            null,
            oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
        );

        if (0 >= $loadLimit) {
            return false;
        }

        list(, $loadavg5min, ) = sys_getloadavg();
        return ($loadavg5min >= $loadLimit);
    }

    public function getLanguagesAction()
    {
        /** @var oxLang $lang */
        $lang = oxRegistry::getLang();
        return $lang->getLanguageIds();
    }

    public function getUserAction($body)
    {
        if (!isset($body->username)) {
            throw new \RuntimeException("username parameter not set");
        }

        /** @var \Marm\Yamm\DIC $dic */
        $dic = oxRegistry::get('yamm_dic');

        /** @var \Makaira\Connect\Repository\UserRepository $repository */
        $repository = $dic['makaira.connect.repository.user'];
        $user = $repository->getAuthorizedUserByUsername($body->username);

        return $user;
    }

    public function getCurrentUserAction($body)
    {
        if (!isset($body->token)) {
            throw new \RuntimeException("token parameter not set");
        }

        /** @var \Marm\Yamm\DIC $dic */
        $dic = oxRegistry::get('yamm_dic');

        /** @var \Makaira\Connect\Repository\UserRepository $repository */
        $repository = $dic['makaira.connect.repository.user'];
        $user = $repository->getAuthorizedUserByToken($body->token);

        return $user;
    }

    public function getReplicationStatusAction($body)
    {
        if (!isset($body->indices)) {
            return [];
        }

        /** @var \Marm\Yamm\DIC $dic */
        $dic = oxRegistry::get('yamm_dic');

        /** @var \Makaira\Connect\Repository $repository */
        $repository = $dic['makaira.connect.repository'];
        foreach ($body->indices as $index) {
            $index->openChanges = $repository->countChangesSince($index->lastRevision);
        }

        return $body->indices;
    }

    protected function getBoostFieldStatistics()
    {
        /** @var \Marm\Yamm\DIC $dic */
        $dic = oxRegistry::get('yamm_dic');

        /** @var BoostFields $boostFieldStatistics */
        $boostFieldStatistics = $dic['makaira.connect.utils.boostfields'];

        return $boostFieldStatistics->getMinMaxValues();
    }
}
