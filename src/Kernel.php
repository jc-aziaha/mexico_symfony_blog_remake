<?php

namespace App;

use App\Trait\TimeZoneTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
    use TimeZoneTrait;

    public function __construct(string $environment, bool $debug)
    {

        $this->changeTimeZone("Europe/Paris");
        
        parent::__construct($environment, $debug);
    }
}
