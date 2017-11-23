<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Profile;
use AppBundle\Services\Configuration;
use AppBundle\Services\Utiles;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;

class UtilesExtension extends \Twig_Extension
{
    private $functions;
    private $userManager;
    private $configuration;

    public function __construct(Utiles $functions, UserManagerInterface $userManager, Configuration $configuration)
    {
        $this->functions = $functions;
        $this->userManager = $userManager;
        $this->configuration = $configuration;
    }

    public function getFunctions()
    {
        return array(
            'in_str'   => new \Twig_Function_Method( $this, 'in_str' ),
            'get_slug' => new \Twig_Function_Method( $this, 'getSlug' ),
            'get_bono' => new \Twig_Function_Method( $this, 'getBono' ),
        );
    }

    public function in_str( $haystack, $needle )
    {
        return $this->functions->in_str($haystack, $needle);
    }

    public function getSlug( $cadena, $separador = '-' )
    {
        return $this->functions->get_slug($cadena, $separador);
    }

    /**
     * @param $user
     * @return int
     */
    public function getBono($user) {
        $bono = 0;
        if(empty($user))
            return $bono;

        if(is_string($user)) {
            $user = $this->userManager->findUserByUsername($user);
        }

        if ($user instanceof UserInterface) {
            $demoChildren = $user->getActiveProfile()->getDemoChildren();
            if(!empty($demoChildren)) {
                foreach ($demoChildren as $children) {
                    if(Profile::PROFILE_PROFESSIONAL == $children->getType()) {
                        $bono += (integer)$this->configuration->findByField('third_comition')->getValue();
                    } elseif(Profile::PROFILE_SUPERIOR == $children->getType()) {
                        $bono += (integer)$this->configuration->findByField('secound_comition')->getValue();
                    } else {
                        $bono += (integer)$this->configuration->findByField('first_comition')->getValue();
                    }
                }
            }
        }

        return $bono;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'utiles';
    }
}