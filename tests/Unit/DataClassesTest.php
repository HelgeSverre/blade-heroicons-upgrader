<?php

use HelgeSverre\BladeHeroiconsUpgrader\Data\Replacement;
use HelgeSverre\BladeHeroiconsUpgrader\Data\Result;

describe('Result class', function () {

    it('correctly reports when empty', function () {
        $result = new Result(
            new: 'new content',
            old: 'old content',
            replacements: [],
            adjustedReplacements: []
        );

        expect($result->isEmpty())->toBeTrue()
            ->and($result->isNotEmpty())->toBeFalse()
            ->and($result->count())->toBe(0);
    });

    it('correctly reports when not empty', function () {
        $replacement = new Replacement(
            oldIcon: 'heroicon-o-archive',
            newIcon: 'heroicon-o-archive-box',
            pattern: '/test/',
            line: 1,
            column: 0,
            filePath: '/test.blade.php'
        );

        $result = new Result(
            new: 'new content',
            old: 'old content',
            replacements: [$replacement],
            adjustedReplacements: [$replacement]
        );

        expect($result->isEmpty())->toBeFalse()
            ->and($result->isNotEmpty())->toBeTrue()
            ->and($result->count())->toBe(1);
    });

    it('counts multiple replacements correctly', function () {
        $replacements = [
            new Replacement('heroicon-o-archive', 'heroicon-o-archive-box', '/test/', 1, 0, '/test.blade.php'),
            new Replacement('heroicon-o-bell', 'heroicon-o-bell', '/test/', 2, 0, '/test.blade.php'),
            new Replacement('heroicon-o-cake', 'heroicon-o-cake', '/test/', 3, 0, '/test.blade.php'),
        ];

        $result = new Result(
            new: 'new content',
            old: 'old content',
            replacements: $replacements,
            adjustedReplacements: $replacements
        );

        expect($result->count())->toBe(3)
            ->and($result->isNotEmpty())->toBeTrue();
    });

    it('stores old and new content correctly', function () {
        $oldContent = 'This is the old content';
        $newContent = 'This is the new content';

        $result = new Result(
            new: $newContent,
            old: $oldContent,
            replacements: [],
            adjustedReplacements: []
        );

        expect($result->old)->toBe($oldContent)
            ->and($result->new)->toBe($newContent);
    });

    it('maintains separate replacement and adjusted replacement arrays', function () {
        $replacements = [
            new Replacement('heroicon-o-archive', 'heroicon-o-archive-box', '/test/', 1, 5, '/test.blade.php'),
        ];

        $adjustedReplacements = [
            new Replacement('heroicon-o-archive', 'heroicon-o-archive-box', '/test/', 1, 9, '/test.blade.php'),
        ];

        $result = new Result(
            new: 'new content',
            old: 'old content',
            replacements: $replacements,
            adjustedReplacements: $adjustedReplacements
        );

        expect($result->replacements)->toBe($replacements)
            ->and($result->adjustedReplacements)->toBe($adjustedReplacements)
            ->and($result->replacements[0]->column)->toBe(5)
            ->and($result->adjustedReplacements[0]->column)->toBe(9);
    });
});

describe('Replacement class', function () {

    it('stores all properties correctly', function () {
        $replacement = new Replacement(
            oldIcon: 'heroicon-o-archive',
            newIcon: 'heroicon-o-archive-box',
            pattern: '#test-pattern#',
            line: 42,
            column: 15,
            filePath: '/path/to/test.blade.php'
        );

        expect($replacement->oldIcon)->toBe('heroicon-o-archive')
            ->and($replacement->newIcon)->toBe('heroicon-o-archive-box')
            ->and($replacement->pattern)->toBe('#test-pattern#')
            ->and($replacement->line)->toBe(42)
            ->and($replacement->column)->toBe(15)
            ->and($replacement->filePath)->toBe('/path/to/test.blade.php');
    });

    it('handles null file path', function () {
        $replacement = new Replacement(
            oldIcon: 'heroicon-o-archive',
            newIcon: 'heroicon-o-archive-box',
            pattern: '#test#',
            line: 1,
            column: 0,
            filePath: null
        );

        expect($replacement->filePath)->toBeNull();
    });

    it('properties are readonly', function () {
        $replacement = new Replacement(
            oldIcon: 'heroicon-o-archive',
            newIcon: 'heroicon-o-archive-box',
            pattern: '#test#',
            line: 1,
            column: 0,
            filePath: '/test.blade.php'
        );

        // Attempting to modify readonly property should throw error
        expect(fn () => $replacement->oldIcon = 'different-icon')
            ->toThrow(Error::class);
    });

    it('handles zero line and column values', function () {
        $replacement = new Replacement(
            oldIcon: 'heroicon-o-archive',
            newIcon: 'heroicon-o-archive-box',
            pattern: '#test#',
            line: 0,
            column: 0,
            filePath: '/test.blade.php'
        );

        expect($replacement->line)->toBe(0)
            ->and($replacement->column)->toBe(0);
    });

    it('handles large line and column values', function () {
        $replacement = new Replacement(
            oldIcon: 'heroicon-o-archive',
            newIcon: 'heroicon-o-archive-box',
            pattern: '#test#',
            line: 999999,
            column: 999999,
            filePath: '/test.blade.php'
        );

        expect($replacement->line)->toBe(999999)
            ->and($replacement->column)->toBe(999999);
    });
});
