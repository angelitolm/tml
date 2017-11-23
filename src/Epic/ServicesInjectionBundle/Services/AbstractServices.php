<?php

namespace Epic\ServicesInjectionBundle\Services;

use Epic\ServicesInjectionBundle\Interfaces\BaseServicesInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractServices implements BaseServicesInterface
{
    protected $repository;
    protected $entityRepository;
    protected $entityName;
    protected $entityShortcutName;
    protected $eventDispatcher;

    public function __construct(EntitiesRepository $entityRepository,EventDispatcherInterface $eventDispatcher, $persistentManagerName = 'default')
    {
        $this->entityRepository = $entityRepository;
        $this->eventDispatcher = $eventDispatcher;
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

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }


}