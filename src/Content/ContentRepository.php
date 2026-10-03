<?php

declare(strict_types=1);

namespace Technum\Content;

use DateTimeImmutable;

/**
 * Charge et valide le contenu éditorial de content/*.php.
 */
final class ContentRepository
{
    private ?SiteInfo $site = null;

    /** @var list<Product>|null */
    private ?array $products = null;

    /** @var list<Project>|null */
    private ?array $projects = null;

    /** @var list<Service>|null */
    private ?array $services = null;

    public function __construct(
        private readonly string $contentDir,
        private readonly string $publicDir,
    ) {
    }

    public function site(): SiteInfo
    {
        return $this->site ??= $this->buildSite($this->load('site'));
    }

    /**
     * @return list<Product>
     */
    public function products(): array
    {
        return $this->products ??= array_map($this->buildProduct(...), $this->loadList('products'));
    }

    /**
     * @return list<Project>
     */
    public function projects(): array
    {
        return $this->projects ??= array_map($this->buildProject(...), $this->loadList('projects'));
    }

    /**
     * @return list<Service>
     */
    public function services(): array
    {
        return $this->services ??= array_map($this->buildService(...), $this->loadList('services'));
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function buildSite(array $data): SiteInfo
    {
        $context = 'site.php';
        $updatedAt = DateTimeImmutable::createFromFormat('!Y-m-d', $this->text($data, 'updatedAt', $context));
        if ($updatedAt === false) {
            throw new ContentException($context . ' : « updatedAt » doit suivre le format AAAA-MM-JJ');
        }
        $email = $this->text($data, 'email', $context);
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new ContentException($context . ' : « email » doit être une adresse valide');
        }
        $phoneE164 = $this->text($data, 'phoneE164', $context);
        if (preg_match('/^\+[0-9]{8,15}$/', $phoneE164) !== 1) {
            throw new ContentException($context . ' : « phoneE164 » doit ressembler à +2290150617300');
        }
        $whatsappNumber = $this->text($data, 'whatsappNumber', $context);
        if (preg_match('/^[0-9]{8,15}$/', $whatsappNumber) !== 1) {
            throw new ContentException($context . ' : « whatsappNumber » ne contient que des chiffres, sans +');
        }

        return new SiteInfo(
            email: $email,
            phoneDisplay: $this->text($data, 'phoneDisplay', $context),
            phoneE164: $phoneE164,
            whatsappNumber: $whatsappNumber,
            whatsappMessage: $this->text($data, 'whatsappMessage', $context),
            githubUrl: $this->httpsUrl($data, 'githubUrl', $context),
            city: $this->text($data, 'city', $context),
            updatedAt: $updatedAt,
        );
    }

    /**
     * @param array<array-key, mixed> $item
     */
    private function buildProduct(array $item): Product
    {
        $slug = $this->slug($item, 'products.php');
        $context = 'products.php, ' . $slug;
        $stage = ProductStage::tryFrom($this->text($item, 'stage', $context));
        if ($stage === null) {
            throw new ContentException($context . ' : « stage » doit valoir conception, pilote, beta ou en-service');
        }
        $image = $item['image'] ?? null;
        if (!is_array($image)) {
            throw new ContentException($context . ' : « image » doit être un tableau');
        }

        return new Product(
            slug: $slug,
            name: $this->text($item, 'name', $context),
            url: $this->httpsUrl($item, 'url', $context, required: false),
            tagline: $this->prose($item, 'tagline', $context),
            audience: $this->prose($item, 'audience', $context),
            stage: $stage,
            done: $this->prose($item, 'done', $context),
            next: $this->prose($item, 'next', $context, required: false),
            note: $this->prose($item, 'note', $context, required: false),
            icon: $this->asset($item, 'icon', $context),
            image: $this->buildImage($image, $context),
        );
    }

    /**
     * @param array<array-key, mixed> $image
     */
    private function buildImage(array $image, string $context): ProductImage
    {
        $context .= ', image';
        $frame = $this->text($image, 'frame', $context);
        if (!in_array($frame, ['desktop', 'phone'], true)) {
            throw new ContentException($context . ' : « frame » doit valoir desktop ou phone');
        }
        $srcSmall = $this->text($image, 'srcSmall', $context, required: false);

        return new ProductImage(
            src: $this->asset($image, 'src', $context),
            srcSmall: $srcSmall === '' ? '' : $this->asset($image, 'srcSmall', $context),
            width: $this->positiveInt($image, 'width', $context),
            height: $this->positiveInt($image, 'height', $context),
            alt: $this->text($image, 'alt', $context),
            frame: $frame,
        );
    }

    /**
     * @param array<array-key, mixed> $item
     */
    private function buildProject(array $item): Project
    {
        $slug = $this->slug($item, 'projects.php');
        $context = 'projects.php, ' . $slug;
        $linkUrl = $this->httpsUrl($item, 'linkUrl', $context, required: false);
        $linkLabel = $this->text($item, 'linkLabel', $context, required: false);
        if (($linkUrl === '') !== ($linkLabel === '')) {
            throw new ContentException($context . ' : « linkUrl » et « linkLabel » vont ensemble');
        }

        return new Project(
            slug: $slug,
            name: $this->text($item, 'name', $context),
            description: $this->prose($item, 'description', $context),
            nature: $this->text($item, 'nature', $context),
            linkUrl: $linkUrl,
            linkLabel: $linkLabel,
        );
    }

    /**
     * @param array<array-key, mixed> $item
     */
    private function buildService(array $item): Service
    {
        $name = $this->text($item, 'name', 'services.php');
        $context = 'services.php, ' . $name;
        $examples = $item['examples'] ?? null;
        if (!is_array($examples) || $examples === [] || !array_is_list($examples)) {
            throw new ContentException($context . ' : « examples » doit être une liste non vide');
        }
        $built = [];
        foreach ($examples as $example) {
            if (!is_array($example)) {
                throw new ContentException($context . ' : chaque exemple doit être un tableau');
            }
            $href = $this->text($example, 'href', $context, required: false);
            if ($href !== '' && preg_match('/^#[a-z0-9-]+$/', $href) !== 1) {
                throw new ContentException($context . ' : « href » doit être une ancre de la page, comme #produits');
            }
            $built[] = new ServiceExample($this->text($example, 'label', $context), $href);
        }

        return new Service($name, $this->prose($item, 'description', $context), $built);
    }

    /**
     * @return array<array-key, mixed>
     */
    private function load(string $name): array
    {
        $file = $this->contentDir . '/' . $name . '.php';
        if (!is_file($file)) {
            throw new ContentException('Fichier de contenu introuvable : ' . $name . '.php');
        }
        $data = require $file;
        if (!is_array($data)) {
            throw new ContentException($name . '.php doit renvoyer un tableau');
        }

        return $data;
    }

    /**
     * @return list<array<array-key, mixed>>
     */
    private function loadList(string $name): array
    {
        $data = $this->load($name);
        if ($data === [] || !array_is_list($data)) {
            throw new ContentException($name . '.php doit renvoyer une liste non vide');
        }
        $items = [];
        foreach ($data as $item) {
            if (!is_array($item)) {
                throw new ContentException($name . '.php : chaque entrée doit être un tableau');
            }
            $items[] = $item;
        }

        return $items;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function text(array $data, string $key, string $context, bool $required = true): string
    {
        $value = $data[$key] ?? ($required ? null : '');
        if (!is_string($value)) {
            throw new ContentException($context . ' : « ' . $key . ' » doit être un texte');
        }
        $value = trim($value);
        if ($required && $value === '') {
            throw new ContentException($context . ' : « ' . $key . ' » est vide');
        }

        return $value;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function prose(array $data, string $key, string $context, bool $required = true): string
    {
        return Typography::french($this->text($data, $key, $context, $required));
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function slug(array $data, string $context): string
    {
        $slug = $this->text($data, 'slug', $context);
        if (preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $slug) !== 1) {
            throw new ContentException($context . ' : « slug » ne contient que des minuscules, des chiffres et des tirets');
        }

        return $slug;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function httpsUrl(array $data, string $key, string $context, bool $required = true): string
    {
        $url = $this->text($data, $key, $context, $required);
        if ($url !== '' && (!str_starts_with($url, 'https://') || filter_var($url, FILTER_VALIDATE_URL) === false)) {
            throw new ContentException($context . ' : « ' . $key . ' » doit être une adresse https valide');
        }

        return $url;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function asset(array $data, string $key, string $context): string
    {
        $path = ltrim($this->text($data, $key, $context), '/');
        if (!is_file($this->publicDir . '/assets/' . $path)) {
            throw new ContentException($context . ' : fichier introuvable, assets/' . $path);
        }

        return $path;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function positiveInt(array $data, string $key, string $context): int
    {
        $value = $data[$key] ?? null;
        if (!is_int($value) || $value <= 0) {
            throw new ContentException($context . ' : « ' . $key . ' » doit être un entier positif');
        }

        return $value;
    }
}
