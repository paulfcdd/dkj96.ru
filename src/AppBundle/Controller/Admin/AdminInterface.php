<?php


namespace AppBundle\Controller\Admin;


use Symfony\Component\HttpFoundation\Request;

interface AdminInterface
{
    public function listAction();
    public function createAction(Request $request, $entity);
    public function editAction(Request $request, $entity, $id);
}