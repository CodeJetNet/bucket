<?php

namespace CodeJet\Bucket\Exception;

use Interop\Container\Exception\NotFoundException as InteropNotFoundException;

class NotFoundException extends \Exception implements InteropNotFoundException
{

}
