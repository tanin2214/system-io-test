<?php

use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer;
use PhpCsFixer\Fixer\Operator\NewExpressionParenthesesFixer;
use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths(
        [
            __DIR__ . '/src',
            __DIR__ . '/tests',
        ],
    );


    $ecsConfig->sets(
        [
            SetList::CLEAN_CODE,
            SetList::PSR_12,
            SetList::ARRAY,
            SetList::SPACES,
            SetList::NAMESPACES,
        ],
    );

    $ecsConfig->rule(StandaloneLineInMultilineArrayFixer::class);
    $ecsConfig->rule(LineLengthFixer::class);
    $ecsConfig->rule(DeclareStrictTypesFixer::class);
    $ecsConfig->rule(NewExpressionParenthesesFixer::class);

    $ecsConfig->ruleWithConfiguration(
        MultilineWhitespaceBeforeSemicolonsFixer::class,
        ['strategy' => 'new_line_for_chained_calls'],
    );
    $ecsConfig->ruleWithConfiguration(
        TrailingCommaInMultilineFixer::class,
        ['elements' => ['arguments', 'arrays', 'match', 'parameters']],
    );

    $ecsConfig->ruleWithConfiguration(
        DeclareEqualNormalizeFixer::class,
        [
            'space' => 'none',
        ],
    );

    $ecsConfig->ruleWithConfiguration(
        OrderedImportsFixer::class,
        [
            'imports_order' => [
                'class',
                'function',
                'const',
            ],
        ],
    );

    $ecsConfig->ruleWithConfiguration(
        BlankLineBeforeStatementFixer::class,
        [
            'statements' => [
                'continue',
                'declare',
                'return',
                'throw',
                'try',
            ],
        ],
    );
};
