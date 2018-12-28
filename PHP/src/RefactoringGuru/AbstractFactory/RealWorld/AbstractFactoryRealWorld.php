<?php

namespace RefactoringGuru\AbstractFactory\RealWorld;

/**
 * Abstract Factory Design Pattern
 *
 * Intent: Provide an interface for creating families of related or dependent
 * objects without specifying their concrete classes.
 *
 * Example: In this example, the Abstract Factory pattern provides an
 * infrastructure for creating various types of templates for different elements
 * of a web page.
 *
 * A web application can support different rendering engines at the same time,
 * but only if its classes are independent of the concrete classes of rendering
 * engines. Hence, the application's objects must communicate with template
 * objects only via their abstract interfaces. Your code should not create the
 * template objects directly, but delegate their creation to special factory
 * objects. Finally, your code should not depend on the factory objects either
 * but, instead, should work with them via the abstract factory interface.
 *
 * As a result, you will be able to provide the app with the factory object that
 * corresponds to one of the rendering engines. All templates, created in the
 * app, will be created by that factory and their type will match the type of
 * the factory. If you decide to change the rendering engine, you'll be able to
 * pass a new factory to the client code, without breaking any existing code.
 */

/**
 * The Abstract Factory interface declares creation methods for each distinct
 * product type.
 */
interface TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate;

    public function createPageTemplate(): PageTemplate;
}

/**
 * Each Concrete Factory corresponds to a specific variant (or family) of
 * products.
 *
 * This Concrete Factory creates Twig templates.
 */
class TwigTemplateFactory implements TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate
    {
        return new TwigTitleTemplate;
    }

    public function createPageTemplate(): PageTemplate
    {
        return new TwigPageTemplate($this->createTitleTemplate());
    }
}

/**
 * And this Concrete Factory creates PHPTemplate templates.
 */
class PHPTemplateFactory implements TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate
    {
        return new PHPTemplateTitleTemplate;
    }

    public function createPageTemplate(): PageTemplate
    {
        return new PHPTemplatePageTemplate($this->createTitleTemplate());
    }
}

/**
 * Each distinct product type should have a separate interface. All variants of
 * the product must follow the same interface.
 *
 * For instance, this Abstract Product interface describes the behavior of page
 * title templates.
 */
interface TitleTemplate
{
    public function getTemplateString(): string;
}

/**
 * This Concrete Product provides Twig page title templates.
 */
class TwigTitleTemplate implements TitleTemplate
{
    public function getTemplateString(): string
    {
        return "<h1>{{ title }}</h1>";
    }
}

/**
 * And this Concrete Product provides PHPTemplate page title templates.
 */
class PHPTemplateTitleTemplate implements TitleTemplate
{
    public function getTemplateString(): string
    {
        return "<h1><?= $title; ?></h1>";
    }
}

/**
 * This is another Abstract Product type, which describes whole page templates.
 */
interface PageTemplate
{
    public function getTemplateString(): string;
}

/**
 * The page template uses the title sub-template, so we have to provide the way
 * to set it in the sub-template object. The abstract factory will link the page
 * template with a title template of the same variant.
 */
abstract class BasePageTemplate implements PageTemplate
{
    protected $titleTemplate;

    public function __construct(TitleTemplate $titleTemplate)
    {
        $this->titleTemplate = $titleTemplate;
    }
}

/**
 * The Twig variant of the whole page templates.
 */
class TwigPageTemplate extends BasePageTemplate
{
    public function getTemplateString(): string
    {
        $renderedTitle = $this->titleTemplate->getTemplateString();
        
        return <<<HTML
        <div class="page">
            $renderedTitle
            <article class="content">{{ content }}</article>
        </div>
        HTML;
    }
}

/**
 * The PHPTemplate variant of the whole page templates.
 */
class PHPTemplatePageTemplate extends BasePageTemplate
{
    public function getTemplateString(): string
    {
        $renderedTitle = $this->titleTemplate->getTemplateString();
        
        return <<<HTML
        <div class="page">
            $renderedTitle
            <article class="content"><?= $content; ?></article>
        </div>
        HTML;
    }
}

/**
 * The client code. Note that it accepts the Abstract Factory class as the
 * parameter, which allows the client to work with any concrete factory type.
 */
function templateRenderer(TemplateFactory $factory)
{
    $pageTemplate = $factory->createPageTemplate();

    echo $pageTemplate->getTemplateString();

    // Here's how would you use the template further in real life:

    // Twig::render($pageTemplate->getTemplateString(), [
    //     'title' => $page->title,
    //     'content' => $page->content, ]);
}

/**
 * Now, in other parts of the app, the client code can accept factory objects of
 * any type.
 */
echo "Testing rendering with the Twig factory:\n";
templateRenderer(new TwigTemplateFactory);
echo "\n\n";

echo "Testing rendering with the PHPTemplate factory:\n";
templateRenderer(new PHPTemplateFactory);
