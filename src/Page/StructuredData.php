<?php

declare(strict_types=1);

namespace Technum\Page;

use Technum\Content\SiteInfo;

final class StructuredData
{
    /**
     * Données structurées schema.org de TECHNUM, sûres dans une balise script grâce à JSON_HEX_TAG.
     */
    public static function organization(SiteInfo $site): string
    {
        return json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'TECHNUM',
            'slogan' => 'La technologie à votre portée',
            'url' => 'https://bytechnum.com/',
            'logo' => 'https://bytechnum.com/assets/img/logo-technum-carre-512.png',
            'email' => $site->email,
            'telephone' => $site->phoneE164,
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Porto-Novo',
                'addressCountry' => 'BJ',
            ],
            'founder' => [
                '@type' => 'Person',
                'name' => 'Elisée Magloire ATONDE',
                'url' => 'https://moi.bytechnum.com',
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_THROW_ON_ERROR);
    }
}
