<?php

namespace Rakutentech\LaravelRequestDocs\Attributes;

abstract class RouteAttribute
{

    abstract public function toMarkdown(): string;

}
