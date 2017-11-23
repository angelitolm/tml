<?php

namespace AppBundle\Services;

use AppBundle\Entity\Repositories\SocialMessageRepository;
use Epic\ServicesInjectionBundle\Services\AbstractServices;
use FOS\UserBundle\Model\UserInterface;

class SocialMessage extends AbstractServices
{
    protected $entityName = '\AppBundle\Entity\SocialMessage';
    protected $entityShortcutName = 'AppBundle:SocialMessage';

    /**
     * @return SocialMessageRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param null|integer id
     *
     * @return \AppBundle\Entity\SocialMessage
     */
    public function getEntity($id = null)
    {
        if(is_integer($id)) {
            return $this->getRepository()->find($id);
        }
        return new $this->entityName();
    }

    /**
     * @param \AppBundle\Entity\SocialMessage $entity
     */
    public function update(\AppBundle\Entity\SocialMessage $entity)
    {
        $this->getRepository()->update($entity);
    }

    /**
     * @param \AppBundle\Entity\SocialMessage $entity
     */
    public function remove(\AppBundle\Entity\SocialMessage $entity)
    {
        $this->getRepository()->remove($entity);
    }

    /**
     * @param UserInterface $user
     * @param $message
     * @return \AppBundle\Entity\SocialMessage
     */
    public function registerMessage(UserInterface $user, $message) {
        $socialMessage = $this->getEntity();
        $socialMessage->setUser($user);
        $socialMessage->setMessage($message);
        $this->update($socialMessage);

        return $socialMessage;
    }

    /**
     * @param int $limit
     * @return array
     */
    public function getLatest($limit = 10) {
        return $this->getRepository()->getLatest($limit);
    }
}