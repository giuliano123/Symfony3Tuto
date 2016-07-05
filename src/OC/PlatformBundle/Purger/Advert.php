<?php

namespace OC\PlatformBundle\Purger;

use Doctrine\ORM\EntityManager;

class advert
{

    private $manager;

    public function __construct(EntityManager $em)
    {
        $this->manager = $em;
    }

    public function purge($days)
    {
        $advertRepository = $this->em->getRepository('OCPlatformBundle:Advert');
        $advertSkillRepository = $this->em->getRepository('OCPlatformBundle:AdvertSkill');
        
        $date = new \Datetime($days . ' days ago');
        
        $listAdverts = $advertRepository->getAdvertsBefore($date);
        
        foreach ($listAdverts as $advert)
        {
            
            $advertSkills = $advertSkillRepository->findBy(array('advert' => $advert));
            
            foreach ($advertSkills as $advertSkill)
            {
                $this->em->remove($advertSkill);
            }
            
            $this->em->remove($advert);
        }
        
        $this->em->flush();
    }

}
