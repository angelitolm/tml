<?php

namespace AppBundle\Controller;

use AppBundle\Entity\News;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PageextrasController extends Controller
{
    /**
     * @Route("/tool_dashboard", name="tool_dashboard")
     */
    public function tool_dashboardAction()
    {
        return $this->render('AppBundle:Extras:tool_dashboard.html.twig');
    }

    /**
     * @Route("/tool_comisiones", name="tool_comisiones")
     */
    public function tool_comisionesAction()
    {
        return $this->render('AppBundle:Extras:tool_comisiones.html.twig');
    }

    /**
     * @Route("/tool_invitados", name="tool_invitados")
     */
    public function tool_invitadosAction()
    {
        return $this->render('AppBundle:Extras:tool_invitados.html.twig');
    }

    /**
     * @Route("/tool_reportes", name="tool_reportes")
     */
    public function tool_reportesAction()
    {
        return $this->render('AppBundle:Extras:tool_reportes.html.twig');
    }

    /**
     * @Route("/tool_strategias_marketing", name="tool_strategias_marketing")
     */
    public function tool_strategias_marketingAction()
    {
        return $this->render('AppBundle:Extras:tool_estrategias.html.twig');
    }

    /**
     * @Route("/tool_iweb", name="tool_iweb")
     */
    public function tool_iwebAction()
    {
        return $this->render('AppBundle:Extras:tool_iweb.html.twig');
    }

    /**
     * @Route("/tool_adminafiliados", name="tool_adminafiliados")
     */
    public function tool_adminafiliadosAction()
    {
        return $this->render('AppBundle:Extras:tool_adminafiliados.html.twig');
    }

    /**
     * @Route("/tool_mensajeria", name="tool_mensajeria")
     */
    public function tool_mensajeriaAction()
    {
        return $this->render('AppBundle:Extras:tool_serviciosadd.html.twig');
    }

    /**
     * @Route("/tool_club", name="tool_club")
     */
    public function tool_clubAction()
    {
        return $this->render('AppBundle:Extras:tool_club.html.twig');
    }

    /**
     * @Route("/tool_cursos", name="tool_cursos")
     */
    public function tool_cursosAction()
    {
        return $this->render('AppBundle:Extras:tool_cursos.html.twig');
    }

    /**
     * @Route("/news/{id}", name="news")
     * @ParamConverter("entity")
     */
    public function newsAction(News $entity)
    {
        $latestNews1 = $this->get('tml.news')->getLatestNews(3);

        return $this->render('AppBundle:Extras:news.html.twig', array(
            'news'=>$entity,
            'news1' => $latestNews1
        ));
    }
}
