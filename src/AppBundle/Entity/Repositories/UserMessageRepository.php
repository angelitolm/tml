<?php

namespace AppBundle\Entity\Repositories;
use AppBundle\Entity\UserMessage;
use Doctrine\ORM\QueryBuilder;

/**
 * UserMessageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserMessageRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param UserMessage $entity
     */
    public function update(UserMessage $entity)
    {
        $this->_em->persist($entity);
        $this->_em->flush();
    }

    /**
     * @param UserMessage $entity
     */
    public function remove(UserMessage $entity)
    {
        $this->_em->remove($entity);
        $this->_em->flush();
    }

    /**
     * @param int $user
     * @param int $start
     * @param int $limit
     * @param array $order
     * @param string $filter
     * @return array
     */
    public function datableElement($user, $start = 0, $limit = 10, $order = array(), $filter ='')
    {
        $query = $this->createQueryBuilder('user_message');
        $query->innerJoin('user_message.user','user');
        $query->setFirstResult($start);
        $query->setMaxResults($limit);
        $query->where($query->expr()->eq('user.id',':user'));
        $query->setParameter('user', $user);
        /*if(!empty($order)) {
            switch($order['column']) {
                case 1:
                    $query->addOrderBy('post.title',$order['dir']);
                    break;
                default:
                    $query->addOrderBy('post.created',$order['dir']);
            }
        }*/
        $query->addOrderBy('user.created','DESC');

        if(!empty($filter)) {
            $query = $this->addQueryFilter($query, $filter);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getCountTotal($user)
    {
        $query = $this->createQueryBuilder('user_message');
        $query->innerJoin('user_message.user','user');
        $query->where($query->expr()->eq('user.id',':user'));
        $query->setParameter('user', $user);
        $query->select($query->expr()->count('user_message.id'));

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * @param $user
     * @param $filter
     * @return mixed
     */
    public function getFilterTotal($user, $filter)
    {
        $query = $this->createQueryBuilder('user_message');
        $query->innerJoin('user_message.user','user');
        $query->where($query->expr()->eq('user.id',':user'));
        $query->setParameter('user', $user);
        $query->select($query->expr()->count('user_message.id'));
        if(!empty($filter)) {
            $query = $this->addQueryFilter($query, $filter);
        }

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * @param QueryBuilder $query
     * @param $filter
     * @return QueryBuilder
     */
    private function addQueryFilter(QueryBuilder $query, $filter)
    {
        $expr = $query->expr();
        $query->andWhere(
            $expr->orX(
                $expr->like('user_message.send',':filter_str'),
                $expr->like('user_message.subject',':filter_str'),
                $expr->like('user_message.message',':filter_str')
            )
        );
        $query->setParameter('filter_str', '%'.$filter.'%');
        return $query;
    }
}
