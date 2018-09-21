<?php
namespace MMoravac\AppNexusClient;

use RuntimeException;
use stdClass;

class ServerException extends RuntimeException
{
    /**
     * @var stdClass
     */
    private $response;

    /**
     * __construct
     *
     * @param stdClass $response Server response
     */
    public function __construct(stdClass $response)
    {
        parent::__construct(@$response->error);
        $this->response = $response;
    }

    /**
     * getResponse
     *
     * @return object
     */
    public function getResponse()
    {
        return $this->response;
    }
}
