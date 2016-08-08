<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Form\Extension\Csrf\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class CsrfValidationListener implements EventSubscriberInterface
{
    /**
     * The name of the CSRF field.
     *
     * @var string
     */
    private $fieldName;

    /**
     * The generator for CSRF tokens.
     *
     * @var CsrfTokenManagerInterface
     */
    private $tokenManager;

    /**
     * A text mentioning the tokenId of the CSRF token.
     *
     * Validation of the token will only succeed if it was generated in the
     * same session and with the same tokenId.
     *
     * @var string
     */
    private $tokenId;

    /**
     * The message displayed in case of an error.
     *
     * @var string
     */
    private $errorMessage;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var null|string
     */
    private $translationDomain;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * CsrfValidationListener constructor.
     *
     * @param $fieldName
     * @param CsrfTokenManagerInterface $tokenManager
     * @param $tokenId
     * @param $errorMessage
     * @param TranslatorInterface|null $translator
     * @param null                     $translationDomain
     */
    public function __construct($fieldName, CsrfTokenManagerInterface $tokenManager, $tokenId, $errorMessage, TranslatorInterface $translator = null, $translationDomain = null)
    {
        $this->fieldName = $fieldName;
        $this->tokenManager = $tokenManager;
        $this->tokenId = $tokenId;
        $this->errorMessage = $errorMessage;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();

        if ($form->isRoot() && $form->getConfig()->getOption('compound')) {
            $data = $event->getData();

            if (!isset($data[$this->fieldName]) || !$this->tokenManager->isTokenValid(new CsrfToken($this->tokenId, $data[$this->fieldName]))) {
                $errorMessage = $this->errorMessage;

                if (null !== $this->translator) {
                    $errorMessage = $this->translator->trans($errorMessage, array(), $this->translationDomain);
                }

                $form->addError(new FormError($errorMessage));
            }

            if (is_array($data)) {
                unset($data[$this->fieldName]);
                $event->setData($data);
            }
        }
    }
}
