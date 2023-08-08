<?php

namespace Rj\EmailBundle\Entity;

class EmailTemplateTranslationProxyProxy implements \ArrayAccess
{
    public function __construct(private EmailTemplate $emailTemplate)
    {
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->emailTemplate->translate($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    public function offsetExists(mixed $offset): bool
    {
        return true;
    }

    public function offsetUnset(mixed $offset): void
    {
    }
}

