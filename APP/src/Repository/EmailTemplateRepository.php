<?php
namespace App\Repository;

use App\Entity\Company;
use App\Entity\EmailTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EmailTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailTemplate::class);
    }

    /**
     * Lookup mit Fallback:
     * 1) (company, locale)    2) (company, lang)
     * 3) (NULL,   locale)     4) (NULL,   lang)
     * 5) (NULL,   default)     6) (NULL,   'en')
     */
    public function findOneByKeyCompanyLocaleFallback(
        string $key,
        ?Company $company,
        ?string $locale,
        string $defaultLocale = 'de'
    ): ?EmailTemplate {
        $lang = $locale ? substr($locale, 0, 2) : null;

        $candidates = [
            [$company, $locale],
            [$company, $lang],
            [null,     $locale],
            [null,     $lang],
            [null,     $defaultLocale],
            [null,     'en'],
        ];

        foreach ($candidates as [$c, $loc]) {
            if (!$loc) continue;
            $tpl = $this->findOneBy([
                'templateKey' => $key,
                'company'     => $c,
                'locale'      => $loc,
            ]);
            if ($tpl) return $tpl;
        }

        return null;
    }
}
