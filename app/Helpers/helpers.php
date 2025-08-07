<?php

use Illuminate\Support\Facades\Log;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\Output\RenderedContentInterface;
use Illuminate\Support\Str;

if (!function_exists('convertMarkdownToHtml')) {
    /**
     * @throws CommonMarkException
     */
    function convertMarkdownToHtml($markdown): RenderedContentInterface {
        // Create an environment and add the CommonMark extension
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $converter = new MarkdownConverter($environment);

        return $converter->convert($markdown);
    }

    function toLiteralNewline(string $input): string {
        return str_replace(["\r\n", "\r", "\n"], '\n', $input);
    }
}
/**
 * @param string $type as 'error' or 'info'
 * @param string $message
 * @param array $context
 * @return void
 */
function LogThis(string $type, string $message, array $context = []): void {
    if (in_array(APP_ENV, ['local', 'dev', 'development', 'testing', 'staging']) || LOG_OK) {
        if ($type == 'error') {
            Log::error('TrinityAi: ' . $message, $context);
        } else {
            Log::info('TrinityAi: ' . $message, $context);
        }
    }
}

function booleanToString(bool $value): string {
    return $value ? "true" : "false";
}

if (!function_exists('slugify')) {
    /**
     * Convert a string into a URL-friendly slug.
     *
     * @param string $string The string to be slugified.
     * @param string $separator The separator to use in the slug (default is '-').
     * @return string The slugified string.
     */
    function slugify(string $string, string $separator = '-'): string {
        // Convert to lowercase
        $slug = strtolower($string);

        // Remove all non-alphanumeric characters except the separator
        $slug = preg_replace('/[^a-z0-9]+/i', $separator, $slug);

        // Trim leading and trailing separators
        $slug = trim($slug, $separator);

        return $slug;
    }
}

if (!function_exists('unslugify')) {
    function unslugify(string $slug): string {
        return Str::title(str_replace('-', ' ', $slug));
    }
}
