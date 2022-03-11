<?php

namespace Rakutentech\LaravelRequestDocs\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class RouteDescription extends RouteAttribute
{

    public function __construct(public string $title, public string $description)
    {

    }

    public function toMarkdown(): string
    {
        return "# " . $this->title . "\n" . $this->description;
    }

}
