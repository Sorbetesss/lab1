<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation;

/**
 * @author Abdellatif Ait boudad <a.aitboudad@gmail.com>
 */
class DataCollectorTranslator implements TranslatorInterface, TranslatorBagInterface, FallbackLocaleAwareInterface
{
    const MESSAGE_DEFINED = 0;
    const MESSAGE_MISSING = 1;
    const MESSAGE_EQUALS_FALLBACK = 2;

    /**
     * @var TranslatorInterface|TranslatorBagInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $messages = array();

    /**
     * @var FallbackLocaleAwareInterface
     */
    private $fallbackLocaleAware;

    /**
     * @param TranslatorInterface $translator The translator must implement TranslatorBagInterface
     */
    public function __construct(TranslatorInterface $translator)
    {
        if ($translator instanceof FallbackLocaleAwareInterface) {
            $this->fallbackLocaleAware = $translator;
        } else if ($translator instanceof TranslatorBagInterface) {
            @trigger_error(sprintf('The Translator "%" should implement \Symfony\Component\Translation\FallbackLocaleAwareInterface instead of (or in addition to) \Symfony\Component\Translation\TranslatorBagInterface.', get_class($translator)), E_USER_DEPRECATED);
            $this->fallbackLocaleAware = new TranslatorBagToFallbackLocaleAwareAdapter($translator);
        } else {
            throw new \InvalidArgumentException(sprintf('The Translator "%s" implements neither \Symfony\Component\Translation\FallbackLocaleAwareInterface nor the deprecated \Symfony\Component\Translation\TranslatorBagInterface.', get_class($translator)));
        }

        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        $trans = $this->translator->trans($id, $parameters, $domain, $locale);
        $this->collectMessage($locale, $domain, $id, $trans, $parameters);

        return $trans;
    }

    /**
     * {@inheritdoc}
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null)
    {
        $trans = $this->translator->transChoice($id, $number, $parameters, $domain, $locale);
        $this->collectMessage($locale, $domain, $id, $trans, $parameters, $number);

        return $trans;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveLocale($id, $domain = null, $locale = null)
    {
        return $this->fallbackLocaleAware->resolveLocale($id, $domain, $locale);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function setLocale($locale)
    {
        $this->translator->setLocale($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->translator->getLocale();
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated TranslatorBagInterface implementation will be removed in 3.0.
     */
    public function getCatalogue($locale = null)
    {
        if (!$this->translator instanceof TranslatorBagInterface) {
            throw new \RuntimeException(sprintf('You called the deprecated \Symfony\Component\Translation\DataCollectorTranslator::getCatalogue() method, but the Translator provided to the \Symfony\Component\Translation\DataCollectorTranslator constructor does not implement TranslatorBagInterface. (Hint: The class is %s.)',  get_class($this->translator)));
        }

        return $this->translator->getCatalogue($locale);
    }

    /**
     * Passes through all unknown calls onto the translator object.
     */
    public function __call($method, $args)
    {
        return call_user_func_array(array($this->translator, $method), $args);
    }

    /**
     * @return array
     */
    public function getCollectedMessages()
    {
        return $this->messages;
    }

    /**
     * @param string|null $locale
     * @param string|null $domain
     * @param string      $id
     * @param string      $translation
     * @param array|null  $parameters
     * @param int|null    $number
     */
    private function collectMessage($locale, $domain, $id, $translation, $parameters = array(), $number = null)
    {
        if (null === $domain) {
            $domain = 'messages';
        }

        if (null === $locale) {
            $locale = $this->translator->getLocale();
        }

        $id = (string) $id;
        $resolvedLocale = $this->translator->resolveLocale($id, $domain, $locale);

        switch (true) {
            case $resolvedLocale === $locale:
                $state = self::MESSAGE_DEFINED;
                break;

            case $resolvedLocale === null:
                $state = self::MESSAGE_MISSING;
                break;

            default:
                $state = self::MESSAGE_EQUALS_FALLBACK;
                $locale = $resolvedLocale;
        }

        $this->messages[] = array(
            'locale' => $locale,
            'domain' => $domain,
            'id' => $id,
            'translation' => $translation,
            'parameters' => $parameters,
            'transChoiceNumber' => $number,
            'state' => $state,
        );
    }
}
