<?php

declare(strict_types=1);

namespace Technum;

use Technum\Clock\Clock;
use Technum\Clock\SystemClock;
use Technum\Contact\FormToken;
use Technum\Contact\LogMailer;
use Technum\Contact\MailerInterface;
use Technum\Contact\SmtpMailer;
use Technum\Content\ContentRepository;
use Technum\Controller\ContactController;
use Technum\Controller\ErrorController;
use Technum\Controller\HomeController;
use Technum\Controller\LegalController;
use Technum\Http\Request;
use Technum\Http\Response;
use Technum\Http\Router;
use Technum\Page\HomePage;
use Technum\Security\RateLimiter;
use Technum\Security\SecurityLog;
use Technum\View\View;

/**
 * Assemble les services du site et déclare ses routes.
 */
final class Application
{
    private function __construct(
        private readonly Router $router,
        private readonly bool $isProduction,
        private readonly RateLimiter $rateLimiter,
        private readonly Clock $clock,
    ) {
    }

    public static function create(
        string $rootDir,
        Config $config,
        ?MailerInterface $mailer = null,
        ?Clock $clock = null,
        ?string $storageDir = null,
    ): self {
        $storageDir ??= $rootDir . '/storage';
        $clock ??= new SystemClock();
        $content = new ContentRepository($rootDir . '/content', $rootDir . '/public');
        $site = $content->site();
        $view = new View($rootDir . '/templates', $rootDir . '/public');
        $view->share(['site' => $site, 'products' => $content->products()]);

        $mailer ??= $config->mailTransport === 'log'
            ? new LogMailer($storageDir . '/logs/mail-local.log')
            : new SmtpMailer(
                $config->smtpHost,
                $config->smtpPort,
                $config->smtpUsername,
                $config->smtpPassword,
                $config->contactSenderEmail,
                $config->contactRecipientEmail,
            );

        $formToken = new FormToken($config->appSecret);
        $rateLimiter = new RateLimiter($storageDir . '/rate-limit', $config->appSecret);
        $homePage = new HomePage($view, $content);
        $home = new HomeController($homePage, $formToken, $clock);
        $contact = new ContactController(
            $homePage,
            $formToken,
            $rateLimiter,
            $mailer,
            new SecurityLog($storageDir . '/logs/security.log'),
            $clock,
            $site,
        );
        $errors = new ErrorController($view);

        $router = new Router($errors->notFound(...));
        $router->get('/', $home->show(...));
        $router->get('/contact', static fn (Request $request): Response => Response::redirect('/#contact', 301));
        // Seule adresse de l'ancienne page « Bientôt en ligne ».
        $router->get('/index.html', static fn (Request $request): Response => Response::redirect('/', 301));
        $router->post('/contact', $contact->submit(...));
        $legal = new LegalController($view);
        foreach (LegalController::pages() as $page) {
            $router->get('/' . $page, static fn (Request $request): Response => $legal->show($page));
        }

        return new self($router, $config->isProduction(), $rateLimiter, $clock);
    }

    public function handle(Request $request): Response
    {
        // Chaque requête efface les empreintes de plus d'une heure, comme le promet la politique de confidentialité.
        $this->rateLimiter->purgeExpired($this->clock->now());
        $response = $this->router->dispatch($request);

        return $this->isProduction
            ? $response->withHeader('Strict-Transport-Security', 'max-age=31536000')
            : $response;
    }
}
