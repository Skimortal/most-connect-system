<?php
namespace App\Entity;

use App\Enum\EmailTemplateKey;
use App\Repository\EmailTemplateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailTemplateRepository::class)]
#[ORM\Table(name: 'email_template')]
#[ORM\UniqueConstraint(
    name: 'uniq_template_key_locale',
    columns: ['template_key', 'locale']
)]
class EmailTemplate extends Base
{

    #[ORM\Column(type: 'string', length: 190, enumType: EmailTemplateKey::class)]
    private EmailTemplateKey $templateKey;

    // z.B. "de", "en", "de_AT"
    #[ORM\Column(type: 'string', length: 10)]
    private string $locale;

    #[ORM\Column(name: 'subject_tpl', type: 'text')]
    private string $subjectTemplate;

    #[ORM\Column(name: 'html_tpl', type: 'text')]
    private string $htmlTemplate;

    #[ORM\Column(name: 'text_tpl', type: 'text', nullable: true)]
    private ?string $textTemplate = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $variablesHint = null;

    public function getTemplateKey(): EmailTemplateKey
    {
        return $this->templateKey;
    }

    public function setTemplateKey(EmailTemplateKey $key): self
    {
        $this->templateKey = $key;
        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getSubjectTemplate(): string
    {
        return $this->subjectTemplate;
    }

    public function setSubjectTemplate(string $subjectTemplate): void
    {
        $this->subjectTemplate = $subjectTemplate;
    }

    public function getHtmlTemplate(): string
    {
        return $this->htmlTemplate;
    }

    public function setHtmlTemplate(string $htmlTemplate): void
    {
        $this->htmlTemplate = $htmlTemplate;
    }

    public function getTextTemplate(): ?string
    {
        return $this->textTemplate;
    }

    public function setTextTemplate(?string $textTemplate): void
    {
        $this->textTemplate = $textTemplate;
    }

    public function getVariablesHint(): ?array
    {
        return $this->variablesHint;
    }

    public function setVariablesHint(?array $variablesHint): void
    {
        $this->variablesHint = $variablesHint;
    }

}
