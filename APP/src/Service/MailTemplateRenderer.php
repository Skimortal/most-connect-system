<?php
// src/Service/MailTemplateRenderer.php
namespace App\Service;

use App\Entity\Company;
use App\Enum\EmailTemplateKey;
use App\Repository\EmailTemplateRepository;
use Twig\Environment as TwigEnvironment;

class MailTemplateRenderer
{
    public function __construct(
        private readonly TwigEnvironment $twig,
        private readonly EmailTemplateRepository $repo,
        private readonly string $defaultLocale = 'de'
    ) {}

    public function render(
        EmailTemplateKey|string $key,
        ?string $locale,
        array $context,
        ?Company $company = null
    ): RenderedMail {
        $keyValue = $key instanceof EmailTemplateKey ? $key->value : $key;

        $tpl = $this->repo->findOneByKeyCompanyLocaleFallback(
            $keyValue,
            $company,
            $locale,
            $this->defaultLocale
        );

        if (!$tpl) {
            throw new \RuntimeException(sprintf(
                'Mail-Template "%s" (%s) nicht gefunden (Company: %s).',
                $keyValue, $locale ?? '—', $company?->getId() ?? '—'
            ));
        }

        $subject = $this->twig->createTemplate($tpl->getSubjectTemplate())->render($context);
        $html    = $this->twig->createTemplate($tpl->getHtmlTemplate())->render($context);
        $text    = $tpl->getTextTemplate()
            ? $this->twig->createTemplate($tpl->getTextTemplate())->render($context)
            : null;

        return new RenderedMail($subject, $html, $text);
    }
}

class RenderedMail
{
    public function __construct(
        public readonly string $subject,
        public readonly string $html,
        public readonly ?string $text
    ) {}
}
