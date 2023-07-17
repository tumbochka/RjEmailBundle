<?php

namespace Rj\EmailBundle\Twig;

use Rj\EmailBundle\Entity\EmailTemplate;
use Rj\EmailBundle\Entity\EmailTemplateManager;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

class EmailTemplateLoader implements LoaderInterface
{
    private $manager;

    protected $template;

    protected $locale;

    protected $part;

    /**
     * {@inheritDoc}
     */
    public function exists($name)
    {
        try {
            list($name, $this->locale, $this->part) = $this->parse($name);
            $this->template = $this->getTemplate($name);

            return true;
        } catch (LoaderError $e) {
            return false;
        }
    }

    public function __construct(EmailTemplateManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Gets the source code of a template, given its name.
     *
     * @param string $name The name of the template to load
     *
     * @return Source The template source code
     *
     * @throws LoaderError When $name is not found
     */
    public function getSourceContext($name)
    {
        $source = $this->getTemplatePart($this->template, $this->locale, $this->part) ?: '';

        return new Source($source, $name);
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @param string $name The name of the template to load
     *
     * @return string The cache key
     *
     * @throws LoaderError When $name is not found
     */
    public function getCacheKey($fullName)
    {
        list($name, ) = $this->parse($fullName);

        $template = $this->getTemplate($name);

        return
            __CLASS__
            . '#' . $fullName
            // force reload even if Twig has autoReload to false
            . '#' . $template->getUpdatedAt()->getTimestamp();
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param string    $name The template name
     * @param int $time The last modification time of the cached template
     *
     * @return Boolean true if the template is fresh, false otherwise
     *
     * @throws LoaderError When $name is not found
     */
    public function isFresh($name, $time)
    {
        list($name, ) = $this->parse($name);

        $template = $this->getTemplate($name);

        return $template->getUpdatedAt()->getTimestamp() <= $time;
    }

    private function canHandle($name)
    {
        return 0 === strpos($name, 'email_template:');
    }

    private function parse($name)
    {
        if (!preg_match('#^email_template:([^:]+):([^:]+):([^:]+)$#', $name, $m)) {
            throw new LoaderError('invalid template name');
        }

        return array($m[1], $m[2], $m[3]);
    }

    private function getTemplate($name)
    {
        if (!$template = $this->manager->getTemplate($name)) {
            throw new LoaderError(sprintf("Unable to find email template %s", $name));
        }

        return $template;
    }

    private function getTemplateTranslation(EmailTemplate $template, $locale)
    {
        return $this->manager->getTemplateTranslation($template, $locale);
    }

    private function getTemplatePart(EmailTemplate $template, $locale, $part)
    {
        $translation = $this->getTemplateTranslation($template, $locale);
        if (!$translation) {
            throw new \Exception(sprintf('No translation %s for %s', $locale, $template->getName()));
        }

        switch ($part) {
        case 'subject':
                return '{% autoescape false %}' . $translation->getSubject() . '{% endautoescape %}';
            case 'body':
                return $translation->getBody();
            case 'bodyHtml':
                return $translation->getBodyHtml();
            default:
                throw new LoaderError(sprintf("Invalid template part %s", $part));
        }
    }
}
