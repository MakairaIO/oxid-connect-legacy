<?php

use Makaira\Connect\Result\Error;

class makaira_connect_endpoint extends oxUBase
{
    protected $statusCodes = array (
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
        510 => 'Not Extended'
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

        try {
            $updates = $this->getUpdatesAction();
            echo json_encode($updates);
        /*} catch (ForbiddenException $e) {
            $this->setStatusHeader(403);
            echo json_encode(new Error('Forbidden'));*/
        } catch (\Exception $e) {
            $this->setStatusHeader($e->getCode());
            echo json_encode(new Error($e->getMessage()));
        }

        exit();
    }

    public function getUpdatesAction()
    {
        // @TODO: Verify shared secret
        /** @var \Marm\Yamm\DIC $dic */
        $dic = oxRegistry::get('yamm_dic');
        $since = oxRegistry::getConfig()->getRequestParameter('since');
        /** @var \Makaira\Connect\Repository $repository */
        $repository = $dic['makaira.connect.repository'];
        return $repository->getChangesSince($since);
    }

    protected function setStatusHeader($statusCode) {
        if (isset($this->statusCodes[$statusCode])) {
            $string = $statusCode . ' ' . $this->statusCodes[$statusCode];
            header('HTTP/1.1 ' . $string, true, $statusCode);
        } else {
            $this->setStatusHeader(500);
        }
    }
}
