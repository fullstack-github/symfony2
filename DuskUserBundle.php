<?php

namespace Dusk\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class DuskUserBundle extends Bundle
{
    public function getParent() {
        return 'SonataUserBundle';
    }
}
