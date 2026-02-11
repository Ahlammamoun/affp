<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\ArticleReaction;
use App\Entity\Dossier;
use App\Entity\FooterLink;
use App\Entity\Media;
use App\Entity\NewsletterSubscriber;
use App\Entity\Section;
use App\Entity\SiteSetting;
use App\Entity\ArticleCard;
use App\Entity\Destination;
use App\Entity\PremiumBrief;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\FeaturedSlot;
use App\Entity\LiveUpdate;
use App\Entity\AdSlide;
use App\Entity\Event;


class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->redirect(
            $this->generateUrl('admin_article_index')
        );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle('AFRICA FACTS PRESS — Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Contenu');
        yield MenuItem::linkToCrud('Articles', 'fa fa-newspaper', Article::class);
        yield MenuItem::linkToCrud('Article Cards', 'fa fa-id-card', ArticleCard::class);
        yield MenuItem::linkToCrud('Dossiers', 'fa fa-folder-open', Dossier::class);
        yield MenuItem::linkToCrud('Médias', 'fa fa-image', Media::class);
        yield MenuItem::linkToCrud('Sections', 'fa fa-list', Section::class);
        yield MenuItem::section('Interactions');
        yield MenuItem::linkToCrud('Réactions', 'fa fa-heart', ArticleReaction::class);
        yield MenuItem::linkToCrud('Newsletter', 'fa fa-envelope', NewsletterSubscriber::class);
        yield MenuItem::linkToCrud('Premium Briefs', 'fa fa-star', PremiumBrief::class);
        yield MenuItem::section('Site');
        yield MenuItem::linkToCrud('Footer Links', 'fa fa-link', FooterLink::class);
        yield MenuItem::linkToCrud('Réglages', 'fa fa-cog', SiteSetting::class);
        yield MenuItem::linkToCrud('Mises en avant', 'fa fa-star', FeaturedSlot::class);
        yield MenuItem::linkToCrud('Actu en continu', 'fa fa-bolt', LiveUpdate::class);
        yield MenuItem::section('Monétisation');
        yield MenuItem::linkToCrud('Destinations', 'fa fa-map-marker-alt', Destination::class);
        yield MenuItem::linkToCrud('Zone commerciale', 'fa fa-bullhorn', AdSlide::class);
        yield MenuItem::linkToCrud('Événements', 'fa fa-calendar', Event::class);
        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-users', User::class);
    }
}
