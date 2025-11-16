<?php

use Necro304\CrudInertiaShadcn\Support\StubRenderer;

describe('StubRenderer', function () {
    test('replaces single token in stub template', function () {
        $renderer = new StubRenderer;
        $template = 'Hello {{ NAME }}!';
        $result = $renderer->render($template, ['NAME' => 'World']);

        expect($result)->toBe('Hello World!');
    });

    test('replaces multiple tokens in stub template', function () {
        $renderer = new StubRenderer;
        $template = 'class {{ CLASS }} extends {{ PARENT }}';
        $result = $renderer->render($template, [
            'CLASS' => 'Product',
            'PARENT' => 'Model',
        ]);

        expect($result)->toBe('class Product extends Model');
    });

    test('replaces same token multiple times', function () {
        $renderer = new StubRenderer;
        $template = '{{ NAME }} is {{ NAME }}';
        $result = $renderer->render($template, ['NAME' => 'test']);

        expect($result)->toBe('test is test');
    });

    test('preserves template when no tokens provided', function () {
        $renderer = new StubRenderer;
        $template = 'class Product extends Model';
        $result = $renderer->render($template, []);

        expect($result)->toBe($template);
    });

    test('ignores tokens not in template', function () {
        $renderer = new StubRenderer;
        $template = 'Hello {{ NAME }}';
        $result = $renderer->render($template, [
            'NAME' => 'World',
            'UNUSED' => 'Value',
        ]);

        expect($result)->toBe('Hello World');
    });

    test('handles empty string tokens', function () {
        $renderer = new StubRenderer;
        $template = 'class {{ CLASS }}';
        $result = $renderer->render($template, ['CLASS' => '']);

        expect($result)->toBe('class ');
    });

    test('handles complex multi-line templates', function () {
        $renderer = new StubRenderer;
        $template = <<<'STUB'
<?php

namespace {{ NAMESPACE }};

class {{ CLASS }}
{
    protected $table = '{{ TABLE }}';
}
STUB;

        $result = $renderer->render($template, [
            'NAMESPACE' => 'App\Models',
            'CLASS' => 'Product',
            'TABLE' => 'products',
        ]);

        expect($result)->toContain('namespace App\Models;');
        expect($result)->toContain('class Product');
        expect($result)->toContain("protected \$table = 'products';");
    });

    test('preserves whitespace and indentation', function () {
        $renderer = new StubRenderer;
        $template = "    public function {{ METHOD }}()\n    {\n        // code\n    }";
        $result = $renderer->render($template, ['METHOD' => 'store']);

        expect($result)->toContain('    public function store()');
        expect($result)->toContain('        // code');
    });

    test('handles tokens with special characters in values', function () {
        $renderer = new StubRenderer;
        $template = 'protected $table = \'{{ TABLE }}\';';
        $result = $renderer->render($template, ['TABLE' => 'users_test']);

        expect($result)->toBe('protected $table = \'users_test\';');
    });

    test('loads stub from file and renders', function () {
        $renderer = new StubRenderer;
        $stubPath = __DIR__ . '/../fixtures/test.stub';

        // Create a temporary test stub
        if (! is_dir(dirname($stubPath))) {
            mkdir(dirname($stubPath), 0755, true);
        }
        file_put_contents($stubPath, 'class {{ CLASS }} {}');

        $result = $renderer->renderFromFile($stubPath, ['CLASS' => 'TestClass']);

        expect($result)->toBe('class TestClass {}');

        // Cleanup
        unlink($stubPath);
        if (is_dir(dirname($stubPath))) {
            rmdir(dirname($stubPath));
        }
    });
});
