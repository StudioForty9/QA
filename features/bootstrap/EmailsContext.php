<?php

use Alex\MailCatcher\Behat\MailCatcherContext;
use Symfony\Component\DomCrawler\Crawler;
use Behat\Behat\Context\SnippetAcceptingContext;

/**
 * Emails context.
 */
class EmailsContext extends MailCatcherContext implements SnippetAcceptingContext
{
    /**
     * @Then /^I should not see "([^"]+)" in mail$/
     */
    public function notSeeInMail($text)
    {
        $message = $this->currentMessage;

        if (!$message->isMultipart()) {
            $content = $message->getContent();
        } elseif ($message->hasPart('text/html')) {
            $content = $this->getCrawler($message)->text();
        } elseif ($message->hasPart('text/plain')) {
            $content = $message->getPart('text/plain')->getContent();
        } else {
            throw new \RuntimeException(sprintf('Unable to read mail'));
        }

        if (false !== strpos($content, $text)) {
            throw new \InvalidArgumentException(sprintf("Text \"%s\" was found in current message:\n%s", $text,
                $message->getContent()));
        }
    }

    /**
     * @param Message $message
     *
     * @return Crawler
     */
    protected function getCrawler(Message $message)
    {
        if (!class_exists('Symfony\Component\DomCrawler\Crawler')) {
            throw new \RuntimeException('Can\'t crawl HTML: Symfony DomCrawler component is missing from autoloading.');
        }

        return new Crawler($message->getPart('text/html')->getContent());
    }
}
