<?php

// Icons in Alpine.js and Livewire contexts
dataset('AlpineAndLivewire', [
    // In x-data
    [
        '<div x-data="{ icon: \'heroicon-o-old-icon-name\' }"></div>',
        '<div x-data="{ icon: \'heroicon-o-new-icon-name\' }"></div>',
    ],
    // In x-bind
    [
        '<div x-bind:data-icon="\'heroicon-o-old-icon-name\'"></div>',
        '<div x-bind:data-icon="\'heroicon-o-new-icon-name\'"></div>',
    ],
    // In x-on (Alpine event handler)
    [
        '<button x-on:click="icon = \'heroicon-o-old-icon-name\'"></button>',
        '<button x-on:click="icon = \'heroicon-o-new-icon-name\'"></button>',
    ],
    // In wire:click (Livewire)
    [
        '<button wire:click="setIcon(\'heroicon-o-old-icon-name\')"></button>',
        '<button wire:click="setIcon(\'heroicon-o-new-icon-name\')"></button>',
    ],
    // In wire:model
    [
        '<input wire:model="icon" value="heroicon-o-old-icon-name" />',
        '<input wire:model="icon" value="heroicon-o-new-icon-name" />',
    ],
    // In x-show condition with icon string
    [
        '<div x-show="icon === \'heroicon-o-old-icon-name\'"></div>',
        '<div x-show="icon === \'heroicon-o-new-icon-name\'"></div>',
    ],
    // In x-if
    [
        '<template x-if="icon === \'heroicon-o-old-icon-name\'"><div></div></template>',
        '<template x-if="icon === \'heroicon-o-new-icon-name\'"><div></div></template>',
    ],
    // In x-text
    [
        '<span x-text="\'heroicon-o-old-icon-name\'"></span>',
        '<span x-text="\'heroicon-o-new-icon-name\'"></span>',
    ],
    // In wire:loading with icon component
    [
        '<div wire:loading><x-heroicon-o-old-icon-name /></div>',
        '<div wire:loading><x-heroicon-o-new-icon-name /></div>',
    ],
    // In Livewire component attribute
    [
        '<livewire:component icon="heroicon-o-old-icon-name" />',
        '<livewire:component icon="heroicon-o-new-icon-name" />',
    ],
    // In @entangle (Livewire + Alpine)
    [
        '<div x-data="{ icon: @entangle(\'icon\').defer }" data-default="heroicon-o-old-icon-name"></div>',
        '<div x-data="{ icon: @entangle(\'icon\').defer }" data-default="heroicon-o-new-icon-name"></div>',
    ],
    // In x-init
    [
        '<div x-init="icon = \'heroicon-o-old-icon-name\'"></div>',
        '<div x-init="icon = \'heroicon-o-new-icon-name\'"></div>',
    ],
    // In wire:target with icon reference
    [
        '<div wire:target="update" wire:loading.class="heroicon-o-old-icon-name"></div>',
        '<div wire:target="update" wire:loading.class="heroicon-o-new-icon-name"></div>',
    ],
    // In x-ref with icon
    [
        '<div x-ref="icon" data-icon="heroicon-o-old-icon-name"></div>',
        '<div x-ref="icon" data-icon="heroicon-o-new-icon-name"></div>',
    ],
]);
