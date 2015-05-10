<?php
// src/AppBundle/Entity/UserRepository.php
namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;

class StaffInfoRepository extends EntityRepository implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        $user = $this->createQueryBuilder('u')
            ->where('u.account = :account')
            ->setParameter('account', $username)
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $user) {
            $message = sprintf(
                'Unable to find an active admin AppBundle:User object identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message);
        }

        //throw new \Exception(implode(' , ',$user->getRoles()));

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }
        //throw new \Exception('refreshUser');
        return $this->find($user->getId());
    }

    public function supportsClass($class)
    {

        return $this->getEntityName() === $class
        || is_subclass_of($class, $this->getEntityName());
    }
}
