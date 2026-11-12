<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * safe_markdown() turns text typed in the dashboard into HTML that is safe to
 * print with {!! !!}: formatting works, raw HTML and javascript: links do not.
 */
class SafeMarkdownTest extends TestCase
{
    public function test_formatting_is_converted(): void
    {
        $html = safe_markdown("**Rakiza** is *solid*.\n\n- one\n- two");

        $this->assertStringContainsString('<strong>Rakiza</strong>', $html);
        $this->assertStringContainsString('<em>solid</em>', $html);
        $this->assertStringContainsString('<li>one</li>', $html);
    }

    public function test_raw_html_is_removed(): void
    {
        $html = safe_markdown("Hello <script>alert('x')</script> <img src=x onerror=alert(1)>");

        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringNotContainsString('onerror', $html);
    }

    public function test_javascript_links_are_blocked(): void
    {
        $html = safe_markdown('[click](javascript:alert(1)) and [site](https://rakiza.example)');

        $this->assertStringNotContainsString('javascript:', $html);
        $this->assertStringContainsString('href="https://rakiza.example"', $html);
    }

    public function test_empty_text_gives_empty_html(): void
    {
        $this->assertSame('', trim(safe_markdown(null)));
    }
}
