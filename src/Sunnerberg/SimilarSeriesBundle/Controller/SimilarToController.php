<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sunnerberg\SimilarSeriesBundle\Helper\Image\TmdbBackdropSize;
use Sunnerberg\SimilarSeriesBundle\Helper\Image\TmdbPosterSize;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SimilarToController extends Controller
{

    /**
     * @Route("/to/{slug}", name="similar_to_route", methods={"get"})
     */
    public function indexAction($slug)
    {
        $showRepository = $this->getDoctrine()->getRepository('SunnerbergSimilarSeriesBundle:TvShow');

        $show = $showRepository->findOneBy(['slug' => $slug]);
        if (! $show) {
            throw $this->createNotFoundException();
        }

        // @todo This injection of base url is insane.
        // @todo Display actors
        // @todo Title + SEO

        $imageHelper = $this->get('sunnerberg_similar_series.helper.tmdb_image_helper');
        $show->getBackdropImage()->setBaseUrl($imageHelper->getImageBaseUrl(TmdbBackdropSize::W1280));
        $show->getPosterImage()->setBaseUrl($imageHelper->getImageBaseUrl(TmdbPosterSize::W185));

        $similarShows = array_slice($show->getSimilarTvShows()->toArray(), 0, 6);
        $similarBaseUrl = $imageHelper->getImageBaseUrl(TmdbPosterSize::W342);
        foreach ($similarShows as $similar) {
            $similar->getPosterImage()->setBaseUrl($similarBaseUrl);
        }

        return $this->render('SunnerbergSimilarSeriesBundle:SimilarTo:similar-to.html.twig', ['show' => $show]);
    }

}
