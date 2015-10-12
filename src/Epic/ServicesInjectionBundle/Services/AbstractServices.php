<?php

namespace Epic\ServicesInjectionBundle\Services;

use Epic\ServicesInjectionBundle\Interfaces\BaseServicesInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Entity;

abstract class AbstractServices implements BaseServicesInterface
{
    protected $repository;
    protected $entityRepository;
    protected $entityName;
    protected $entityShortcutName;

    public function __construct(EntitiesRepository $entityRepository, $persistentManagerName = 'default')
    {
        $this->entityRepository = $entityRepository;
        $this->repository = $entityRepository->getRepository($this->getEntityShortcutName(), $persistentManagerName);
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * @return string
     */
    public function getEntityShortcutName()
    {
        return $this->entityShortcutName;
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    public function getDoctrine()
    {
        return $this->entityRepository->getDoctrine();
    }

}