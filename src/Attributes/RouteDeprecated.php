<?php

namespace Rakutentech\LaravelRequestDocs\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class RouteDeprecated extends RouteAttribute
{
    public function __construct(private string $comment = '')
    {

    }

    public function toMarkdown(): string
    {
        return '**Deprecated!**' . (strlen($this->comment > 0) ? ' ' . $this->comment : '');
    }
}
