<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class AdminController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function adminAction() {
        var_dump('lel');
    }
}