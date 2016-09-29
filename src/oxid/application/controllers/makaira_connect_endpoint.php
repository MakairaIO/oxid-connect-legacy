<?php

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
            echo json_encode($this->getUpdatesAction());
        } catch (ForbiddenException $e) {
            $this->setStatusHeader(403);
            echo json_encode(array(
                'ok' => false,
                'error' => 'Forbidden',
            ));
        } catch (\Exception $e) {
            $this->setStatusHeader(500);
            echo json_encode(array(
                'ok' => false,
                'error' => $e->getMessage(),
            ));
        }

        exit();
    }

    public function getUpdatesAction()
    {
        // @TODO: Verify shared secret

        $since = isset($_GET['since']) ? $_GET['since'] : 0;
        $repository = oxRegistry::get('yamm_dic')['makaira.connect.repository.product'];
        $changes = $repository->getChangesSince($since);
        return array(
            'ok' => true,
            'since' => $since,
            'count' => count($changes),
            'changes' => $changes,
        );
    }

    protected function setStatusHeader($statusCode) {
        if (isset($this->statusCode[$statusCode])) {
            $string = $statusCode . ' ' . $this->statusCodes[$statusCode];
            header($_SERVER['SERVER_PROTOCOL'] . ' ' . $string, true, $statusCode);
        }
    }
}
